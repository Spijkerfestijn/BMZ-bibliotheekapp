<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/search.php';

$zoekstr = trim($_GET['q'] ?? '');
$terugUrl = '/index.php' . ($zoekstr !== '' ? '?q=' . urlencode($zoekstr) : '');

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
<p class="help-link"><a href="/geavanceerd.php">Geavanceerd zoeken</a></p>

<?php if ($zoekstr === ''): ?>
    <p class="hint">Voer een titel, auteur, categorie of trefwoord in om te beginnen.</p>
<?php elseif ($totaalResultaten === 0): ?>
    <p class="hint">Geen boeken gevonden voor "<?= htmlspecialchars($zoekstr) ?>".</p>
<?php else: ?>
    <?php require __DIR__ . '/includes/boekenlijst.php'; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
