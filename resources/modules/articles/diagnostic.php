<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Utilisation de la config centrale PDO
require_once dirname(__DIR__, 3) . '/config.db.php';

try {
    echo "Connexion établie avec succès !\n\n";
    
    // Vérifier si la table articles existe
    $tables = $pdo->query("SHOW TABLES LIKE 'articles'")->fetchAll();
    echo "Table 'articles' existe : " . (!empty($tables) ? "Oui" : "Non") . "\n";

    if (!empty($tables)) {
        // Afficher la structure de la table
        echo "\nStructure de la table 'articles' :\n";
        $columns = $pdo->query("SHOW COLUMNS FROM articles")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- {$column['Field']} : {$column['Type']}" . 
                 ($column['Null'] === 'NO' ? ' NOT NULL' : '') . 
                 (isset($column['Default']) ? " DEFAULT '{$column['Default']}'" : '') . "\n";
        }

        // Vérifier si la table contient des données
        $count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        echo "\nNombre d'articles : $count\n";

        if ($count > 0) {
            echo "\nDerniers articles ajoutés :\n";
            $articles = $pdo->query("SELECT id, titre, categorie, statut FROM articles ORDER BY date_creation DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($articles as $article) {
                echo "- [{$article['id']}] {$article['titre']} ({$article['categorie']}) - {$article['statut']}\n";
            }
        }
    }

    // Vérifier si la table categories_articles existe
    $tables = $pdo->query("SHOW TABLES LIKE 'categories_articles'")->fetchAll();
    echo "\nTable 'categories_articles' existe : " . (!empty($tables) ? "Oui" : "Non") . "\n";

    if (!empty($tables)) {
        // Afficher les catégories
        echo "\nCatégories existantes :\n";
        $categories = $pdo->query("SELECT * FROM categories_articles")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $cat) {
            echo "- [{$cat['id']}] {$cat['nom']}\n";
        }
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Code : " . $e->getCode() . "\n";
    
    // Afficher la trace de la pile pour le débogage
    echo "\nTrace de la pile :\n";
    echo $e->getTraceAsString() . "\n";
    
    // Afficher les paramètres de connexion (sans le mot de passe)
    echo "\nParamètres de connexion :\n";
    echo "Host: " . $pdo->getAttribute(PDO::ATTR_SERVER) . "\n";
    echo "Database: " . $pdo->getAttribute(PDO::ATTR_DBNAME) . "\n";
    echo "User: " . $pdo->getAttribute(PDO::ATTR_USER) . "\n";
    echo "Port: " . $pdo->getAttribute(PDO::ATTR_PORT) . "\n";
}
