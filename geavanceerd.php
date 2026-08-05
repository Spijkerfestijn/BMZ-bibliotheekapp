<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/search.php';
require_once __DIR__ . '/includes/geavanceerd.php';

$pdo = db_connect();

$g = function (string $key): string {
    return trim($_GET[$key] ?? '');
};

$filters = [
    'titel' => $g('titel'),
    'categorie' => $g('categorie'),
    'inhoud' => $g('inhoud'),
    'schrijver' => $g('schrijver'),
];
$filtersIngevuld = array_filter($filters) !== [];

$resultaten = [];
if ($filtersIngevuld) {
    $resultaten = geavanceerd_zoek_boeken($pdo, $filters);
}

$terugUrl = '/geavanceerd.php?' . http_build_query($filters);

$paginatitel = 'Geavanceerd zoeken';
require __DIR__ . '/includes/header.php';
?>

<h1 class="paginakop">Geavanceerd zoeken</h1>

<details class="filterform-inklap" <?= $filtersIngevuld ? '' : 'open' ?>>
<summary>Zoekfilters</summary>
<form method="get" class="filterform">
    <label>Titel
        <input type="text" name="titel" value="<?= htmlspecialchars($filters['titel']) ?>" placeholder="bijv. Zeeland 1940">
    </label>

    <label>Categorie
        <select name="categorie">
            <option value="">Alle</option>
            <?php foreach (categorieen($pdo) as $categorie): ?>
                <option value="<?= htmlspecialchars($categorie) ?>" <?= $filters['categorie'] === $categorie ? 'selected' : '' ?>><?= htmlspecialchars($categorie) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Inhoud (omschrijving/steekwoorden)
        <input type="text" name="inhoud" value="<?= htmlspecialchars($filters['inhoud']) ?>" placeholder="bijv. Atlantikwal">
    </label>

    <label>Schrijver
        <input type="text" name="schrijver" value="<?= htmlspecialchars($filters['schrijver']) ?>" placeholder="bijv. Hans Sakkers">
    </label>

    <div class="knoppen">
        <button type="submit" class="btn">Zoeken</button>
        <a class="btn btn-terug" href="/geavanceerd.php">Filters wissen</a>
    </div>
</form>
</details>

<?php if (!$filtersIngevuld): ?>
    <p class="hint">Vul minstens één filter in en combineer ze naar wens.</p>
<?php elseif ($resultaten === []): ?>
    <p class="hint">Geen boeken gevonden voor deze combinatie van filters.</p>
<?php else: ?>
    <h2><?= count($resultaten) ?> resultaten</h2>
    <?php require __DIR__ . '/includes/boekenlijst.php'; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
