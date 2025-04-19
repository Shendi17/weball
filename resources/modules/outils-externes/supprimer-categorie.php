<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        // Vérifier s'il y a des liens dans cette catégorie
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM links WHERE category_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Cette catégorie contient des liens. Veuillez d\'abord déplacer ou supprimer ces liens.'
            ]);
            exit;
        }
        
        // Supprimer la catégorie
        $stmt = $pdo->prepare("DELETE FROM link_categories WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Requête invalide'
    ]);
}
