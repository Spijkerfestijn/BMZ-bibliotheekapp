<?php
/**
 * @var array $resultaten
 * @var string $terugUrl volledige URL van de huidige zoekpagina, om vanaf de boekdetailpagina naar terug te linken
 */

$navigatieUrls = [];
foreach ($resultaten as $boek) {
    $navigatieUrls[] = "/boek.php?id={$boek['id']}&van=" . urlencode($terugUrl);
}
?>
<ul class="resultatenlijst boekenlijst">
    <?php foreach ($resultaten as $boek): ?>
        <li>
            <a href="/boek.php?id=<?= (int) $boek['id'] ?>&van=<?= urlencode($terugUrl) ?>">
                <img class="thumb" src="<?= htmlspecialchars(kaft_afbeelding_url((int) $boek['id'])) ?>" alt="" loading="lazy" onerror="kaftFout(this)">
                <span class="naam-wrap">
                    <?php if (!empty($boek['series_name'])): ?>
                        <span class="serie"><?= htmlspecialchars($boek['series_name']) ?> (<?= htmlspecialchars((string) $boek['series_number']) ?>)</span>
                    <?php endif; ?>
                    <span class="naam"><?= htmlspecialchars($boek['title']) ?></span>
                    <?php if (!empty($boek['subtitle'])): ?>
                        <span class="subtitel"><?= htmlspecialchars($boek['subtitle']) ?></span>
                    <?php endif; ?>
                    <span class="auteur"><?= htmlspecialchars($boek['author'] ?: '') ?></span>
                </span>
                <span class="boek-meta">
                    <img class="vlag" src="<?= htmlspecialchars(taal_vlag_url($boek['taal_code'] ?? null)) ?>" alt="<?= htmlspecialchars($boek['taal_code'] ?? '') ?>">
                    <span class="opslag"><?= htmlspecialchars($boek['opslag_code'] ?: '') ?></span>
                    <span class="status status-<?= uitgeleend($boek) ? 'uit' : 'in' ?>"><?= uitgeleend($boek) ? 'Uitgeleend' : 'Aanwezig' ?></span>
                </span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<?php if ($navigatieUrls !== []): ?>
    <script>bewaarResultatenLijst(<?= json_encode($navigatieUrls) ?>);</script>
<?php endif; ?>
