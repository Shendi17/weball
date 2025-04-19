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

// Log pour le débogage
error_log('=== Début du script edit_formation.php ===');
error_log('Méthode: ' . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Récupérer les détails de la formation
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    error_log('GET - ID reçu: ' . $id);
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID invalide']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM formations_formations WHERE id = ?");
        $stmt->execute([$id]);
        $formation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$formation) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Formation non trouvée']);
            exit;
        }

        echo json_encode(['success' => true, 'formation' => $formation]);
    } catch (Exception $e) {
        error_log('Erreur GET: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la récupération de la formation']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mettre à jour la formation
    error_log('POST - Données reçues: ' . print_r($_POST, true));
    
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $niveau = filter_input(INPUT_POST, 'niveau', FILTER_SANITIZE_STRING);
    $duree = filter_input(INPUT_POST, 'duree', FILTER_SANITIZE_STRING);
    $date_debut = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
    $date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);
    $categorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING) ?? 'development';

    error_log('Données filtrées:');
    error_log('ID: ' . $id);
    error_log('Titre: ' . $titre);
    error_log('Description: ' . $description);
    error_log('Niveau: ' . $niveau);
    error_log('Durée: ' . $duree);
    error_log('Date début: ' . $date_debut);
    error_log('Date fin: ' . $date_fin);
    error_log('Catégorie: ' . $categorie);

    if (!$id || !$titre) {
        error_log('Données invalides - ID: ' . $id . ', Titre: ' . $titre);
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Données invalides']);
        exit;
    }

    try {
        $sql = "UPDATE formations_formations 
                SET titre = :titre, 
                    description = :description, 
                    niveau = :niveau, 
                    duree = :duree, 
                    date_debut = :date_debut, 
                    date_fin = :date_fin, 
                    categorie = :categorie 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $params = [
            ':id' => $id,
            ':titre' => $titre,
            ':description' => $description,
            ':niveau' => $niveau,
            ':duree' => $duree,
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin,
            ':categorie' => $categorie
        ];
        error_log('Requête SQL: ' . $sql);
        error_log('Paramètres: ' . print_r($params, true));
        
        $result = $stmt->execute($params);

        if ($result) {
            error_log('Mise à jour réussie');
            echo json_encode(['success' => true, 'message' => 'Formation mise à jour avec succès']);
        } else {
            error_log('Erreur lors de la mise à jour - PDO error info: ' . print_r($stmt->errorInfo(), true));
            throw new Exception('Erreur lors de la mise à jour');
        }
    } catch (Exception $e) {
        error_log('Exception lors de la mise à jour: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour de la formation']);
    }
}
?>
