<?php

const ZOEK_LIMIT = 200;

const LIJST_KOLOMMEN = "b.id, b.title, b.subtitle, b.author, b.grouping, b.series_name, b.series_number,
             b.isbn13, b.cover_image, b.borrower_contact_id,
             sl.code AS opslag_code, sl.name AS opslag_naam,
             l.code AS taal_code";

const LIJST_JOINS = "LEFT JOIN storage_locations sl ON sl.id = b.storage_location_id
             LEFT JOIN languages l ON l.id = b.language_id";

function zoek_boeken(PDO $pdo, string $tekst): array
{
    $w = "%$tekst%";

    $sql = "SELECT " . LIJST_KOLOMMEN . "
            FROM books b " . LIJST_JOINS . "
            WHERE b.visible = 1 AND b.is_duplicate = 0 AND (
                b.author LIKE ? OR b.description LIKE ? OR b.title LIKE ? OR b.subtitle LIKE ?
                OR b.grouping LIKE ? OR b.keywords LIKE ? OR sl.name LIKE ? OR sl.code LIKE ?
            )
            ORDER BY b.series_name, b.series_number, b.author, b.title
            LIMIT " . ZOEK_LIMIT;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$w, $w, $w, $w, $w, $w, $w, $w]);

    return $stmt->fetchAll();
}

function haal_boek(PDO $pdo, int $id): ?array
{
    $sql = "SELECT b.*,
            sl.code AS opslag_code, sl.name AS opslag_naam,
            l.code AS taal_code, l.name AS taal_naam,
            bk.name AS soort_naam,
            ft.name AS uitgave_naam,
            cond.name AS staat_naam,
            cat.name AS categorie_naam
            FROM books b
            LEFT JOIN storage_locations sl ON sl.id = b.storage_location_id
            LEFT JOIN languages l ON l.id = b.language_id
            LEFT JOIN book_kinds bk ON bk.id = b.kind_id
            LEFT JOIN format_types ft ON ft.id = b.format_id
            LEFT JOIN conditions cond ON cond.id = b.condition_id
            LEFT JOIN categories cat ON cat.id = b.category_id
            WHERE b.id = ?
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function andere_exemplaren(PDO $pdo, array $boek): array
{
    $huidigId = (int) $boek['id'];
    $eigenBasecode = (int) ($boek['basecode'] ?? 0);
    // Een duplicaat wijst met zijn basecode terug naar het id van het hoofdexemplaar.
    // Heeft dit boek zelf geen basecode, dan is het (mogelijk) het hoofdexemplaar.
    $hoofdId = $eigenBasecode > 0 ? $eigenBasecode : $huidigId;

    $stmt = $pdo->prepare(
        "SELECT b.id, sl.code AS opslag_code, sl.name AS opslag_naam
         FROM books b LEFT JOIN storage_locations sl ON sl.id = b.storage_location_id
         WHERE b.basecode = ? AND b.id <> ? AND b.visible = 1
         ORDER BY sl.code"
    );
    $stmt->execute([$hoofdId, $huidigId]);
    $exemplaren = $stmt->fetchAll();

    if ($hoofdId !== $huidigId) {
        $hoofdStmt = $pdo->prepare(
            "SELECT b.id, sl.code AS opslag_code, sl.name AS opslag_naam
             FROM books b LEFT JOIN storage_locations sl ON sl.id = b.storage_location_id
             WHERE b.id = ? AND b.visible = 1
             LIMIT 1"
        );
        $hoofdStmt->execute([$hoofdId]);
        $hoofdexemplaar = $hoofdStmt->fetch();
        if ($hoofdexemplaar !== false) {
            array_unshift($exemplaren, $hoofdexemplaar);
        }
    }

    return $exemplaren;
}
