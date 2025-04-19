<?php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Récupérer l'URL du lien
        $stmt = $pdo->prepare("SELECT url FROM links WHERE id = ?");
        $stmt->execute([$id]);
        $link = $stmt->fetch();
        
        if ($link) {
            // Incrémenter le compteur de clics
            $stmt = $pdo->prepare("UPDATE links SET clicks = clicks + 1 WHERE id = ?");
            $stmt->execute([$id]);
            
            // Rediriger vers l'URL
            header("Location: " . $link['url']);
            exit;
        }
    } catch (PDOException $e) {
        die("Erreur lors de la redirection : " . $e->getMessage());
    }
}

// Si on arrive ici, c'est qu'il y a eu une erreur
header("Location: index.php");
exit;
