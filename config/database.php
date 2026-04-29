<?php
// Définir les constantes seulement si elles ne sont pas déjà définies
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'weballone');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

// Fonction pour obtenir une connexion PDO (avec protection contre la redéclaration)
if (!function_exists('getPDO')) {
    function getPDO($forceReconnect = false) {
        static $pdo = null;
        
        if ($pdo === null || $forceReconnect) {
            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                        PDO::ATTR_TIMEOUT => 30,
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        
        return $pdo;
    }
}

// Créer une instance globale pour la compatibilité
try {
    $pdo = getPDO();
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}