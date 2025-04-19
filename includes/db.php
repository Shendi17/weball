<?php
/**
 * Configuration et connexion à la base de données
 */

require_once __DIR__ . '/../config.db.php';
$pdo = getPDO();

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->exec("SET NAMES utf8mb4");
    error_log("Connexion à la base de données réussie");
    
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    throw new Exception("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
}
