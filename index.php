<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/search.php';

$zoekstr = trim($_GET['q'] ?? '');

$resultaten = [];
$totaalResultaten = 0;
if ($zoekstr !== '') {
    $resultaten = zoek_boeken(db_connect(), $zoekstr);
    $totaalResultaten = count($resultaten);
}

$paginatitel = 'Bibliotheek Bevrijdingsmuseum Zeeland';
require __DIR__ . '/includes/header.php';
?>

<h1 class="paginakop">Bibliotheek Bevrijdingsmuseum Zeeland</h1>

<?php if ($zoekstr === ''): ?>
    <p class="hint">Voer een titel, auteur, categorie of trefwoord in om te beginnen.</p>
<?php elseif ($totaalResultaten === 0): ?>
    <p class="hint">Geen boeken gevonden voor "<?= htmlspecialchars($zoekstr) ?>".</p>
<?php else: ?>
    <ul class="resultatenlijst boekenlijst">
        <?php foreach ($resultaten as $boek): ?>
            <li>
                <a href="/boek.php?id=<?= (int) $boek['Boek_id'] ?>">
                    <span class="naam-wrap">
                        <?php if (!empty($boek['Serie_naam'])): ?>
                            <span class="serie"><?= htmlspecialchars($boek['Serie_naam']) ?> (<?= htmlspecialchars((string) $boek['Volgnr']) ?>)</span>
                        <?php endif; ?>
                        <span class="naam"><?= htmlspecialchars($boek['Titel']) ?></span>
                        <?php if (!empty($boek['Sub_titel'])): ?>
                            <span class="subtitel"><?= htmlspecialchars($boek['Sub_titel']) ?></span>
                        <?php endif; ?>
                        <span class="auteur"><?= htmlspecialchars($boek['Auteur'] ?: '') ?></span>
                    </span>
                    <span class="boek-meta">
                        <img class="vlag" src="<?= htmlspecialchars(taal_vlag_url($boek['Taal'])) ?>" alt="<?= htmlspecialchars($boek['Taal'] ?: '') ?>">
                        <span class="opslag"><?= htmlspecialchars($boek['Opslag'] ?: '') ?></span>
                        <span class="status status-<?= uitgeleend($boek) ? 'uit' : 'in' ?>"><?= uitgeleend($boek) ? 'Uitgeleend' : 'Aanwezig' ?></span>
                    </span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
