<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

function checkTable($pdo, $tableName) {
    try {
        // Vérifier si la table existe
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if (!$stmt->fetch()) {
            echo "La table '$tableName' n'existe pas.\n";
            return;
        }

        // Afficher la structure de la table
        echo "\nStructure de la table '$tableName':\n";
        $stmt = $pdo->query("SHOW CREATE TABLE $tableName");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo $row['Create Table'] . "\n";
        }

        // Afficher quelques exemples de données
        echo "\nExemples de données de '$tableName':\n";
        $stmt = $pdo->query("SELECT * FROM $tableName LIMIT 3");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "\n----------------------------------------\n";
    } catch (PDOException $e) {
        echo "Erreur pour la table $tableName: " . $e->getMessage() . "\n";
    }
}

try {
    // Tables à vérifier
    $tables = [
        'articles',
        'register_entries',
        'formations_formations'
    ];

    foreach ($tables as $table) {
        checkTable($pdo, $table);
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>
