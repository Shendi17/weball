<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__DIR__) . '/config.db.php';

try {
    echo "Démarrage de la vérification de la base de données...\n";
    
    // Utilisation de la config centrale PDO
    $pdo = getPDO();
    echo "Connexion établie avec succès !\n";

    // Vérifier si la base de données existe
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $dbExists = $stmt->fetch();

    if (!$dbExists) {
        echo "La base de données '" . DB_NAME . "' n'existe pas. Création en cours...\n";
        
        // Créer la base de données
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "Base de données créée avec succès.\n";
    } else {
        echo "La base de données '" . DB_NAME . "' existe déjà.\n";
    }

    // Se connecter à la base de données
    $pdo->exec("USE `" . DB_NAME . "`");
    echo "Connexion à la base de données réussie.\n";

    // Vérifier les tables
    $tables = ['articles', 'documents', 'evenements'];
    $missingTables = [];

    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if (!$stmt->fetch()) {
            $missingTables[] = $table;
        }
    }

    if (!empty($missingTables)) {
        echo "Tables manquantes : " . implode(', ', $missingTables) . "\n";
        echo "Exécution du script d'initialisation des tables...\n";
        
        // Inclure le script d'initialisation
        require_once __DIR__ . '/init_db.php';
    } else {
        echo "Toutes les tables nécessaires existent.\n";
        
        // Vérifier le contenu des tables
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "Table '$table' : $count enregistrements\n";
        }
    }

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}
?>
