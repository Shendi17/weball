<?php
require_once __DIR__ . '/../../config.php';

// Configuration des outils externes
define('OPENWEATHERMAP_API_KEY', ''); // Clé API OpenWeatherMap
define('EXCHANGERATE_API_KEY', ''); // Clé API ExchangeRate
define('GOOGLE_TRANSLATE_API_KEY', ''); // Clé API Google Translate

// Configuration des options par défaut
$config = [
    'default_view' => 'grid', // 'grid' ou 'list'
    'items_per_page' => 12,
    'cache_duration' => 3600, // Durée du cache en secondes
    'allowed_apis' => [
        'openweathermap' => true,
        'exchangerate' => true,
        'googletranslate' => true
    ]
];

// Configuration des limites d'utilisation
$usage_limits = [
    'weather' => [
        'daily_limit' => 1000,
        'rate_limit' => 60 // appels par minute
    ],
    'currency' => [
        'daily_limit' => 100,
        'rate_limit' => 30
    ],
    'translate' => [
        'daily_limit' => 500,
        'rate_limit' => 100
    ]
];

// Configuration des options de cache
$cache_options = [
    'weather' => [
        'duration' => 1800, // 30 minutes
        'enabled' => true
    ],
    'currency' => [
        'duration' => 3600, // 1 heure
        'enabled' => true
    ],
    'translate' => [
        'duration' => 86400, // 24 heures
        'enabled' => true
    ]
];

// Configuration des options de sécurité
$security_options = [
    'rate_limiting' => true,
    'api_key_encryption' => true,
    'request_validation' => true,
    'cors_enabled' => false,
    'allowed_origins' => []
];

// Messages d'erreur personnalisés
$error_messages = [
    'api_key_missing' => 'Clé API manquante. Veuillez configurer la clé API dans les paramètres.',
    'rate_limit_exceeded' => 'Limite de requêtes atteinte. Veuillez réessayer plus tard.',
    'invalid_request' => 'Requête invalide. Veuillez vérifier les paramètres.',
    'service_unavailable' => 'Service temporairement indisponible.',
    'network_error' => 'Erreur de connexion au service externe.'
];

// Fonction pour vérifier si une API est disponible
function isApiAvailable($api_name) {
    global $config;
    return isset($config['allowed_apis'][strtolower($api_name)]) && 
           $config['allowed_apis'][strtolower($api_name)] === true;
}

// Fonction pour vérifier les limites d'utilisation
function checkUsageLimit($tool_name) {
    global $usage_limits;
    if (!isset($usage_limits[$tool_name])) {
        return true;
    }
    
    $limit = $usage_limits[$tool_name];
    $current_usage = getCurrentUsage($tool_name);
    
    return $current_usage < $limit['daily_limit'];
}

// Fonction pour obtenir l'utilisation actuelle
function getCurrentUsage($tool_name) {
    // TODO: Implémenter le suivi de l'utilisation
    return 0;
}

// Fonction pour gérer le cache
function getCacheKey($tool_name, $params) {
    return $tool_name . '_' . md5(serialize($params));
}

// Fonction pour vérifier si une requête est en cache
function getFromCache($cache_key) {
    // TODO: Implémenter la logique de cache
    return false;
}

// Fonction pour sauvegarder dans le cache
function saveToCache($cache_key, $data, $duration) {
    // TODO: Implémenter la sauvegarde en cache
    return true;
}

// Fonction pour nettoyer les entrées
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction pour vérifier si une URL est valide
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Fonction pour extraire le nom de domaine d'une URL
function getDomainFromUrl($url) {
    return parse_url($url, PHP_URL_HOST);
}

// Fonction pour générer une icône basée sur le nom de domaine
function getIconForUrl($url) {
    $domain = getDomainFromUrl($url);
    
    // Liste des domaines connus et leurs icônes
    $known_domains = [
        'github.com' => 'fab fa-github',
        'gitlab.com' => 'fab fa-gitlab',
        'bitbucket.org' => 'fab fa-bitbucket',
        'stackoverflow.com' => 'fab fa-stack-overflow',
        'youtube.com' => 'fab fa-youtube',
        'google.com' => 'fab fa-google',
        'facebook.com' => 'fab fa-facebook',
        'twitter.com' => 'fab fa-twitter',
        'linkedin.com' => 'fab fa-linkedin'
    ];
    
    // Retourne l'icône correspondante ou une icône par défaut
    return isset($known_domains[$domain]) ? $known_domains[$domain] : 'fas fa-external-link-alt';
}
?>
