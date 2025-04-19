<?php
/**
 * Point d'entrée principal de l'application
 */

// Définir le chemin de base
define('ROOT_PATH', dirname(__DIR__));

// Activer l'affichage des erreurs en développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger l'autoloader et la configuration
require_once ROOT_PATH . '/config/bootstrap.php';

// Initialiser la session
session_start();

// Router la requête
try {
    $router = new Router();
    $router->dispatch();
} catch (Exception $e) {
    if (DEBUG_MODE) {
        echo "<pre>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString();
        echo "</pre>";
    } else {
        error_log($e->getMessage());
        include ROOT_PATH . '/templates/error/500.php';
    }
}