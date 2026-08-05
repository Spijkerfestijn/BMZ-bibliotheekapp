# bibliotheekapp

Boekencatalogus van de bibliotheek van Bevrijdingsmuseum Zeeland.

## Installatie

1. Kopieer `config.example.php` naar `config.php` en vul de database-gegevens in
   (zelfde database/tabel als de oude site: database `bibliotheek`, tabel `jos_BIEB_boeken`).
2. Zet de map op een PHP 8-server met toegang tot die MySQL-database.

## Structuur

- `index.php` — zoekpagina
- `boek.php` — detailpagina van een boek
- `includes/` — database-, zoek- en helperfuncties, header/footer
- `css/` — stylesheet
- `oud/` — oude legacy-versie van de site (niet in git, bevat hardcoded db-credentials)
