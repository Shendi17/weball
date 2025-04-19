<?php

function handleAuthRoutes($uri) {
    switch ($uri) {
        case '/profile':
            require_once __DIR__ . '/profile.php';
            break;
        case '/parametres':
            require_once __DIR__ . '/settings.php';
            break;
        case '/logout':
            session_start();
            session_destroy();
            header('Location: /weball/login');
            exit;
        default:
            return false;
    }
    return true;
}
