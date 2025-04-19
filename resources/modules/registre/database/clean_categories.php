<?php
require_once '../config.php';

try {
    // 1. Récupérer toutes les catégories
    $stmt = $pdo->query("SELECT * FROM register_categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
    // 2. Identifier les doublons par nom
    $seen = [];
    $duplicates = [];
    
    foreach ($categories as $category) {
        $name = strtolower($category['name']); // Normaliser le nom pour la comparaison
        if (isset($seen[$name])) {
            $duplicates[] = $category['id'];
        } else {
            $seen[$name] = $category['id'];
        }
    }
    
    // 3. Supprimer les doublons
    if (!empty($duplicates)) {
        // Mettre à jour les entrées qui utilisent les catégories en double
        foreach ($duplicates as $duplicate_id) {
            $original_id = $seen[strtolower($categories[array_search($duplicate_id, array_column($categories, 'id'))]['name'])];
            
            // Mettre à jour les entrées
            $stmt = $pdo->prepare("UPDATE register_entries SET category_id = ? WHERE category_id = ?");
            $stmt->execute([$original_id, $duplicate_id]);
            
            // Supprimer la catégorie en double
            $stmt = $pdo->prepare("DELETE FROM register_categories WHERE id = ?");
            $stmt->execute([$duplicate_id]);
            
            echo "Catégorie en double (ID: $duplicate_id) supprimée et entrées mises à jour vers ID: $original_id\n";
        }
        echo "\nNettoyage terminé avec succès !\n";
    } else {
        echo "Aucune catégorie en double trouvée.\n";
    }
    
} catch (PDOException $e) {
    die("Erreur lors du nettoyage des catégories : " . $e->getMessage());
}
