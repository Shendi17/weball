<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Initialisation des tables pour le module Articles\n\n";

// Utilisation de la config centrale PDO
require_once dirname(__DIR__,3) . '/config.db.php';

try {
    echo "Tentative de connexion à la base de données...\n";
    $pdo = getPDO();
    echo "Connexion établie avec succès !\n\n";
    
    // Création de la table categories_articles
    echo "Création de la table categories_articles...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories_articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        description TEXT,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_nom (nom)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table categories_articles créée.\n\n";
    
    // Création de la table articles
    echo "Création de la table articles...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        contenu TEXT NOT NULL,
        image_url VARCHAR(255),
        categorie VARCHAR(100),
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        auteur_id INT,
        statut ENUM('brouillon', 'publie') DEFAULT 'brouillon'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table articles créée.\n\n";
    
    // Ajout des catégories de base
    echo "Ajout des catégories de base...\n";
    $categories = [
        ['nom' => 'Actualités', 'description' => 'Les dernières nouvelles et mises à jour'],
        ['nom' => 'Tutoriels', 'description' => 'Guides et tutoriels pratiques'],
        ['nom' => 'Technologies', 'description' => 'Découvrez les dernières technologies'],
        ['nom' => 'Conseils', 'description' => 'Astuces et bonnes pratiques']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories_articles (nom, description) VALUES (:nom, :description)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
        echo "- Catégorie '{$cat['nom']}' ajoutée ou déjà existante\n";
    }
    echo "\n";
    
    // Ajout d'un article de test
    echo "Ajout d'un article de test...\n";
    $article = [
        'titre' => 'Article de test',
        'contenu' => '<p>Ceci est un article de test pour vérifier le bon fonctionnement du système.</p>',
        'categorie' => 'Actualités',
        'statut' => 'publie'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO articles (titre, contenu, categorie, statut) VALUES (:titre, :contenu, :categorie, :statut)");
    $stmt->execute($article);
    echo "Article de test ajouté.\n\n";
    
    // Vérification finale
    echo "Vérification des données :\n";
    echo "Nombre de catégories : " . $pdo->query("SELECT COUNT(*) FROM categories_articles")->fetchColumn() . "\n";
    echo "Nombre d'articles : " . $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn() . "\n";
    
    echo "\nInitialisation terminée avec succès !";
    
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
