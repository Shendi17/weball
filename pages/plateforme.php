<?php
/**
 * Page Plateforme
 * Gestion de la plateforme
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/plateforme/PlateformeService.php';

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pageTitle = "Plateforme";
$pageDescription = "Gestion de la plateforme";

// Récupération des données
$items = PlateformeService::getAll();
$totalCount = PlateformeService::count();
$isAdmin = isAdmin();

// Inclusion du template
ob_start();
include __DIR__ . '/../templates/pages/plateforme.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
