<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../includes/functions.php';
require_once dirname(__FILE__) . '/../../includes/db.php';

header('Content-Type: application/json');

try {
    // Test 1: Connexion à la base de données
    $pdo->query("SELECT 1");
    $tests['database_connection'] = true;
    
    // Test 2: Vérification de la table formations_formations
    $tables = $pdo->query("SHOW TABLES LIKE 'formations_formations'")->fetchAll();
    $tests['formations_table_exists'] = !empty($tables);
    
    if (!$tests['formations_table_exists']) {
        // Créer la table
        $sql = file_get_contents(__DIR__ . '/create_table.sql');
        $pdo->exec($sql);
        $tests['formations_table_created'] = true;
        
        // Insérer les données d'exemple
        $sql = file_get_contents(__DIR__ . '/sample_data.sql');
        $pdo->exec($sql);
        $tests['sample_data_inserted'] = true;
    }
    
    // Test 3: Compter les formations
    $stmt = $pdo->query("SELECT COUNT(*) FROM formations_formations");
    $count = $stmt->fetchColumn();
    $tests['formations_count'] = $count;
    
    // Test 4: Vérifier la table users et l'utilisateur admin
    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    $tests['users_table_exists'] = !empty($tables);
    
    if ($tests['users_table_exists']) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch();
        $tests['admin_user_exists'] = !empty($admin);
    }
    
    echo json_encode([
        'success' => true,
        'tests' => $tests,
        'message' => 'Tests effectués avec succès'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'tests' => $tests ?? [],
        'message' => 'Erreur lors des tests'
    ]);
}
?>
