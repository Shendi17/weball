<?php
/**
 * Ajout de colonnes pour enrichir les autorités
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout colonnes autorités ===\n\n";

$columns = [
    'adresse' => 'VARCHAR(500) NULL',
    'telephone' => 'VARCHAR(50) NULL',
    'email' => 'VARCHAR(255) NULL',
    'effectifs' => 'VARCHAR(100) NULL',
    'chiffre_affaires' => 'VARCHAR(100) NULL',
    'date_creation_org' => 'VARCHAR(100) NULL',
    'dirigeant' => 'VARCHAR(255) NULL',
    'secteur' => 'VARCHAR(255) NULL',
    'description_longue' => 'TEXT NULL',
    'missions' => 'TEXT NULL',
    'realisations' => 'TEXT NULL',
    'partenaires' => 'TEXT NULL',
    'reseaux_sociaux' => 'TEXT NULL',
    'actualites' => 'TEXT NULL'
];

foreach ($columns as $column => $type) {
    try {
        $pdo->exec("ALTER TABLE autorite ADD COLUMN $column $type");
        echo "✓ Colonne $column ajoutée\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠ Colonne $column existe déjà\n";
        } else {
            echo "✗ Erreur $column: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Terminé ! ===\n";
