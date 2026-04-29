<?php
/**
 * Page Autorité avec système de types et catégories
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pdo = getPDO();

$pageTitle = "Autorités";
$pageDescription = "Organismes, associations et sociétés";
$moduleName = 'autorite';

// Récupérer les types
$types = $pdo->query("
    SELECT t.*, COUNT(a.id) as count
    FROM autorite_types t
    LEFT JOIN autorite a ON a.type_id = t.id
    GROUP BY t.id
    ORDER BY t.ordre, t.nom
")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les catégories
$categories = $pdo->query("
    SELECT c.*, t.slug as type_slug, COUNT(a.id) as count
    FROM autorite_categories c
    LEFT JOIN autorite_types t ON c.type_id = t.id
    LEFT JOIN autorite a ON a.category_id = c.id
    GROUP BY c.id
    ORDER BY c.ordre, c.nom
")->fetchAll(PDO::FETCH_ASSOC);

// Filtres
$selectedType = isset($_GET['type']) ? (int)$_GET['type'] : null;
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : null;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construire la requête
$sql = "
    SELECT a.*, 
           t.nom as type_nom, t.icon as type_icon, t.slug as type_slug,
           c.nom as category_nom, c.icon as category_icon
    FROM autorite a
    LEFT JOIN autorite_types t ON a.type_id = t.id
    LEFT JOIN autorite_categories c ON a.category_id = c.id
    WHERE 1=1
";

$params = [];

if ($selectedType) {
    $sql .= " AND a.type_id = ?";
    $params[] = $selectedType;
}

if ($selectedCategory) {
    $sql .= " AND a.category_id = ?";
    $params[] = $selectedCategory;
}

if ($searchQuery) {
    $sql .= " AND (a.nom LIKE ? OR a.description LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

$sql .= " ORDER BY a.nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCount = count($items);
$isAdmin = isAdmin();

// Inclusion du template dynamique
ob_start();
include __DIR__ . '/../templates/pages/dynamic_module.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
