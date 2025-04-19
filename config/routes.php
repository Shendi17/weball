<?php
/**
 * Configuration des routes de l'application
 */

$router = new Router();

// Page d'accueil
$router->get('/', function() {
    require ROOT_PATH . '/modules/home/index.php';
});

// Routes d'authentification
$router->get('/login', function() {
    require ROOT_PATH . '/modules/auth/login.php';
});
$router->post('/login', function() {
    require ROOT_PATH . '/modules/auth/login_process.php';
});
$router->get('/logout', function() {
    require ROOT_PATH . '/modules/auth/logout.php';
});

// Routes des formations
$router->get('/formations', function() {
    require ROOT_PATH . '/modules/formations/index.php';
});
$router->get('/formations/{id}', function($id) {
    $_GET['id'] = $id;
    require ROOT_PATH . '/modules/formations/voir.php';
});
$router->post('/formations/ajouter', function() {
    require ROOT_PATH . '/modules/formations/ajouter.php';
});
$router->post('/formations/modifier/{id}', function($id) {
    $_GET['id'] = $id;
    require ROOT_PATH . '/modules/formations/modifier.php';
});
$router->post('/formations/supprimer/{id}', function($id) {
    $_GET['id'] = $id;
    require ROOT_PATH . '/modules/formations/supprimer.php';
});

// Routes du magasin
$router->get('/magasin', function() {
    require ROOT_PATH . '/modules/magasin/index.php';
});
$router->get('/magasin/commandes', function() {
    require ROOT_PATH . '/modules/magasin/commandes/liste.php';
});
$router->get('/magasin/fournisseurs', function() {
    require ROOT_PATH . '/modules/magasin/fournisseurs/liste.php';
});

// Route de profil
$router->get('/profile', function() {
    require ROOT_PATH . '/modules/profile/index.php';
});

// Route de recherche
$router->get('/search', function() {
    require ROOT_PATH . '/modules/search/index.php';
});

// Gestion des erreurs 404
$router->notFound(function() {
    header("HTTP/1.0 404 Not Found");
    require ROOT_PATH . '/templates/error/404.php';
});
