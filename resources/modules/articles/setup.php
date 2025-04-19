<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/includes/functions.php';
require_once dirname(__DIR__, 3) . '/includes/db.php';

try {
    // Création de la table articles si elle n'existe pas
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

    echo "Table 'articles' créée ou déjà existante.\n";

    // Création de la table categories_articles si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories_articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        description TEXT,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_nom (nom)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "Table 'categories_articles' créée ou déjà existante.\n";

    // Ajout des catégories de base si la table est vide
    $count = $pdo->query("SELECT COUNT(*) FROM categories_articles")->fetchColumn();
    if ($count == 0) {
        $categories = [
            ['nom' => 'Actualités', 'description' => 'Les dernières nouvelles et mises à jour'],
            ['nom' => 'Tutoriels', 'description' => 'Guides et tutoriels pratiques'],
            ['nom' => 'Technologies', 'description' => 'Découvrez les dernières technologies'],
            ['nom' => 'Conseils', 'description' => 'Astuces et bonnes pratiques']
        ];

        $stmt = $pdo->prepare("INSERT INTO categories_articles (nom, description) VALUES (:nom, :description)");
        foreach ($categories as $cat) {
            $stmt->execute($cat);
        }
        echo "Catégories de base ajoutées.\n";
    }

    // Ajout d'un article de test si la table est vide
    $count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO articles (titre, contenu, categorie, statut, auteur_id) 
                              VALUES (:titre, :contenu, :categorie, :statut, :auteur_id)");
        
        $article = [
            'titre' => 'Article de test',
            'contenu' => '<p>Ceci est un article de test pour vérifier le bon fonctionnement du système.</p>',
            'categorie' => 'Actualités',
            'statut' => 'publie',
            'auteur_id' => 1
        ];
        
        $stmt->execute($article);
        echo "Article de test ajouté.\n";
    }

    echo "\nConfiguration terminée avec succès !";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Code : " . $e->getCode() . "\n";
}
