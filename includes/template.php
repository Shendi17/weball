<?php
// Template principal : inclut header, sidebar, contenu principal, footer
if (!isset($pageTitle)) $pageTitle = "Weball";
require_once dirname(__DIR__) . '/config.php';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/weball/public/assets/css/style-assets.css">
    <link rel="stylesheet" href="/weball/public/assets/css/hero.css">
    <link rel="stylesheet" href="/weball/public/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="/weball/public/assets/css/brands.min.css">
    <link rel="stylesheet" href="/weball/public/assets/css/solid.min.css">
    <link rel="stylesheet" href="/weball/public/assets/css/header.css">
    <link rel="stylesheet" href="/weball/public/assets/css/footer.css">
    <link rel="stylesheet" href="/weball/public/assets/css/annuaire.css">
    <link rel="stylesheet" href="/weball/public/assets/css/home-widgets.css">
    <link rel="stylesheet" href="/weball/public/assets/css/home-search-create.css">
    <script src="/weball/public/assets/js/main-assets.js" defer></script>
    <?php include __DIR__ . '/head.php'; ?>
</head>
<body>
    <?php include_once __DIR__ . '/header.php'; ?>
    <div class="main-layout">
        <?php include_once __DIR__ . '/sidebar.php'; ?>
        <main class="main-content" id="main-content">
            <?= $content ?? '' ?>
        </main>
    </div>
    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>