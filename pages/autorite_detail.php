<?php
/**
 * Page de détail d'une autorité
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Simuler un utilisateur pour test
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
}

$pdo = getPDO();

// Récupérer l'ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: /autorite');
    exit;
}

// Récupérer l'autorité
$stmt = $pdo->prepare("
    SELECT a.*, 
           t.nom as type_nom, t.icon as type_icon, t.slug as type_slug,
           c.nom as category_nom, c.icon as category_icon
    FROM autorite a
    LEFT JOIN autorite_types t ON a.type_id = t.id
    LEFT JOIN autorite_categories c ON a.category_id = c.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$autorite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$autorite) {
    header('Location: /autorite');
    exit;
}

$pageTitle = $autorite['nom'];
$pageDescription = $autorite['description'];
$isAdmin = isAdmin();

// Inclusion du template
ob_start();
include __DIR__ . '/../templates/pages/autorite_detail_template.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
