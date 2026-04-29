<?php
require_once __DIR__ . '/../config/database.php';
$pdo = getPDO();
$types = $pdo->query('SELECT id, slug FROM autorite_types')->fetchAll(PDO::FETCH_KEY_PAIR);
print_r($types);
