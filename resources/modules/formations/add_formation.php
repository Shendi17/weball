<?php
session_start();
error_log("Début du traitement de add_formation.php");

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    error_log("Accès non autorisé: user_id=" . ($_SESSION['user_id'] ?? 'non défini') . ", role=" . ($_SESSION['user_role'] ?? 'non défini'));
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Accès non autorisé']);
    exit;
}

require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../includes/functions.php';
require_once dirname(__FILE__) . '/../../includes/db.php';

error_log("Méthode de requête: " . $_SERVER['REQUEST_METHOD']);

// Vérifier si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Méthode non autorisée: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer et valider les données
$titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$niveau = filter_input(INPUT_POST, 'niveau', FILTER_SANITIZE_STRING);
$duree = filter_input(INPUT_POST, 'duree', FILTER_SANITIZE_STRING);
$date_debut = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
$date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);
$categorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING) ?? 'development';

error_log("Données reçues: " . json_encode([
    'titre' => $titre,
    'description' => $description,
    'niveau' => $niveau,
    'duree' => $duree,
    'date_debut' => $date_debut,
    'date_fin' => $date_fin,
    'categorie' => $categorie
]));

// Vérifier les données obligatoires
if (!$titre) {
    error_log("Erreur: titre manquant");
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Le titre est obligatoire']);
    exit;
}

try {
    // Préparer la requête d'insertion
    $sql = "INSERT INTO formations_formations (titre, description, niveau, duree, date_debut, date_fin, categorie) 
            VALUES (:titre, :description, :niveau, :duree, :date_debut, :date_fin, :categorie)";
    
    error_log("SQL: " . $sql);
    
    $stmt = $pdo->prepare($sql);
    
    // Exécuter la requête
    $result = $stmt->execute([
        ':titre' => $titre,
        ':description' => $description,
        ':niveau' => $niveau,
        ':duree' => $duree,
        ':date_debut' => $date_debut,
        ':date_fin' => $date_fin,
        ':categorie' => $categorie
    ]);
    
    if ($result) {
        error_log("Formation ajoutée avec succès");
        echo json_encode(['success' => true, 'message' => 'Formation ajoutée avec succès']);
    } else {
        throw new Exception('Erreur lors de l\'ajout de la formation');
    }
} catch (Exception $e) {
    error_log("Erreur lors de l'ajout de la formation : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'ajout de la formation']);
}
?>
