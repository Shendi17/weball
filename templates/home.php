<?php
$pageTitle = "Accueil - Weball";

ob_start();
?>
<?php include_once dirname(__DIR__) . '/templates/hero.php'; ?>
<?php include_once dirname(__DIR__) . '/components/home-search-create.php'; ?>
<?php include_once dirname(__DIR__) . '/components/home-widgets.php'; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/template.php';
?>
