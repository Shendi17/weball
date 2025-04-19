<?php

require_once __DIR__ . '/../modules/auth/routes.php';

function handleRoutes() {
    $uri = $_SERVER['REQUEST_URI'];
    $baseUri = '/weball';
    
    // Supprimer le baseUri de l'URI pour le traitement
    if (strpos($uri, $baseUri) === 0) {
        $uri = substr($uri, strlen($baseUri));
    }

    // Si l'URI est vide ou '/', charger la page d'accueil
    if ($uri === '' || $uri === '/') {
        require_once __DIR__ . '/../templates/home.php';
        return;
    }

    // Routage automatique pour les sections
    $sections = [
        'cadran', 'journal', 'ecran', 'personnalite', 'autorite', 'annuaire', 'entite', 'localite',
        'instrument', 'office', 'plateforme', 'banque', 'reseau', 'publication', 'archive', 'ecole',
        'media', 'formation', 'annonce', 'catalogue', 'marche', 'boutique', 'adhesion', 'projet',
        'carriere', 'discipline', 'concours', 'campagne'
    ];
    foreach ($sections as $section) {
        if (preg_match("#^/{$section}(/.*)?$#", $uri)) {
            $sectionPath = __DIR__ . "/../resources/sections/{$section}/index.php";
            if (file_exists($sectionPath)) {
                require_once $sectionPath;
                return;
            }
        }
    }

    // Essayer de gérer les routes d'authentification
    if (function_exists('handleAuthRoutes') && handleAuthRoutes($uri)) {
        return;
    }

    // Si aucune route n'a été trouvée, afficher une erreur 404
    http_response_code(404);
    require_once __DIR__ . '/../templates/404.php';
}
