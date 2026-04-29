<?php
/**
 * Création du système de types et catégories pour Autorité
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Création Types et Catégories Autorité ===\n\n";

// 1. Créer la table des types
echo "Création de la table autorite_types...\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS autorite_types (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        icon VARCHAR(50) DEFAULT 'building',
        description TEXT,
        ordre INT DEFAULT 0,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "✓ Table autorite_types créée\n\n";

// 2. Créer la table des catégories
echo "Création de la table autorite_categories...\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS autorite_categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        type_id INT NOT NULL,
        nom VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL,
        icon VARCHAR(50) DEFAULT NULL,
        description TEXT,
        ordre INT DEFAULT 0,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (type_id) REFERENCES autorite_types(id) ON DELETE CASCADE,
        UNIQUE KEY unique_category (type_id, slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "✓ Table autorite_categories créée\n\n";

// 3. Ajouter les colonnes type_id et category_id à la table autorite
echo "Ajout des colonnes type_id et category_id...\n";
try {
    $pdo->exec("ALTER TABLE autorite ADD COLUMN type_id INT NULL AFTER id");
    echo "✓ Colonne type_id ajoutée\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "⚠ Colonne type_id existe déjà\n";
    }
}

try {
    $pdo->exec("ALTER TABLE autorite ADD COLUMN category_id INT NULL AFTER type_id");
    echo "✓ Colonne category_id ajoutée\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "⚠ Colonne category_id existe déjà\n";
    }
}

// 4. Ajouter les clés étrangères
try {
    $pdo->exec("ALTER TABLE autorite ADD FOREIGN KEY (type_id) REFERENCES autorite_types(id) ON DELETE SET NULL");
    echo "✓ Clé étrangère type_id ajoutée\n";
} catch (PDOException $e) {
    echo "⚠ Clé étrangère type_id existe déjà\n";
}

try {
    $pdo->exec("ALTER TABLE autorite ADD FOREIGN KEY (category_id) REFERENCES autorite_categories(id) ON DELETE SET NULL");
    echo "✓ Clé étrangère category_id ajoutée\n";
} catch (PDOException $e) {
    echo "⚠ Clé étrangère category_id existe déjà\n";
}

echo "\n=== Structure créée avec succès ! ===\n";
