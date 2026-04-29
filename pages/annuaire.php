<?php
// Logique métier de la page Annuaire
// Session déjà démarrée par config.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/annuaire/AnnuaireService.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

$pageTitle = "Annuaire";

// Récupération des données
$personnes = AnnuaireService::getPersonnes();
$isAdmin = isAdmin();

ob_start();
include __DIR__ . '/../templates/pages/annuaire.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
