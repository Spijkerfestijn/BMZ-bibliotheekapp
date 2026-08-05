</main>
<footer class="site-footer">
    <?php if (isset($zoekstr) && $zoekstr !== '' && isset($totaalResultaten)): ?>
        <span><?= $totaalResultaten ?> resultaten getoond</span>
    <?php else: ?>
        <span>&copy; <?= date('Y') ?> Bevrijdingsmuseum Zeeland</span>
    <?php endif; ?>
</footer>
</body>
</html>
