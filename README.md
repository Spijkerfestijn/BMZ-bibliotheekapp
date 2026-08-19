# bibliotheekapp

Boekencatalogus van de bibliotheek van Bevrijdingsmuseum Zeeland.

## Installatie

1. Kopieer `config.example.php` naar `config.php` en vul de database-gegevens in
   (database `bibliotheek`, genormaliseerd schema met o.a. de tabellen `books`,
   `categories`, `storage_locations`, `languages`, `book_kinds`, `format_types`,
   `conditions` en `contacts`).
2. Zet de map op een PHP 8-server met toegang tot die MySQL-database.

Lokaal ontwikkelen/testen kan met `start.bat` (gebruikt XAMPP: MySQL + de
ingebouwde PHP-server op http://localhost:8090).

## Structuur

- `index.php` — zoekpagina
- `boek.php` — detailpagina van een boek
- `geavanceerd.php` — geavanceerd zoeken (titel, categorie, inhoud, schrijver)
- `beheer.php` — doorverwijzing voor de oude, vervallen beheer-link
- `includes/` — database-, zoek- en helperfuncties, header/footer
- `css/` — stylesheet
- `oud/` — oude legacy-versie van de site (niet in git, bevat hardcoded db-credentials
  en draaide op het oude schema met tabel `jos_BIEB_boeken`)
