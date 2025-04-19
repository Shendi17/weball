<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__DIR__) . '/config.db.php';

echo "Test de connexion à la base de données...\n";

try {
     $pdo = getPDO();
     echo "Connexion établie avec succès !\n";
     
     $stmt = $pdo->query('SHOW TABLES');
     echo "Tables dans la base de données:\n";
     while ($row = $stmt->fetch()) {
         print_r($row);
     }
     
} catch (\PDOException $e) {
     die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
