<?php
require_once __DIR__ . '/../config/database.php';
$pdo = getPDO();
$cols = $pdo->query('DESCRIBE autorite')->fetchAll(PDO::FETCH_ASSOC);
echo "Structure de la table autorite:\n";
foreach($cols as $col) {
    echo "- {$col['Field']} ({$col['Type']})\n";
}
