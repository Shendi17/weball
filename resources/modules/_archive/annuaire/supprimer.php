<?php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    try {
        $pdo->beginTransaction();

        // Suppression des services de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM user_services WHERE user_id = :id");
        $stmt->execute(['id' => $_GET['id']]);

        // Suppression de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);

        $pdo->commit();
        $_SESSION['success'] = "Utilisateur supprimé avec succès.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Redirection vers la liste
header('Location: index.php');
exit;
