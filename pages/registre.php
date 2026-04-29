<?php
// Logique métier de la page Registre
// Session déjà démarrée par config.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/registre/RegistreService.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

$pageTitle = "Registre";

// Récupération des données (exemple)
$categories = RegistreService::getCategories();
$documents = RegistreService::getDocuments();
$isAdmin = isAdmin();

// Inclusion du template de page
include __DIR__ . '/../templates/pages/registre.php';
