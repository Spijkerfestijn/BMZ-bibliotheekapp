<?php

$doel = '/index.php';

$paginatitel = 'Pagina verplaatst';
require __DIR__ . '/includes/header.php';
?>

<h1 class="paginakop">Deze pagina is verplaatst</h1>
<p class="hint">De beheerfunctionaliteit is hier komen te vervallen. U wordt over enkele seconden doorgestuurd naar de bibliotheek.</p>

<div class="knoppen">
    <a class="btn" href="<?= htmlspecialchars($doel) ?>">Ga direct naar de bibliotheek</a>
</div>

<script>setTimeout(function () { window.location.href = <?= json_encode($doel) ?>; }, 3000);</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
