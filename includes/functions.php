<?php
/**
 * Fonctions utilitaires pour l'application WebAllOne
 */

if (!function_exists('isLoggedIn')) {
    /**
     * Vérifie si l'utilisateur est connecté
     * @return bool
     */
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('hasPermission')) {
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     * @param string $permission
     * @return bool
     */
    function hasPermission($permission) {
        return isset($_SESSION['permissions']) && in_array($permission, $_SESSION['permissions']);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Formate une date au format français
     * @param string $date
     * @param bool $withTime
     * @return string
     */
    function formatDate($date, $withTime = false) {
        if (empty($date)) {
            return '';
        }
        $format = $withTime ? 'd/m/Y H:i' : 'd/m/Y';
        return date($format, strtotime($date));
    }
}

if (!function_exists('generateCSRFToken')) {
    /**
     * Génère un token CSRF
     * @return string
     */
    function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCSRFToken')) {
    /**
     * Vérifie si le token CSRF est valide
     * @param string $token
     * @return bool
     */
    function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('cleanString')) {
    /**
     * Nettoie une chaîne de caractères
     * @param string $str
     * @return string
     */
    function cleanString($str) {
        if ($str === null) {
            return '';
        }
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('cleanInput')) {
    /**
     * Nettoie une entrée utilisateur
     * @param string $data
     * @return string
     */
    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirige vers une URL
     * @param string $url
     */
    function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('url')) {
    /**
     * Génère une URL absolue
     * @param string $path
     * @return string
     */
    function url($path) {
        return BASE_PATH . '/' . ltrim($path, '/');
    }
}

if (!function_exists('displayError')) {
    /**
     * Affiche un message d'erreur
     * @param string $message
     * @return string
     */
    function displayError($message) {
        return '<div class="alert alert-danger">' . cleanString($message) . '</div>';
    }
}

if (!function_exists('displaySuccess')) {
    /**
     * Affiche un message de succès
     * @param string $message
     * @return string
     */
    function displaySuccess($message) {
        return '<div class="alert alert-success">' . cleanString($message) . '</div>';
    }
}

if (!function_exists('isAjaxRequest')) {
    /**
     * Vérifie si une requête est en AJAX
     * @return bool
     */
    function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

if (!function_exists('sendJsonResponse')) {
    /**
     * Envoie une réponse JSON
     * @param array $data
     */
    function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('isValidImage')) {
    /**
     * Vérifie si un fichier est une image valide
     * @param array $file
     * @return bool
     */
    function isValidImage($file) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        return isset($file['type']) && in_array($file['type'], $allowed);
    }
}

if (!function_exists('generateUniqueFilename')) {
    /**
     * Génère un nom de fichier unique
     * @param string $originalName
     * @return string
     */
    function generateUniqueFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '.' . $extension;
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Formate la taille d'un fichier
     * @param int $bytes
     * @return string
     */
    function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

if (!function_exists('formatBytes')) {
    /**
     * Formate une taille en bytes
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('sanitizeFileName')) {
    /**
     * Nettoie un nom de fichier
     * @param string $fileName
     * @return string
     */
    function sanitizeFileName($fileName) {
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
        return $fileName;
    }
}

if (!function_exists('getConnection')) {
    /**
     * Établit une connexion à la base de données
     * @return PDO Instance de connexion PDO
     * @throws PDOException Si la connexion échoue
     */
    function getConnection() {
        static $pdo = null;
        
        if ($pdo === null) {
            $pdo = getPDO();
        }
        
        return $pdo;
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Formate un nombre avec séparateur de milliers
     * @param mixed $number Le nombre à formater
     * @param int $decimals Nombre de décimales
     * @return string Le nombre formaté
     */
    function formatNumber($number, $decimals = 0) {
        if ($number === null) {
            return '0';
        }
        return number_format($number, $decimals, ',', ' ');
    }
}

if (!function_exists('isEmpty')) {
    /**
     * Vérifie si une chaîne est vide
     * @param string $str La chaîne à vérifier
     * @return bool True si la chaîne est vide
     */
    function isEmpty($str) {
        return empty(trim((string)$str));
    }
}

if (!function_exists('generateId')) {
    /**
     * Génère un identifiant unique
     * @param string $prefix Préfixe de l'identifiant
     * @return string L'identifiant généré
     */
    function generateId($prefix = '') {
        return uniqid($prefix);
    }
}

if (!function_exists('hasRole')) {
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     * @param string $role Le rôle à vérifier
     * @return bool True si l'utilisateur a le rôle
     */
    function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Vérifie si l'utilisateur est un administrateur
     * @return bool
     */
    function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}

if (!function_exists('requireLogin')) {
    /**
     * Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
     * @param string $redirect_url URL de redirection après la connexion (optionnel)
     */
    function requireLogin($redirect_url = null) {
        if (!isLoggedIn()) {
            if ($redirect_url) {
                $_SESSION['redirect_after_login'] = $redirect_url;
            }
            header('Location: ' . BASE_PATH . '/login.php');
            exit;
        }
    }
}

?>
