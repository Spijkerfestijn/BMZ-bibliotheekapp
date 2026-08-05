<?php
/** @var string $paginatitel */
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($paginatitel ?? 'Bibliotheek Bevrijdingsmuseum Zeeland') ?></title>
<link rel="icon" href="/favicon.ico">
<link rel="stylesheet" href="/css/style.css?v=<?= @filemtime(__DIR__ . '/../css/style.css') ?: time() ?>">
<script src="/js/navigatie.js?v=<?= @filemtime(__DIR__ . '/../js/navigatie.js') ?: time() ?>"></script>
</head>
<body>
<header class="site-header">
    <a href="/" class="site-logo"><img src="/bmz_images/bmz.png" alt="Bevrijdingsmuseum Zeeland"></a>
    <form class="zoekform" method="get" action="/index.php">
        <input type="search" name="q" placeholder="Voer een titel, auteur of trefwoord in..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit">Zoeken</button>
    </form>
</header>
<main>
