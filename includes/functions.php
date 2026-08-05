<?php

function soort_omschrijving(?string $soort): string
{
    return match ($soort) {
        'A' => 'Artikel',
        'B' => '(auto)Biografie',
        'D' => 'Document',
        'H' => 'Historie',
        'F' => "Foto's/Afbeeldingen",
        'R' => 'Research',
        'S' => 'Divers',
        'T' => 'Technisch',
        'V' => 'Verhaal',
        default => 'Onbekend',
    };
}

function uitgave_omschrijving(?string $uitgave): string
{
    return match ($uitgave) {
        'P' => 'Paperback',
        'H', 'C' => 'Hardcover',
        'T' => 'Tijdschrift',
        'A' => 'Artikel',
        'M' => 'Manuscript',
        'K' => 'Krant',
        'B' => 'Brief/Dagboek',
        default => 'Anders',
    };
}

function opslag_locatie(?string $opslag): string
{
    $opslag = trim((string) $opslag);
    if ($opslag === '') {
        return 'Onbekend';
    }

    $omschrijving = 'Rij: ' . $opslag[0] . ' Kast: ' . ($opslag[1] ?? '?') . ' Bord: ' . ($opslag[2] ?? '?');
    if (strlen($opslag) > 3) {
        $omschrijving .= ' Lemma: ' . $opslag[3];
    }

    return $omschrijving;
}

function taal_vlag_url(?string $taal): string
{
    $taal = strtolower(trim((string) $taal));

    return 'https://beheerbieb.bevrijdingsmuseumzeeland.nl/components/com_hvd_bieb/images/vlag/' . rawurlencode($taal) . '.png';
}

function kaft_afbeelding_url(?string $bestand): ?string
{
    $bestand = trim((string) $bestand);
    if ($bestand === '') {
        return null;
    }

    return 'https://beheerbieb.bevrijdingsmuseumzeeland.nl/images/stories/hvd_bieb/kaftfoto/' . rawurlencode($bestand);
}

function isbn_zoek_url(?string $isbn, string $titel, string $auteur): string
{
    $isbn = trim((string) $isbn);
    if ($isbn !== '') {
        return 'https://www.worldcat.org/search?q=' . urlencode($isbn) . '&qt=owc_search';
    }

    return 'https://www.worldcat.org/search?q=' . urlencode(trim($titel . ' ' . $auteur)) . '&qt=owc_search';
}

function uitgeleend(array $boek): bool
{
    return (int) ($boek['Naw_id'] ?? 0) > 1;
}
