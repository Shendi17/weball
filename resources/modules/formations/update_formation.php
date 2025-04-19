<?php
session_start();

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Accès non autorisé']);
    exit;
}

require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../includes/functions.php';
require_once dirname(__FILE__) . '/../../includes/db.php';

header('Content-Type: application/json');

// Vérifier si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer et valider les données
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$niveau = filter_input(INPUT_POST, 'niveau', FILTER_SANITIZE_STRING);
$duree = filter_input(INPUT_POST, 'duree', FILTER_SANITIZE_STRING);
$date_debut = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
$date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);

// Vérifier les données obligatoires
if (!$id || !$titre) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données manquantes ou invalides']);
    exit;
}

try {
    // Préparer la requête de mise à jour
    $sql = "UPDATE formations_formations SET 
            titre = :titre,
            description = :description,
            niveau = :niveau,
            duree = :duree,
            date_debut = :date_debut,
            date_fin = :date_fin
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    
    // Exécuter la requête
    $result = $stmt->execute([
        ':id' => $id,
        ':titre' => $titre,
        ':description' => $description,
        ':niveau' => $niveau,
        ':duree' => $duree,
        ':date_debut' => $date_debut,
        ':date_fin' => $date_fin
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Formation mise à jour avec succès']);
    } else {
        throw new Exception('Erreur lors de la mise à jour de la formation');
    }
} catch (Exception $e) {
    error_log("Erreur lors de la mise à jour de la formation : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour de la formation']);
}
?>
