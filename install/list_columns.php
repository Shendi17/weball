<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    // Vérifier les colonnes de la table articles
    $stmt = $pdo->query("SELECT * FROM articles LIMIT 1");
    echo "Colonnes de la table articles:\n";
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $col = $stmt->getColumnMeta($i);
        echo "- {$col['name']}\n";
    }

    echo "\n";

    // Vérifier les colonnes de la table register_entries
    $stmt = $pdo->query("SELECT * FROM register_entries LIMIT 1");
    echo "Colonnes de la table register_entries:\n";
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $col = $stmt->getColumnMeta($i);
        echo "- {$col['name']}\n";
    }

    echo "\n";

    // Vérifier les colonnes de la table formations
    $stmt = $pdo->query("SELECT * FROM formations LIMIT 1");
    echo "Colonnes de la table formations:\n";
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $col = $stmt->getColumnMeta($i);
        echo "- {$col['name']}\n";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
