<?php
/**
 * Script de migration complète pour WebAll
 * Exporte toutes les données nécessaires pour la migration
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Migration Complète WebAll ===\n\n";

try {
    $pdo = getPDO();
    
    // 1. Export de la structure complète
    echo "1. Export de la structure des tables...\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $structureSql = "-- WebAll Database Structure\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    
    foreach ($tables as $table) {
        $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $structureSql .= $createTable['Create Table'] . ";\n\n";
    }
    
    file_put_contents(__DIR__ . '/exports/structure.sql', $structureSql);
    echo "✓ Structure exportée dans exports/structure.sql\n";
    
    // 2. Export des données de personnalités
    echo "\n2. Export des personnalités...\n";
    $personalitiesSql = "-- Personalities Data\n";
    
    // Données de base
    $personalities = $pdo->query("SELECT * FROM personnalite")->fetchAll();
    foreach ($personalities as $p) {
        $fields = array_keys($p);
        $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $p);
        $personalitiesSql .= "INSERT INTO personnalite (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    
    // Types et catégories
    $personalityTypes = $pdo->query("SELECT * FROM personnalite_types")->fetchAll();
    foreach ($personalityTypes as $type) {
        $fields = array_keys($type);
        $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $type);
        $personalitiesSql .= "INSERT INTO personnalite_types (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    
    $personalityCategories = $pdo->query("SELECT * FROM personnalite_categories")->fetchAll();
    foreach ($personalityCategories as $cat) {
        $fields = array_keys($cat);
        $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $cat);
        $personalitiesSql .= "INSERT INTO personnalite_categories (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    
    file_put_contents(__DIR__ . '/exports/personalities_data.sql', $personalitiesSql);
    echo "✓ Personnalités exportées dans exports/personalities_data.sql\n";
    
    // 3. Export des autorités
    echo "\n3. Export des autorités...\n";
    $authoritiesSql = "-- Authorities Data\n";
    
    $authorities = $pdo->query("SELECT * FROM autorite")->fetchAll();
    foreach ($authorities as $a) {
        $fields = array_keys($a);
        $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $a);
        $authoritiesSql .= "INSERT INTO autorite (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    
    // Types et catégories d'autorités
    $authorityTypes = $pdo->query("SELECT * FROM autorite_types")->fetchAll();
    foreach ($authorityTypes as $type) {
        $fields = array_keys($type);
        $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $type);
        $authoritiesSql .= "INSERT INTO autorite_types (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    
    $authorityCategories = $pdo->query("SELECT * FROM autorite_categories")->fetchAll();
    foreach ($authorityCategories as $cat) {
        $fields = array_keys($cat);
        $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $cat);
        $authoritiesSql .= "INSERT INTO autorite_categories (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    
    file_put_contents(__DIR__ . '/exports/authorities_data.sql', $authoritiesSql);
    echo "✓ Autorités exportées dans exports/authorities_data.sql\n";
    
    // 4. Export de tous les types et catégories
    echo "\n4. Export de tous les types et catégories...\n";
    $typesCategoriesSql = "-- All Types and Categories\n";
    
    foreach ($tables as $table) {
        if (strpos($table, '_types') !== false || strpos($table, '_categories') !== false) {
            $data = $pdo->query("SELECT * FROM `$table`")->fetchAll();
            foreach ($data as $row) {
                $fields = array_keys($row);
                $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $row);
                $typesCategoriesSql .= "INSERT INTO `$table` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
        }
    }
    
    file_put_contents(__DIR__ . '/exports/types_categories.sql', $typesCategoriesSql);
    echo "✓ Types et catégories exportés dans exports/types_categories.sql\n";
    
    // 5. Export des données utilisateur
    echo "\n5. Export des utilisateurs...\n";
    $usersSql = "-- Users Data\n";
    $users = $pdo->query("SELECT * FROM users")->fetchAll();
    foreach ($users as $user) {
        $fields = array_keys($user);
        $values = array_map(function($v) { return "'" . addslashes($v ?? '') . "'"; }, $user);
        $usersSql .= "INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    file_put_contents(__DIR__ . '/exports/users_data.sql', $usersSql);
    echo "✓ Utilisateurs exportés dans exports/users_data.sql\n";
    
    // 6. Création du script d'importation
    echo "\n6. Création du script d'importation...\n";
    $importScript = '<?php
/**
 * Script d\'importation pour WebAll
 * À exécuter sur le nouveau serveur après installation
 */

require_once __DIR__ . "/../config/database.php";

echo "=== Importation WebAll ===\\n\\n";

try {
    $pdo = getPDO();
    
    // Importer la structure
    echo "1. Importation de la structure...\\n";
    $structure = file_get_contents(__DIR__ . "/structure.sql");
    $statements = array_filter(array_map("trim", explode(";", $structure)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
    echo "✓ Structure importée\\n";
    
    // Importer les données
    $dataFiles = [
        "users_data.sql",
        "personalities_data.sql", 
        "authorities_data.sql",
        "types_categories.sql"
    ];
    
    foreach ($dataFiles as $file) {
        echo "2. Importation de $file...\\n";
        $data = file_get_contents(__DIR__ . "/$file");
        $statements = array_filter(array_map("trim", explode(";", $data)));
        
        foreach ($statements as $stmt) {
            if (!empty($stmt)) {
                $pdo->exec($stmt);
            }
        }
        echo "✓ $file importé\\n";
    }
    
    echo "\\n🎉 Importation terminée avec succès !\\n";
    
} catch (PDOException $e) {
    die("❌ Erreur d\'importation : " . $e->getMessage() . "\\n");
}
?>';
    
    file_put_contents(__DIR__ . '/exports/import_complete.php', $importScript);
    echo "✓ Script d\'importation créé dans exports/import_complete.php\n";
    
    // 7. Création d'un rapport
    echo "\n7. Génération du rapport de migration...\n";
    $report = [
        'date' => date('Y-m-d H:i:s'),
        'tables_count' => count($tables),
        'personalities_count' => count($personalities),
        'authorities_count' => count($authorities),
        'users_count' => count($users),
        'exported_files' => [
            'structure.sql',
            'personalities_data.sql',
            'authorities_data.sql', 
            'types_categories.sql',
            'users_data.sql',
            'import_complete.php'
        ]
    ];
    
    file_put_contents(__DIR__ . '/exports/migration_report.json', json_encode($report, JSON_PRETTY_PRINT));
    echo "✓ Rapport généré dans exports/migration_report.json\n";
    
    echo "\n=== Migration terminée avec succès ! ===\n";
    echo "📁 Fichiers générés dans le dossier 'exports/' :\n";
    echo "  - structure.sql (structure complète)\n";
    echo "  - personalities_data.sql (personnalités et types)\n";
    echo "  - authorities_data.sql (autorités et types)\n";
    echo "  - types_categories.sql (tous les types et catégories)\n";
    echo "  - users_data.sql (utilisateurs)\n";
    echo "  - import_complete.php (script d\'importation)\n";
    echo "  - migration_report.json (rapport de migration)\n";
    
} catch (PDOException $e) {
    die("❌ Erreur de migration : " . $e->getMessage() . "\n");
}
?>
