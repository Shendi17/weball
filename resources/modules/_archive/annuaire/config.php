<?php
// Utilisation de la config centrale PDO
require_once dirname(__DIR__,3) . '/config.db.php';

// Chemin de base pour l'application
define('BASE_PATH', '/weball');

// Connexion à la base de données
try {
    $pdo = getPDO(); // Utilise la fonction centrale
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
