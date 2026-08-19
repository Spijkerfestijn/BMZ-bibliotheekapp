<?php

function taal_vlag_url(?string $taalCode): string
{
    $taalCode = strtolower(trim((string) $taalCode));

    return 'https://beheerbieb.bevrijdingsmuseumzeeland.nl/components/com_hvd_bieb/images/vlag/' . rawurlencode($taalCode) . '.png';
}

function kaft_afbeelding_url(int $boekId): string
{
    return 'https://beheerbieb.bevrijdingsmuseumzeeland.nl/images/stories/hvd_bieb/kaftfoto/img-' . $boekId . '-01.jpg';
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
    return !empty($boek['borrower_contact_id']);
}

function veilige_terug_url(string $raw, string $default = '/index.php'): string
{
    if ($raw === '' || $raw[0] !== '/' || str_starts_with($raw, '//') || str_contains($raw, '://')) {
        return $default;
    }

    return $raw;
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

function splits_auteurs(?string $auteur): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\s*(?:;|,|&|\ben\b)\s*/i', (string) $auteur))));
}
