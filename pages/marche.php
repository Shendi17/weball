<?php
/**
 * Page Marché
 * Gestion du marché
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/marche/MarcheService.php';

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pageTitle = "Marché";
$pageDescription = "Gestion du marché";

// Récupération des données
$items = MarcheService::getAll();
$totalCount = MarcheService::count();
$isAdmin = isAdmin();

// Inclusion du template
ob_start();
include __DIR__ . '/../templates/pages/marche.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
