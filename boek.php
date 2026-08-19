<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/search.php';

$boekId = (int) ($_GET['id'] ?? 0);
$zoekstr = trim($_GET['q'] ?? '');
$van = trim($_GET['van'] ?? '');
$terugUrl = $van !== ''
    ? veilige_terug_url($van)
    : '/index.php' . ($zoekstr !== '' ? '?q=' . urlencode($zoekstr) : '');

$pdo = db_connect();
$boek = $boekId > 0 ? haal_boek($pdo, $boekId) : null;

$paginatitel = $boek ? $boek['title'] : 'Boek niet gevonden';
require __DIR__ . '/includes/header.php';
?>

<?php if ($boek === null): ?>
    <p class="hint">Dit boek kon niet worden gevonden.</p>
    <div class="knoppen">
        <a class="btn btn-terug" href="<?= htmlspecialchars($terugUrl) ?>">&larr; Terug naar zoekresultaten</a>
    </div>
<?php else: ?>
    <article>
        <?php if (!empty($boek['series_name'])): ?>
            <p class="serie"><?= htmlspecialchars($boek['series_name']) ?> (<?= htmlspecialchars((string) $boek['series_number']) ?>)</p>
        <?php endif; ?>
        <h1><?= htmlspecialchars($boek['title']) ?></h1>
        <?php if (!empty($boek['subtitle'])): ?>
            <p class="subtitel"><?= htmlspecialchars($boek['subtitle']) ?></p>
        <?php endif; ?>

        <?php $kaftUrl = kaft_afbeelding_url($boek['cover_image'] ?? null); ?>
        <?php if ($kaftUrl !== null): ?>
            <img class="kaft" src="<?= htmlspecialchars($kaftUrl) ?>" alt="Omslag van <?= htmlspecialchars($boek['title']) ?>">
        <?php endif; ?>

        <table class="gegevens">
            <caption class="sr-only">Boekgegevens</caption>
            <tr><th scope="row">Boekcode</th><td><?= htmlspecialchars((string) $boek['id']) ?></td></tr>
            <tr><th scope="row">Auteur</th><td><?= htmlspecialchars($boek['author'] ?: '') ?></td></tr>
            <tr><th scope="row">Uitgever</th><td><?= htmlspecialchars($boek['publisher'] ?: '') ?></td></tr>
            <tr>
                <th scope="row">ISBN</th>
                <td>
                    <a href="<?= htmlspecialchars(isbn_zoek_url($boek['isbn13'] ?? null, $boek['title'], $boek['author'] ?: '')) ?>" target="_blank" rel="noopener" title="Zoeken naar dit ISBN/deze titel op WorldCat">
                        <?= htmlspecialchars($boek['isbn13'] ?: 'Zoek op WorldCat') ?><span class="sr-only"> (opent in nieuw tabblad)</span>
                    </a>
                </td>
            </tr>
            <tr>
                <th scope="row">Taal</th>
                <td><img class="vlag" src="<?= htmlspecialchars(taal_vlag_url($boek['taal_code'] ?? null)) ?>" alt="<?= htmlspecialchars($boek['taal_naam'] ?? $boek['taal_code'] ?? '') ?>"></td>
            </tr>
            <tr><th scope="row">Status</th><td><?= uitgeleend($boek) ? 'Uitgeleend' : 'Aanwezig' ?></td></tr>
            <tr>
                <th scope="row">Uitgave</th>
                <td>
                    <?php if (!empty($boek['publish_year'])): ?>Jaar: <?= htmlspecialchars((string) $boek['publish_year']) ?><br><?php endif; ?>
                    <?php if (!empty($boek['uitgave_naam'])): ?>Type: <?= htmlspecialchars($boek['uitgave_naam']) ?><br><?php endif; ?>
                    <?php if (!empty($boek['soort_naam'])): ?>Soort: <?= htmlspecialchars($boek['soort_naam']) ?><?php endif; ?>
                </td>
            </tr>
            <?php if (!empty($boek['categorie_naam'])): ?>
                <tr><th scope="row">Categorie</th><td><?= htmlspecialchars($boek['categorie_naam']) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($boek['staat_naam'])): ?>
                <tr><th scope="row">Staat</th><td><?= htmlspecialchars($boek['staat_naam']) ?></td></tr>
            <?php endif; ?>
            <tr>
                <th scope="row">Steekwoorden</th>
                <td>
                    <?php $steekwoorden = array_filter(array_map('trim', explode(';', (string) ($boek['keywords'] ?? '')))); ?>
                    <?php foreach ($steekwoorden as $i => $steekwoord): ?>
                        <?= $i > 0 ? ', ' : '' ?><a href="/index.php?q=<?= urlencode($steekwoord) ?>" title="Zoek boeken met dit trefwoord"><?= htmlspecialchars($steekwoord) ?></a>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr><th scope="row">Opslag</th><td><b><?= htmlspecialchars($boek['opslag_code'] ?: '') ?></b><?= !empty($boek['opslag_naam']) ? ' &rarr; ' . htmlspecialchars($boek['opslag_naam']) : '' ?></td></tr>
        </table>

        <?php if (!empty($boek['description'])): ?>
            <div class="omschrijving"><?= nl2br(htmlspecialchars(schoon_omschrijving($boek['description']))) ?></div>
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
                        <li><a href="/boek.php?id=<?= (int) $exemplaar['id'] ?>&van=<?= urlencode($terugUrl) ?>"><?= htmlspecialchars($exemplaar['opslag_naam'] ?: ($exemplaar['opslag_code'] ?: 'onbekend')) ?> &mdash; <?= htmlspecialchars((string) $exemplaar['id']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </details>

        <?php $auteurs = splits_auteurs($boek['author'] ?? ''); ?>

        <div class="knoppen">
            <?php foreach ($auteurs as $auteur): ?>
                <a class="btn" href="/index.php?q=<?= urlencode($auteur) ?>">Meer van <?= htmlspecialchars($auteur) ?></a>
            <?php endforeach; ?>
            <a class="btn btn-beheer" href="<?= htmlspecialchars(boekwinkeltjes_zoek_url($boek['title'], $boek['author'] ?: '')) ?>" target="_blank" rel="noopener">Zoek op Boekwinkeltjes.nl (prijsbepaling)<span class="sr-only"> (opent in nieuw tabblad)</span></a>
            <a class="btn btn-terug" href="<?= htmlspecialchars($terugUrl) ?>">&larr; Terug naar zoekresultaten</a>
        </div>
    </article>

    <script>initSwipeNavigatie();</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
