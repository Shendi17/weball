<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/auth.php';

// Vérifier l'authentification
requireLogin();

// Vérifier les permissions
if (!hasPermission('formations')) {
    error_log("Formations - Accès refusé pour l'utilisateur " . $_SESSION['user']['username']);
    header('Location: ' . BASE_PATH . '/index.php');
    exit;
}

try {
    // Création de la table formations_formations si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS formations_formations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        date_debut DATE NOT NULL,
        date_fin DATE NOT NULL,
        statut ENUM('planifiee', 'en_cours', 'terminee') NOT NULL DEFAULT 'planifiee',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Création de la table formations_evaluations si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS formations_evaluations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        formation_id INT NOT NULL,
        utilisateur_id INT NOT NULL,
        note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
        commentaire TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (formation_id) REFERENCES formations_formations(id),
        FOREIGN KEY (utilisateur_id) REFERENCES users(id)
    )");

    // Insérer des données de test si les tables sont vides
    $stmt = $pdo->query("SELECT COUNT(*) FROM formations_formations");
    if ($stmt->fetchColumn() == 0) {
        // Insérer quelques formations de test
        $pdo->exec("INSERT INTO formations_formations (titre, description, date_debut, date_fin, statut) VALUES
            ('Formation PHP Avancé', 'Apprenez les concepts avancés de PHP', '2025-01-15', '2025-01-20', 'planifiee'),
            ('Formation JavaScript', 'Les bases de JavaScript', '2025-02-01', '2025-02-05', 'planifiee'),
            ('Formation SQL', 'Maîtrisez les bases de données', '2025-03-01', '2025-03-15', 'planifiee'),
            ('Formation Développement Web Avancé', 'Maîtrisez les technologies web modernes : HTML5, CSS3, JavaScript et PHP', '2025-04-01', '2025-04-30', 'planifiee'),
            ('Formation Sécurité Web', 'Sécurisez vos applications web', '2025-05-01', '2025-05-15', 'planifiee')");
        
        // Récupérer l'ID de l'utilisateur admin
        $stmt = $pdo->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
        $admin_id = $stmt->fetchColumn();
        
        if ($admin_id) {
            // Récupérer les IDs des formations
            $formations = $pdo->query("SELECT id FROM formations_formations")->fetchAll(PDO::FETCH_COLUMN);
            
            // Insérer quelques évaluations de test
            foreach ($formations as $formation_id) {
                $pdo->prepare("INSERT INTO formations_evaluations (formation_id, utilisateur_id, note, commentaire) VALUES (?, ?, ?, ?)")
                    ->execute([$formation_id, $admin_id, rand(3, 5), 'Très bonne formation']);
            }
        }
    }

} catch (PDOException $e) {
    error_log("Erreur lors de la création des tables : " . $e->getMessage());
}

// Fonction pour récupérer les formations à venir
function getProchainesFormations() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT * FROM formations_formations 
            WHERE date_debut >= CURDATE() 
            AND statut = 'planifiee'
            ORDER BY date_debut ASC 
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des prochaines formations : " . $e->getMessage());
        return [];
    }
}

// Fonction pour récupérer les dernières évaluations
function getDernieresEvaluations() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT e.*, f.titre as formation_titre, u.full_name as evaluateur
            FROM formations_evaluations e
            JOIN formations_formations f ON e.formation_id = f.id
            JOIN users u ON e.utilisateur_id = u.id
            ORDER BY e.created_at DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des évaluations : " . $e->getMessage());
        return [];
    }
}

// Fonctions utilitaires
function getFormationStatusLabel($status) {
    switch ($status) {
        case 'planifiee':
            return 'Planifiée';
        case 'en_cours':
            return 'En cours';
        case 'terminee':
            return 'Terminée';
        default:
            return 'Inconnu';
    }
}

function getFormationStatusClass($status) {
    switch ($status) {
        case 'planifiee':
            return 'info';
        case 'en_cours':
            return 'warning';
        case 'terminee':
            return 'success';
        default:
            return 'secondary';
    }
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

function formatDuree($minutes) {
    $heures = floor($minutes / 60);
    $min = $minutes % 60;
    return $heures . "h" . ($min > 0 ? $min : "");
}
?>
