<?php
/**
 * Script d'importation pour WebAll
 * À exécuter sur le nouveau serveur après installation
 */

require_once __DIR__ . "/../config/database.php";

echo "=== Importation WebAll ===\n\n";

try {
    $pdo = getPDO();
    
    // Importer la structure
    echo "1. Importation de la structure...\n";
    $structure = file_get_contents(__DIR__ . "/structure.sql");
    $statements = array_filter(array_map("trim", explode(";", $structure)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
    echo "✓ Structure importée\n";
    
    // Importer les données
    $dataFiles = [
        "users_data.sql",
        "personalities_data.sql", 
        "authorities_data.sql",
        "types_categories.sql"
    ];
    
    foreach ($dataFiles as $file) {
        echo "2. Importation de $file...\n";
        $data = file_get_contents(__DIR__ . "/$file");
        $statements = array_filter(array_map("trim", explode(";", $data)));
        
        foreach ($statements as $stmt) {
            if (!empty($stmt)) {
                $pdo->exec($stmt);
            }
        }
        echo "✓ $file importé\n";
    }
    
    echo "\n🎉 Importation terminée avec succès !\n";
    
} catch (PDOException $e) {
    die("❌ Erreur d'importation : " . $e->getMessage() . "\n");
}
?>