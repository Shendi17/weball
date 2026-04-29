<?php
require_once __DIR__ . '/../config/database.php';
$pdo = getPDO();

echo "=== Personnalités sans photo ===\n\n";
$result = $pdo->query('SELECT id, prenom, nom FROM personnalite WHERE photo IS NULL OR photo = ""')->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $p) {
    echo $p['id'] . ' - ' . trim($p['prenom'] . ' ' . $p['nom']) . "\n";
}

echo "\nTotal: " . count($result) . " personnalités sans photo\n";
