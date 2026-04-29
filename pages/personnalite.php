<?php
/**
 * Page Personnalité avec système de types et catégories
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/personnalite/PersonnaliteService.php';

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pageTitle = "Personnalité";
$pageDescription = "Répertoire des personnalités";
$moduleIcon = "user-tie";
$moduleName = "personnalite";

// Récupérer le type et la catégorie sélectionnés
$selectedType = isset($_GET['type']) ? (int)$_GET['type'] : null;
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Récupération des types
$pdo = getPDO();
$types = $pdo->query("SELECT * FROM personnalite_types WHERE actif = 1 ORDER BY ordre")->fetchAll();

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM personnalite_categories WHERE actif = 1 ORDER BY ordre")->fetchAll();

// Debug temporaire
if (isset($_GET['debug'])) {
    echo "<pre>";
    echo "Types: " . count($types) . "\n";
    print_r($types);
    echo "\nCategories: " . count($categories) . "\n";
    print_r($categories);
    echo "</pre>";
    exit;
}

// Récupération des données avec jointures
$sql = "SELECT p.*, 
        t.nom as type_nom, t.icon as type_icon,
        c.nom as category_nom, c.icon as category_icon
        FROM personnalite p
        LEFT JOIN personnalite_types t ON p.type_id = t.id
        LEFT JOIN personnalite_categories c ON p.category_id = c.id
        WHERE 1=1";

$params = [];

if ($selectedType) {
    $sql .= " AND p.type_id = ?";
    $params[] = $selectedType;
}

if ($selectedCategory) {
    $sql .= " AND p.category_id = ?";
    $params[] = $selectedCategory;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$totalCount = PersonnaliteService::count();
$isAdmin = isAdmin();

// Inclusion du template dynamique
ob_start();
include __DIR__ . '/../templates/pages/dynamic_module.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
