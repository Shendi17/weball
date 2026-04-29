<?php
/**
 * Page de détail d'une personnalité
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../modules/personnalite/PersonnaliteService.php';

// Récupérer l'ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: /personnalite');
    exit;
}

// Récupérer la personnalité
$pdo = getPDO();
$stmt = $pdo->prepare("
    SELECT p.*, 
           t.nom as type_nom, t.icon as type_icon,
           c.nom as category_nom, c.icon as category_icon
    FROM personnalite p
    LEFT JOIN personnalite_types t ON p.type_id = t.id
    LEFT JOIN personnalite_categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$personnalite = $stmt->fetch();

if (!$personnalite) {
    header('Location: /personnalite');
    exit;
}

$pageTitle = $personnalite['prenom'] . ' ' . $personnalite['nom'];
$pageDescription = $personnalite['type_nom'] ?? 'Personnalité';

// Inclusion du template
ob_start();
include __DIR__ . '/../templates/pages/personnalite_detail.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
