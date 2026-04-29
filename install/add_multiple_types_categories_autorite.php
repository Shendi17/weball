<?php
/**
 * Ajout des colonnes pour types et catégories multiples
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout colonnes types/catégories multiples ===\n\n";

try {
    // Ajouter types_ids pour stocker plusieurs types
    $pdo->exec("ALTER TABLE autorite ADD COLUMN types_ids TEXT NULL COMMENT 'JSON array of type IDs'");
    echo "✓ Colonne types_ids ajoutée\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "⚠ Colonne types_ids existe déjà\n";
    } else {
        echo "✗ Erreur types_ids: " . $e->getMessage() . "\n";
    }
}

try {
    // Ajouter categories_ids pour stocker plusieurs catégories
    $pdo->exec("ALTER TABLE autorite ADD COLUMN categories_ids TEXT NULL COMMENT 'JSON array of category IDs'");
    echo "✓ Colonne categories_ids ajoutée\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "⚠ Colonne categories_ids existe déjà\n";
    } else {
        echo "✗ Erreur categories_ids: " . $e->getMessage() . "\n";
    }
}

// Migrer les données existantes
echo "\n=== Migration des données ===\n";

$autorites = $pdo->query("SELECT id, type_id, category_id FROM autorite WHERE type_id IS NOT NULL OR category_id IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);

foreach ($autorites as $autorite) {
    $typesIds = [];
    $categoriesIds = [];
    
    if (!empty($autorite['type_id'])) {
        $typesIds[] = (int)$autorite['type_id'];
    }
    
    if (!empty($autorite['category_id'])) {
        $categoriesIds[] = (int)$autorite['category_id'];
    }
    
    $stmt = $pdo->prepare("UPDATE autorite SET types_ids = ?, categories_ids = ? WHERE id = ?");
    $stmt->execute([
        !empty($typesIds) ? json_encode($typesIds) : null,
        !empty($categoriesIds) ? json_encode($categoriesIds) : null,
        $autorite['id']
    ]);
    
    echo "✓ Autorité #{$autorite['id']} migrée\n";
}

echo "\n=== Terminé ! ===\n";
