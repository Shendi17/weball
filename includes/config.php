<?php
// Configuration de base
define('BASE_PATH', '');
define('ROOT_PATH', dirname(__DIR__));

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
