<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../includes/functions.php';
require_once dirname(__FILE__) . '/../../includes/db.php';

header('Content-Type: application/json');

// Vérifier la connexion à la base de données
try {
    $pdo->query("SELECT 1");
    error_log("Connexion à la base de données OK");
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur de connexion à la base de données',
        'debug' => [
            'message' => $e->getMessage(),
            'config' => [
                'host' => DB_HOST,
                'dbname' => DB_NAME,
                'user' => DB_USER
            ]
        ]
    ]);
    exit;
}

// Log pour le débogage
error_log("Requête reçue pour l'ID : " . ($_GET['id'] ?? 'non défini'));

// Vérifier si la table existe et la créer si nécessaire
try {
    $tables = $pdo->query("SHOW TABLES LIKE 'formations_formations'")->fetchAll();
    if (empty($tables)) {
        $sql = "CREATE TABLE formations_formations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) NOT NULL,
            description TEXT,
            niveau ENUM('debutant', 'intermediaire', 'avance') DEFAULT 'intermediaire',
            categorie ENUM('development', 'design', 'marketing') DEFAULT 'development',
            duree VARCHAR(50) DEFAULT '40 heures',
            date_debut DATE,
            date_fin DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        error_log("Table formations_formations créée");
        
        // Insérer les données d'exemple
        $sql = file_get_contents(dirname(__FILE__) . '/sample_data.sql');
        $pdo->exec($sql);
        error_log("Données d'exemple insérées");
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la vérification/création de la table : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la vérification/création de la table',
        'debug' => [
            'message' => $e->getMessage(),
            'config' => [
                'host' => DB_HOST,
                'dbname' => DB_NAME,
                'user' => DB_USER
            ]
        ]
    ]);
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'error' => 'ID de formation invalide',
        'debug' => [
            'get_params' => $_GET,
            'id_isset' => isset($_GET['id']),
            'id_is_numeric' => isset($_GET['id']) ? is_numeric($_GET['id']) : false
        ]
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM formations_formations WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    error_log("Requête SQL exécutée");
    
    $formation = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("Résultat de la requête : " . ($formation ? 'formation trouvée' : 'formation non trouvée'));

    if (!$formation) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Formation non trouvée',
            'debug' => [
                'id' => $_GET['id'],
                'sql' => "SELECT * FROM formations_formations WHERE id = " . $_GET['id']
            ]
        ]);
        exit;
    }

    // S'assurer que tous les champs nécessaires sont présents
    $formation = array_merge([
        'id' => 0,
        'titre' => '',
        'description' => '',
        'niveau' => 'intermediaire',
        'categorie' => 'development',
        'duree' => '40 heures',
        'date_debut' => null,
        'date_fin' => null
    ], $formation);

    // Formater les dates pour l'affichage
    if ($formation['date_debut']) {
        $formation['date_debut'] = date('d/m/Y', strtotime($formation['date_debut']));
    }
    if ($formation['date_fin']) {
        $formation['date_fin'] = date('d/m/Y', strtotime($formation['date_fin']));
    }

    echo json_encode([
        'success' => true,
        'formation' => $formation
    ]);

} catch (PDOException $e) {
    error_log("Erreur SQL dans get_formation.php : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la récupération des détails de la formation',
        'debug' => [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>
