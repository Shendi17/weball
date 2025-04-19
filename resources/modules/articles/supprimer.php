<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Récupérer l'image de l'article avant la suppression
$sql = "SELECT image_url FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

// Supprimer l'article
$sql = "DELETE FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Supprimer l'image si elle existe
    if ($article && $article['image_url'] && file_exists('../../' . $article['image_url'])) {
        unlink('../../' . $article['image_url']);
    }
    header('Location: index.php?success=3');
} else {
    header('Location: index.php?error=1');
}
exit;
