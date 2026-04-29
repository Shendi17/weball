<?php
/**
 * Page École
 * Gestion de l'école
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/ecole/EcoleService.php';

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pageTitle = "École";
$pageDescription = "Gestion de l'école";

// Récupération des données
$items = EcoleService::getAll();
$totalCount = EcoleService::count();
$isAdmin = isAdmin();

// Inclusion du template
ob_start();
include __DIR__ . '/../templates/pages/ecole.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
