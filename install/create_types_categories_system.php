<?php
/**
 * Création du système de types et catégories pour tous les modules
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getPDO();
    
    echo "=== Création du système Types & Catégories ===\n\n";
    
    // Liste des modules (25 modules, excluant cadran, journal, ecran)
    $modules = [
        'personnalite', 'autorite', 'annuaire', 'entite', 'localite',
        'instrument', 'office', 'plateforme', 'banque', 'reseau',
        'publication', 'archive', 'ecole', 'media', 'formation',
        'annonce', 'catalogue', 'marche', 'boutique', 'adhesion',
        'projet', 'carriere', 'discipline', 'concours', 'campagne'
    ];
    
    foreach ($modules as $module) {
        echo "--- Module: $module ---\n";
        
        // 1. Créer la table des types
        $typesTable = "{$module}_types";
        echo "Création table: $typesTable\n";
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS $typesTable (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            icon VARCHAR(50) DEFAULT 'circle',
            description TEXT,
            ordre INT DEFAULT 0,
            actif TINYINT(1) DEFAULT 1,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // 2. Créer la table des catégories
        $categoriesTable = "{$module}_categories";
        echo "Création table: $categoriesTable\n";
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS $categoriesTable (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type_id INT NOT NULL,
            nom VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            icon VARCHAR(50) DEFAULT 'tag',
            description TEXT,
            ordre INT DEFAULT 0,
            actif TINYINT(1) DEFAULT 1,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (type_id) REFERENCES $typesTable(id) ON DELETE CASCADE,
            UNIQUE KEY unique_type_slug (type_id, slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // 3. Ajouter les colonnes type_id et category_id à la table principale
        echo "Ajout colonnes type_id et category_id à: $module\n";
        
        try {
            $pdo->exec("ALTER TABLE $module 
                ADD COLUMN type_id INT NULL,
                ADD COLUMN category_id INT NULL,
                ADD FOREIGN KEY (type_id) REFERENCES $typesTable(id) ON DELETE SET NULL,
                ADD FOREIGN KEY (category_id) REFERENCES $categoriesTable(id) ON DELETE SET NULL");
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') === false) {
                echo "  Attention: " . $e->getMessage() . "\n";
            } else {
                echo "  Colonnes déjà existantes\n";
            }
        }
        
        echo "\n";
    }
    
    echo "=== Tables créées avec succès ! ===\n";
    
} catch (PDOException $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    exit(1);
}
