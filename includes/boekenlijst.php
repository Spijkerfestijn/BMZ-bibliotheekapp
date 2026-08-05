<?php
/**
 * @var array $resultaten
 * @var string $terugUrl volledige URL van de huidige zoekpagina, om vanaf de boekdetailpagina naar terug te linken
 */

$navigatieUrls = [];
foreach ($resultaten as $boek) {
    $navigatieUrls[] = "/boek.php?id={$boek['Boek_id']}&van=" . urlencode($terugUrl);
}
?>
<ul class="resultatenlijst boekenlijst">
    <?php foreach ($resultaten as $boek): ?>
        <li>
            <a href="/boek.php?id=<?= (int) $boek['Boek_id'] ?>&van=<?= urlencode($terugUrl) ?>">
                <?php $kaftUrl = kaft_afbeelding_url($boek['Afbeelding'] ?? null); ?>
                <?php if ($kaftUrl !== null): ?>
                    <img class="thumb" src="<?= htmlspecialchars($kaftUrl) ?>" alt="" loading="lazy">
                <?php else: ?>
                    <span class="thumb thumb-leeg" aria-hidden="true"></span>
                <?php endif; ?>
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
<?php if ($navigatieUrls !== []): ?>
    <script>bewaarResultatenLijst(<?= json_encode($navigatieUrls) ?>);</script>
<?php endif; ?>
