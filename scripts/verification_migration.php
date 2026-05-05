<?php
/**
 * Script de vérification de la migration
 * Compare les données exportées avec ce qui devrait être importé
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Vérification de la Migration WebAll ===\n\n";

try {
    $pdo = getPDO();
    
    // Vérification des personnalités
    echo "📊 Vérification des Personnalités :\n";
    $personalitesCount = $pdo->query("SELECT COUNT(*) FROM personnalite")->fetchColumn();
    echo "  - Personnalités dans la base : $personalitesCount\n";
    
    $typesPersonnalites = $pdo->query("SELECT COUNT(*) FROM personnalite_types")->fetchColumn();
    echo "  - Types de personnalités : $typesPersonnalites\n";
    
    $categoriesPersonnalites = $pdo->query("SELECT COUNT(*) FROM personnalite_categories")->fetchColumn();
    echo "  - Catégories de personnalités : $categoriesPersonnalites\n";
    
    // Détail des types de personnalités
    echo "\n📋 Types de personnalités trouvés :\n";
    $types = $pdo->query("SELECT id, nom, slug FROM personnalite_types ORDER BY id")->fetchAll();
    foreach ($types as $type) {
        $count = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE type_id = {$type['id']}")->fetchColumn();
        echo "  - {$type['nom']} ({$type['slug']}) : $count personnalités\n";
    }
    
    // Vérification des autorités
    echo "\n📊 Vérification des Autorités :\n";
    $autoritesCount = $pdo->query("SELECT COUNT(*) FROM autorite")->fetchColumn();
    echo "  - Autorités dans la base : $autoritesCount\n";
    
    $typesAutorites = $pdo->query("SELECT COUNT(*) FROM autorite_types")->fetchColumn();
    echo "  - Types d'autorités : $typesAutorites\n";
    
    $categoriesAutorites = $pdo->query("SELECT COUNT(*) FROM autorite_categories")->fetchColumn();
    echo "  - Catégories d'autorités : $categoriesAutorites\n";
    
    // Détail des types d'autorités
    echo "\n📋 Types d'autorités trouvés :\n";
    $types = $pdo->query("SELECT id, nom, slug FROM autorite_types ORDER BY id")->fetchAll();
    foreach ($types as $type) {
        $count = $pdo->query("SELECT COUNT(*) FROM autorite WHERE type_id = {$type['id']}")->fetchColumn();
        echo "  - {$type['nom']} ({$type['slug']}) : $count autorités\n";
    }
    
    // Vérification des modules
    echo "\n📊 Vérification des Modules :\n";
    $modules = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $typeTables = [];
    $categoryTables = [];
    $dataTables = [];
    
    foreach ($modules as $table) {
        if (strpos($table, '_types') !== false) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            $moduleName = str_replace('_types', '', $table);
            $typeTables[$moduleName] = $count;
        } elseif (strpos($table, '_categories') !== false) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            $moduleName = str_replace('_categories', '', $table);
            $categoryTables[$moduleName] = $count;
        } elseif (in_array($table, ['personnalite', 'autorite', 'entite', 'formation', 'publication', 'projet'])) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            $dataTables[$table] = $count;
        }
    }
    
    echo "\n📋 Modules avec types et catégories :\n";
    foreach ($typeTables as $module => $typeCount) {
        $catCount = $categoryTables[$module] ?? 0;
        $dataCount = $dataTables[$module] ?? 0;
        echo "  - $module : $typeCount types, $catCount catégories";
        if ($dataCount > 0) echo ", $dataCount données";
        echo "\n";
    }
    
    // Vérification des utilisateurs
    echo "\n📊 Vérification des Utilisateurs :\n";
    $usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "  - Utilisateurs dans la base : $usersCount\n";
    
    $users = $pdo->query("SELECT username, role FROM users")->fetchAll();
    foreach ($users as $user) {
        echo "  - {$user['username']} ({$user['role']})\n";
    }
    
    // Vérification des données problématiques
    echo "\n🔍 Vérification des données problématiques :\n";
    
    // Personnalités sans type
    $sansType = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE type_id IS NULL OR type_id = 0")->fetchColumn();
    if ($sansType > 0) {
        echo "  ⚠️  $sansType personnalités sans type\n";
    }
    
    // Autorités sans type
    $sansType = $pdo->query("SELECT COUNT(*) FROM autorite WHERE type_id IS NULL OR type_id = 0")->fetchColumn();
    if ($sansType > 0) {
        echo "  ⚠️  $sansType autorités sans type\n";
    }
    
    // Données vides importantes
    $photosVides = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE photo IS NULL OR photo = ''")->fetchColumn();
    if ($photosVides > 0) {
        echo "  ⚠️  $photosVides personnalités sans photo\n";
    }
    
    $biosVides = $pdo->query("SELECT COUNT(*) FROM personnalite WHERE biographie IS NULL OR biographie = ''")->fetchColumn();
    if ($biosVides > 0) {
        echo "  ⚠️  $biosVides personnalités sans biographie\n";
    }
    
    // Résumé
    echo "\n=== RÉSUMÉ DE LA VÉRIFICATION ===\n";
    echo "✅ Personnalités : $personalitesCount (avec $typesPersonnalites types)\n";
    echo "✅ Autorités : $autoritesCount (avec $typesAutorites types)\n";
    echo "✅ Modules : " . count($typeTables) . " modules avec types/catégories\n";
    echo "✅ Utilisateurs : $usersCount\n";
    
    echo "\n🎯 Si les chiffres ci-dessus correspondent à vos attentes, la migration est réussie !\n";
    echo "❌ Sinon, vérifiez les logs d'importation ou relancez l'importation.\n";
    
} catch (PDOException $e) {
    die("❌ Erreur de vérification : " . $e->getMessage() . "\n");
}
?>
