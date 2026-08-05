<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/search.php';

$boekId = (int) ($_GET['id'] ?? 0);
$zoekstr = trim($_GET['q'] ?? '');
$terugUrl = '/index.php' . ($zoekstr !== '' ? '?q=' . urlencode($zoekstr) : '');

$pdo = db_connect();
$boek = $boekId > 0 ? haal_boek($pdo, $boekId) : null;

$paginatitel = $boek ? $boek['Titel'] : 'Boek niet gevonden';
require __DIR__ . '/includes/header.php';
?>

<?php if ($boek === null): ?>
    <p class="hint">Dit boek kon niet worden gevonden.</p>
    <div class="knoppen">
        <a class="btn btn-terug" href="<?= htmlspecialchars($terugUrl) ?>">&larr; Terug naar zoekresultaten</a>
    </div>
<?php else: ?>
    <article>
        <?php if (!empty($boek['Serie_naam'])): ?>
            <p class="serie"><?= htmlspecialchars($boek['Serie_naam']) ?> (<?= htmlspecialchars((string) $boek['Volgnr']) ?>)</p>
        <?php endif; ?>
        <h1><?= htmlspecialchars($boek['Titel']) ?></h1>
        <?php if (!empty($boek['Sub_titel'])): ?>
            <p class="subtitel"><?= htmlspecialchars($boek['Sub_titel']) ?></p>
        <?php endif; ?>

        <?php $kaftUrl = kaft_afbeelding_url($boek['Afbeelding'] ?? null); ?>
        <?php if ($kaftUrl !== null): ?>
            <img class="kaft" src="<?= htmlspecialchars($kaftUrl) ?>" alt="Omslag van <?= htmlspecialchars($boek['Titel']) ?>">
        <?php endif; ?>

        <table class="gegevens">
            <tr><td>Biebcode</td><td><?= htmlspecialchars((string) $boek['Boek_id']) ?></td></tr>
            <tr><td>Auteur</td><td><?= htmlspecialchars($boek['Auteur'] ?: '') ?></td></tr>
            <tr><td>Uitgever</td><td><?= htmlspecialchars($boek['Uitgever'] ?: '') ?></td></tr>
            <tr>
                <td>ISBN</td>
                <td>
                    <a href="<?= htmlspecialchars(isbn_zoek_url($boek['ISBN13'] ?? null, $boek['Titel'], $boek['Auteur'] ?: '')) ?>" target="_blank" rel="noopener" title="Zoeken naar dit ISBN/deze titel op WorldCat">
                        <?= htmlspecialchars($boek['ISBN13'] ?: 'Zoek op WorldCat') ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td>Taal</td>
                <td><img class="vlag" src="<?= htmlspecialchars(taal_vlag_url($boek['Taal'])) ?>" alt="<?= htmlspecialchars($boek['Taal'] ?: '') ?>"></td>
            </tr>
            <tr><td>Status</td><td><?= uitgeleend($boek) ? 'Uitgeleend' : 'Aanwezig' ?></td></tr>
            <tr>
                <td>Uitgave</td>
                <td>
                    <?php if (!empty($boek['Uitgave_jaar'])): ?>Jaar: <?= htmlspecialchars((string) $boek['Uitgave_jaar']) ?><br><?php endif; ?>
                    <?php if (!empty($boek['Uitgave'])): ?>Type: <?= htmlspecialchars(uitgave_omschrijving($boek['Uitgave'])) ?><br><?php endif; ?>
                    <?php if (!empty($boek['Soort'])): ?>Soort: <?= htmlspecialchars(soort_omschrijving($boek['Soort'])) ?><?php endif; ?>
                </td>
            </tr>
            <tr><td>Steekwoorden</td><td><?= htmlspecialchars($boek['Steekwoorden'] ?: '') ?></td></tr>
            <tr><td>Opslag</td><td><b><?= htmlspecialchars($boek['Opslag'] ?: '') ?></b> &rarr; <?= htmlspecialchars(opslag_locatie($boek['Opslag'] ?? null)) ?></td></tr>
        </table>

        <?php if (!empty($boek['Omschrijving'])): ?>
            <div class="omschrijving"><?= nl2br(htmlspecialchars(schoon_omschrijving($boek['Omschrijving']))) ?></div>
        <?php endif; ?>

        <?php $exemplaren = andere_exemplaren($pdo, $boek); ?>

        <details class="beheer-blok">
            <summary class="btn btn-beheer">Aantal exemplaren &amp; locatie</summary>
            <?php if ($exemplaren === []): ?>
                <p>Geen andere exemplaren van dit boek gevonden.</p>
            <?php else: ?>
                <p>Nog <b><?= count($exemplaren) ?></b> ander(e) exemplaar/exemplaren van dit boek gevonden:</p>
                <ul class="exemplarenlijst">
                    <?php foreach ($exemplaren as $exemplaar): ?>
                        <li><a href="/boek.php?id=<?= (int) $exemplaar['Boek_id'] ?>&q=<?= urlencode($zoekstr) ?>"><?= htmlspecialchars($exemplaar['Opslag'] ?: 'onbekend') ?> &mdash; <?= htmlspecialchars((string) $exemplaar['Boek_id']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </details>

        <?php $auteurs = array_filter(array_map('trim', preg_split('/\s*(?:;|,|&|\ben\b)\s*/i', (string) ($boek['Auteur'] ?? '')))); ?>

        <div class="knoppen">
            <?php foreach ($auteurs as $auteur): ?>
                <a class="btn" href="/index.php?q=<?= urlencode($auteur) ?>">Meer van <?= htmlspecialchars($auteur) ?></a>
            <?php endforeach; ?>
            <a class="btn btn-beheer" href="<?= htmlspecialchars(boekwinkeltjes_zoek_url($boek['Titel'], $boek['Auteur'] ?: '')) ?>" target="_blank" rel="noopener">Zoek op Boekwinkeltjes.nl (prijsbepaling)</a>
            <a class="btn btn-terug" href="<?= htmlspecialchars($terugUrl) ?>">&larr; Terug naar zoekresultaten</a>
        </div>
    </article>

    <script>initSwipeNavigatie();</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
