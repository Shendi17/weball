<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM articles LIMIT 1");
    $columns = array();
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $col = $stmt->getColumnMeta($i);
        echo $col['name'] . "\n";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
