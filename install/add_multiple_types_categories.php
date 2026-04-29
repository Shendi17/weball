<?php
/**
 * Ajout des colonnes pour types et catégories multiples
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout Types/Catégories Multiples ===\n\n";

// Ajouter les colonnes pour sélection multiple
$columns = [
    "types_ids TEXT NULL COMMENT 'IDs des types (JSON array)'",
    "categories_ids TEXT NULL COMMENT 'IDs des catégories (JSON array)'",
    "liens_externes TEXT NULL COMMENT 'Liens externes supplémentaires (JSON)'"
];

echo "Ajout des colonnes...\n";
foreach ($columns as $column) {
    try {
        $pdo->exec("ALTER TABLE personnalite ADD COLUMN $column");
        $colName = explode(' ', $column)[0];
        echo "  ✓ Colonne ajoutée: $colName\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            $colName = explode(' ', $column)[0];
            echo "  ⚠ Colonne existe déjà: $colName\n";
        } else {
            echo "  ✗ Erreur: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Structure enrichie ! ===\n";
