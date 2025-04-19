<?php
// Configuration de base
define('BASE_PATH', '/weball');
define('ROOT_PATH', dirname(__DIR__));

// Configuration de la base de données (à ajouter plus tard si nécessaire)
define('DB_HOST', 'localhost');
define('DB_NAME', 'weballone');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuration des sessions
session_start();

// Fonction pour obtenir l'URL de base
function getBasePath() {
    return BASE_PATH;
}

// Fonction pour obtenir le chemin racine du projet
function getRootPath() {
    return ROOT_PATH;
}
