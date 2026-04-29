<?php

require_once __DIR__ . '/../modules/auth/routes.php';

function handleRoutes() {
    $uri = $_SERVER['REQUEST_URI'];
    
    // DEBUG ACTIF - AVANT nettoyage
    $debugMode = (strpos($uri, 'debug=1') !== false);
    
    if ($debugMode) {
        echo "<div style='background:#000;color:#0f0;padding:20px;font-family:monospace;'>";
        echo "<h2>🔍 DEBUG ROUTER</h2>";
        echo "<strong>REQUEST_URI original:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI']) . "<br>";
    }
    
    // Nettoyer l'URI (supprimer les query strings)
    $uri = strtok($uri, '?');
    
    if ($debugMode) {
        echo "<strong>URI nettoyée:</strong> " . htmlspecialchars($uri) . "<br>";
        echo "</div>";
    }
    
    // Pour weball.local, pas de baseUri à supprimer
    // L'URI est déjà à la racine
    
    // Autoriser l'accès direct aux fichiers PHP à la racine (test, diagnostic, etc.)
    if (preg_match('#^/([a-zA-Z0-9_-]+\.php)$#', $uri, $matches)) {
        $phpFile = __DIR__ . '/../' . $matches[1];
        if (file_exists($phpFile)) {
            require_once $phpFile;
            return;
        }
    }

    // Si l'URI est vide ou '/', charger la page d'accueil
    if ($uri === '' || $uri === '/') {
        require_once __DIR__ . '/../templates/home.php';
        return;
    }

    // Routage pour les pages d'édition (ex: /personnalite_edit?id=1)
    if (preg_match('#^/([a-z_]+)_edit$#', $uri, $matches)) {
        $module = $matches[1];
        $editPage = __DIR__ . "/../pages/{$module}_edit.php";
        if (file_exists($editPage)) {
            require_once $editPage;
            return;
        }
    }

    // Routage pour les pages de détail (ex: /personnalite_detail?id=1)
    if (preg_match('#^/([a-z_]+)_detail$#', $uri, $matches)) {
        $module = $matches[1];
        $detailPage = __DIR__ . "/../pages/{$module}_detail.php";
        if (file_exists($detailPage)) {
            require_once $detailPage;
            return;
        }
    }

    // Routage automatique pour les sections
    $sections = [
        'cadran', 'journal', 'ecran', 'personnalite', 'autorite', 'annuaire', 'entite', 'localite',
        'instrument', 'office', 'plateforme', 'banque', 'reseau', 'publication', 'archive', 'ecole',
        'media', 'formation', 'annonce', 'catalogue', 'marche', 'boutique', 'adhesion', 'projet',
        'carriere', 'discipline', 'concours', 'campagne'
    ];
    
    $matched = false;
    foreach ($sections as $section) {
        if (preg_match("#^/{$section}(/.*)?$#", $uri)) {
            $matched = true;
            // TOUJOURS utiliser /pages (nouveau système)
            $pagePath = __DIR__ . "/../pages/{$section}.php";
            
            if ($debugMode) {
                echo "<div style='background:#000;color:#0f0;padding:20px;font-family:monospace;'>";
                echo "<strong>✓ MATCH trouvé pour section:</strong> $section<br>";
                echo "<strong>Fichier recherché:</strong> $pagePath<br>";
                echo "<strong>Existe:</strong> " . (file_exists($pagePath) ? '✓ OUI' : '✗ NON') . "<br>";
                echo "</div>";
            }
            
            if (file_exists($pagePath)) {
                require_once $pagePath;
                return;
            }
            // NE PLUS utiliser /resources/sections (ancien système désactivé)
        }
    }
    
    if ($debugMode && !$matched) {
        echo "<div style='background:#f00;color:#fff;padding:20px;font-family:monospace;'>";
        echo "<strong>✗ Aucun match trouvé pour URI:</strong> $uri";
        echo "</div>";
    }

    // Essayer de gérer les routes d'authentification
    if (function_exists('handleAuthRoutes') && handleAuthRoutes($uri)) {
        return;
    }

    // Si aucune route n'a été trouvée, afficher une erreur 404
    http_response_code(404);
    require_once __DIR__ . '/../templates/404.php';
}
