<?php

function soort_omschrijving(?string $soort): string
{
    $labels = [
        'A' => 'Artikel',
        'B' => '(auto)Biografie',
        'D' => 'Document',
        'H' => 'Historie',
        'F' => "Foto's/Afbeeldingen",
        'R' => 'Research',
        'S' => 'Divers',
        'T' => 'Technisch',
        'V' => 'Verhaal',
    ];

    return $labels[$soort] ?? 'Onbekend';
}

function uitgave_omschrijving(?string $uitgave): string
{
    $labels = [
        'P' => 'Paperback',
        'H' => 'Hardcover',
        'C' => 'Hardcover',
        'T' => 'Tijdschrift',
        'A' => 'Artikel',
        'M' => 'Manuscript',
        'K' => 'Krant',
        'B' => 'Brief/Dagboek',
    ];

    return $labels[$uitgave] ?? 'Anders';
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

function boekwinkeltjes_zoek_url(string $titel, string $auteur): string
{
    return 'https://www.boekwinkeltjes.nl/s/?q=' . urlencode(trim($titel . ' ' . $auteur)) . '&t=1&n=1';
}

function uitgeleend(array $boek): bool
{
    return (int) ($boek['Naw_id'] ?? 0) > 1;
}

function schoon_omschrijving(?string $ruw): string
{
    $tekst = (string) $ruw;
    $tekst = preg_replace('/<\s*(p|div|br)[^>]*>/i', "\n", $tekst) ?? $tekst;
    $tekst = strip_tags($tekst);
    $tekst = html_entity_decode($tekst, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $tekst = preg_replace('/\n{3,}/', "\n\n", trim($tekst)) ?? $tekst;

    return trim($tekst);
}
