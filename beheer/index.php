<?php

$doel = 'https://beheerbieb.bevrijdingsmuseumzeeland.nl/';

$paginatitel = 'Beheeromgeving verplaatst';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="paginakop">Deze site is verplaatst</h1>
<p class="hint">U wordt over enkele seconden automatisch doorgestuurd naar de beheeromgeving.</p>

<div class="knoppen">
    <a class="btn" href="<?= htmlspecialchars($doel) ?>">Ga direct naar de beheeromgeving</a>
</div>

<script>setTimeout(function () { window.location.href = <?= json_encode($doel) ?>; }, 3000);</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
