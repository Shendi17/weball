<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    $tables = ['articles', 'register_entries', 'formations'];
    
    foreach ($tables as $table) {
        echo "\nStructure de la table '$table':\n";
        $stmt = $pdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']} ({$row['Type']})\n";
        }
    }
    
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
