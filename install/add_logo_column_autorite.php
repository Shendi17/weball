<?php
/**
 * Ajout de la colonne logo à la table autorite
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout colonne logo à autorite ===\n\n";

try {
    $pdo->exec("ALTER TABLE autorite ADD COLUMN logo VARCHAR(500) NULL AFTER nom");
    echo "✓ Colonne logo ajoutée\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "⚠ Colonne logo existe déjà\n";
    } else {
        echo "✗ Erreur: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Terminé ! ===\n";
