<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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

        // Obtenir la structure de la table
        echo "\nStructure de la table '$tableName':\n";
        $stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
        }

        // Compter le nombre d'enregistrements
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $tableName");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "\nNombre d'enregistrements dans '$tableName': $count\n";

        // Afficher quelques exemples d'enregistrements
        echo "\nExemples d'enregistrements de '$tableName':\n";
        $stmt = $pdo->query("SELECT * FROM $tableName LIMIT 3");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
        }

    } catch (PDOException $e) {
        echo "Erreur lors de la vérification de la table '$tableName': " . $e->getMessage() . "\n";
    }
}

try {
    $tables = ['articles', 'register_entries', 'formations'];
    foreach ($tables as $table) {
        checkTable($pdo, $table);
        echo "\n" . str_repeat('-', 80) . "\n";
    }
} catch (Exception $e) {
    echo "Erreur générale : " . $e->getMessage() . "\n";
}
?>
