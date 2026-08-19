<?php

const GEAVANCEERD_LIMIT = 300;

function categorieen(PDO $pdo): array
{
    return $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
}

function geavanceerd_zoek_boeken(PDO $pdo, array $f): array
{
    $where = ['b.visible = 1', 'b.is_duplicate = 0'];
    $params = [];

    if ($f['titel'] !== '') {
        $w = "%{$f['titel']}%";
        $where[] = '(b.title LIKE ? OR b.subtitle LIKE ?)';
        array_push($params, $w, $w);
    }
    if ($f['categorie'] !== '') {
        $where[] = 'b.category_id = ?';
        $params[] = (int) $f['categorie'];
    }
    if ($f['inhoud'] !== '') {
        $w = "%{$f['inhoud']}%";
        $where[] = '(b.description LIKE ? OR b.keywords LIKE ? OR b.grouping LIKE ?)';
        array_push($params, $w, $w, $w);
    }
    if ($f['schrijver'] !== '') {
        $where[] = 'b.author LIKE ?';
        $params[] = "%{$f['schrijver']}%";
    }

    if (count($where) === 2) {
        return [];
    }

    $sql = "SELECT " . LIJST_KOLOMMEN . "
            FROM books b " . LIJST_JOINS . "
            WHERE " . implode(' AND ', $where) . "
            ORDER BY b.series_name, b.series_number, b.author, b.title
            LIMIT " . GEAVANCEERD_LIMIT;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}
