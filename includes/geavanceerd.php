<?php

const GEAVANCEERD_LIMIT = 300;

function categorieen(PDO $pdo): array
{
    return $pdo->query("SELECT DISTINCT Groepering FROM jos_BIEB_boeken WHERE Groepering <> '' ORDER BY Groepering")
        ->fetchAll(PDO::FETCH_COLUMN);
}

function geavanceerd_zoek_boeken(PDO $pdo, array $f): array
{
    $where = ["Duplicaat <> 'J'"];
    $params = [];

    if ($f['titel'] !== '') {
        $w = "%{$f['titel']}%";
        $where[] = '(Titel LIKE ? OR Sub_titel LIKE ?)';
        array_push($params, $w, $w);
    }
    if ($f['categorie'] !== '') {
        $where[] = 'Groepering = ?';
        $params[] = $f['categorie'];
    }
    if ($f['inhoud'] !== '') {
        $w = "%{$f['inhoud']}%";
        $where[] = '(Omschrijving LIKE ? OR Steekwoorden LIKE ?)';
        array_push($params, $w, $w);
    }
    if ($f['schrijver'] !== '') {
        $where[] = 'Auteur LIKE ?';
        $params[] = "%{$f['schrijver']}%";
    }

    if (count($where) === 1) {
        return [];
    }

    $kolommen = "Boek_id, Titel, Sub_titel, Auteur, Groepering, Opslag, Taal, ISBN13,
                 Serie_naam, Volgnr, Naw_id, Afbeelding";
    $sql = "SELECT $kolommen FROM jos_BIEB_boeken WHERE " . implode(' AND ', $where) .
           " ORDER BY Serie_naam, Volgnr, Auteur, Titel LIMIT " . GEAVANCEERD_LIMIT;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}
