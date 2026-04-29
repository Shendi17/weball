<?php
/**
 * Page Boutique
 * Boutiques et commerces
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/boutique/BoutiqueService.php';

// Vérifier si l'utilisateur est connecté (temporairement désactivé pour test)
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ' . BASE_PATH . '/login.php');
//     exit;
// }

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pageTitle = "Boutique";
$pageDescription = "Boutiques et commerces";

// Récupération des données
$items = BoutiqueService::getAll();
$totalCount = BoutiqueService::count();
$isAdmin = isAdmin();

// Inclusion du template
ob_start();
include __DIR__ . '/../templates/pages/boutique.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
