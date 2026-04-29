<?php
// Template principal : inclut sidebar, topbar, contenu principal, footer
if (!isset($pageTitle)) $pageTitle = "Weball";
require_once dirname(__DIR__) . '/config.php';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Weball') ?></title>
    <link rel="stylesheet" href="/assets/css/sidebar-modern.css">
    <script src="/assets/js/main-assets.js" defer></script>
    <?php include __DIR__ . '/head.php'; ?>
</head>
<body>
    <div class="main-layout">
        <?php include_once __DIR__ . '/sidebar.php'; ?>
        <div class="main-zone">
            <?php include_once __DIR__ . '/topbar.php'; ?>
            <main class="main-content" id="main-content">
                <?= $content ?? '' ?>
            </main>
            <?php include_once __DIR__ . '/footer.php'; ?>
        </div>
    </div>
</body>
</html>