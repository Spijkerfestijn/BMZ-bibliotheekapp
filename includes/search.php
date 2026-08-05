<?php

const ZOEK_LIMIT = 200;

function zoek_boeken(PDO $pdo, string $tekst): array
{
    $kolommen = "Boek_id, Titel, Sub_titel, Auteur, Groepering, Opslag, Taal, ISBN13,
                 Serie_naam, Volgnr, Naw_id, Afbeelding";
    $w = "%$tekst%";

    $sql = "SELECT $kolommen FROM jos_BIEB_boeken
            WHERE Duplicaat <> 'J' AND (
                Auteur LIKE ? OR Omschrijving LIKE ? OR Opslag LIKE ? OR Titel LIKE ?
                OR Sub_titel LIKE ? OR Groepering LIKE ? OR Steekwoorden LIKE ?
            )
            ORDER BY Serie_naam, Volgnr, Auteur, Titel
            LIMIT " . ZOEK_LIMIT;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$w, $w, $w, $w, $w, $w, $w]);

    return $stmt->fetchAll();
}

function haal_boek(PDO $pdo, int $boekId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM jos_BIEB_boeken WHERE Boek_id = ? LIMIT 1");
    $stmt->execute([$boekId]);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function andere_exemplaren(PDO $pdo, array $boek): array
{
    $huidigId = (int) $boek['Boek_id'];
    $eigenBasecode = (int) ($boek['Basecode'] ?? 0);
    // Een duplicaat wijst met zijn Basecode terug naar het Boek_id van het hoofdexemplaar.
    // Heeft dit boek zelf geen Basecode, dan is het (mogelijk) het hoofdexemplaar.
    $hoofdId = $eigenBasecode > 0 ? $eigenBasecode : $huidigId;

    // Duplicaat = 'J' betekent alleen dat een kopie verborgen wordt in de gewone
    // zoekresultaten, niet dat het geen exemplaar is - dus hier bewust niet filteren.
    $stmt = $pdo->prepare(
        "SELECT Boek_id, Opslag, Duplicaat FROM jos_BIEB_boeken
         WHERE Basecode = ? AND Boek_id <> ?
         ORDER BY Opslag"
    );
    $stmt->execute([$hoofdId, $huidigId]);
    $exemplaren = $stmt->fetchAll();

    if ($hoofdId !== $huidigId) {
        $hoofdStmt = $pdo->prepare(
            "SELECT Boek_id, Opslag, Duplicaat FROM jos_BIEB_boeken WHERE Boek_id = ? LIMIT 1"
        );
        $hoofdStmt->execute([$hoofdId]);
        $hoofdexemplaar = $hoofdStmt->fetch();
        if ($hoofdexemplaar !== false) {
            array_unshift($exemplaren, $hoofdexemplaar);
        }
    }

    return $exemplaren;
}
