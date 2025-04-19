<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

function showTableStructure($pdo, $tableName) {
    try {
        echo "\nStructure de la table '$tableName':\n";
        $stmt = $pdo->query("SHOW CREATE TABLE $tableName");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo $row['Create Table'] . "\n";
        }
    } catch (PDOException $e) {
        echo "Erreur pour la table $tableName: " . $e->getMessage() . "\n";
    }
}

try {
    $tables = [
        'articles',
        'register_entries',
        'formations'
    ];

    foreach ($tables as $table) {
        showTableStructure($pdo, $table);
        echo "\n----------------------------------------\n";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>
