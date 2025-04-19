<?php
// Configuration de base
define('BASE_PATH', dirname(__DIR__));
define('DEBUG_MODE', true);

// Configuration de la base de données
require_once BASE_PATH . '/config/database.php';

// Autoloader
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/classes/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});