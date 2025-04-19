<?php
// Configuration de base
require_once __DIR__ . '/config.db.php';
if (!defined('BASE_PATH')) {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseDir = ($scriptDir === '/') ? '' : $scriptDir;
    define('BASE_PATH', $baseDir);
}
if (!defined('ROOT_PATH')) define('ROOT_PATH', __DIR__);

// Configuration des sessions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour obtenir l'URL de base
function getBasePath() {
    return BASE_PATH;
}

// Fonction pour obtenir le chemin racine du projet
function getRootPath() {
    return ROOT_PATH;
}

// Fonction pour obtenir l'URL complète
function getFullUrl($path) {
    $basePath = rtrim(BASE_PATH, '/');
    $path = ltrim($path, '/');
    return $basePath . '/' . $path;
}

// Fonction pour obtenir le chemin absolu d'un fichier
function getAbsolutePath($path) {
    return ROOT_PATH . '/' . ltrim($path, '/');
}

// Chemins de l'application
define('MODULES_PATH', ROOT_PATH . '/modules');
define('INCLUDES_PATH', ROOT_PATH . '/includes');

try {
    $pdo = getPDO();
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
