<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Initialisation complète de la base de données WebAll ===\n\n";

require_once dirname(__DIR__) . '/config.db.php';

try {
    echo "Connexion à la base de données...\n";
    $pdo = getPDO();
    echo "Connexion établie avec succès !\n\n";
    
    // 1. Table users (utilisateurs)
    echo "1. Création de la table users...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table users créée.\n\n";
    
    // 2. Table categories_articles
    echo "2. Création de la table categories_articles...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories_articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        description TEXT,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_nom (nom)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table categories_articles créée.\n\n";
    
    // 3. Table articles
    echo "3. Création de la table articles...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        contenu TEXT NOT NULL,
        image_url VARCHAR(255),
        categorie VARCHAR(100),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        auteur_id INT,
        statut ENUM('brouillon', 'publie') DEFAULT 'brouillon',
        FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table articles créée.\n\n";
    
    // 4. Table formations
    echo "4. Création de la table formations_formations...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS formations_formations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        niveau VARCHAR(50) DEFAULT 'intermediaire',
        duree VARCHAR(50) DEFAULT '40 heures',
        date_debut DATE,
        date_fin DATE,
        categorie VARCHAR(50) DEFAULT 'development',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table formations_formations créée.\n\n";
    
    // 5. Table annuaire (personnes)
    echo "5. Création de la table annuaire...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS annuaire (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(150),
        telephone VARCHAR(20),
        poste VARCHAR(100),
        service VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table annuaire créée.\n\n";
    
    // 6. Table documents
    echo "6. Création de la table documents...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        chemin VARCHAR(500) NOT NULL,
        type VARCHAR(50),
        taille INT,
        date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        uploaded_by INT,
        FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table documents créée.\n\n";
    
    // 7. Table evenements
    echo "7. Création de la table evenements...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS evenements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        date_debut DATETIME NOT NULL,
        date_fin DATETIME,
        lieu VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by INT,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table evenements créée.\n\n";
    
    // 8. Insertion des données de base
    echo "8. Insertion des données de base...\n";
    
    // Utilisateur admin par défaut
    $adminExists = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn();
    if ($adminExists == 0) {
        $pdo->exec("INSERT INTO users (username, email, password, role) VALUES 
            ('admin', 'admin@weball.local', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')");
        echo "✓ Utilisateur admin créé (login: admin, mot de passe: admin123)\n";
    }
    
    // Catégories d'articles
    $categories = [
        ['nom' => 'Actualités', 'description' => 'Les dernières nouvelles et mises à jour'],
        ['nom' => 'Tutoriels', 'description' => 'Guides et tutoriels pratiques'],
        ['nom' => 'Technologies', 'description' => 'Découvrez les dernières technologies'],
        ['nom' => 'Conseils', 'description' => 'Astuces et bonnes pratiques']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories_articles (nom, description) VALUES (:nom, :description)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
        echo "✓ Catégorie '{$cat['nom']}' ajoutée\n";
    }
    
    // Données de test pour l'annuaire
    $personnes = [
        ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont@weball.local', 'poste' => 'Développeur', 'service' => 'IT'],
        ['nom' => 'Martin', 'prenom' => 'Claire', 'email' => 'claire.martin@weball.local', 'poste' => 'Chef de projet', 'service' => 'Management'],
        ['nom' => 'Durand', 'prenom' => 'Paul', 'email' => 'paul.durand@weball.local', 'poste' => 'Designer', 'service' => 'Créatif']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO annuaire (nom, prenom, email, poste, service) VALUES (:nom, :prenom, :email, :poste, :service)");
    foreach ($personnes as $personne) {
        $stmt->execute($personne);
        echo "✓ Personne '{$personne['prenom']} {$personne['nom']}' ajoutée à l'annuaire\n";
    }
    
    echo "\n=== Vérification finale ===\n";
    $tables = ['users', 'articles', 'categories_articles', 'formations_formations', 'annuaire', 'documents', 'evenements'];
    
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "✓ Table '$table' : $count enregistrements\n";
    }
    
    echo "\n🎉 Initialisation de la base de données terminée avec succès !\n";
    echo "Vous pouvez maintenant accéder à votre application WebAll.\n";
    echo "Connexion admin : admin / admin123\n";
    
} catch (PDOException $e) {
    die("❌ Erreur de base de données : " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("❌ Erreur : " . $e->getMessage() . "\n");
}
?>
