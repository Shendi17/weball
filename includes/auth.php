<?php
/**
 * Fonctions d'authentification
 */

if (!function_exists('checkPermission')) {
    /**
     * Vérifie si l'utilisateur a la permission requise
     * @param string $permission
     * @return bool
     */
    function checkPermission($permission) {
        if (!isset($_SESSION['permissions']) || !in_array($permission, $_SESSION['permissions'])) {
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }
        return true;
    }
}

if (!function_exists('login')) {
    /**
     * Connecte l'utilisateur
     * @param array $user
     */
    function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_name'] = $user['full_name'] ?? $user['username'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        
        // Gestion des permissions
        if (isset($user['permissions'])) {
            // Si les permissions sont stockées en JSON
            if ($permissions = json_decode($user['permissions'], true)) {
                $_SESSION['permissions'] = $permissions;
            }
            // Si les permissions sont stockées en CSV
            else {
                $_SESSION['permissions'] = array_filter(array_map('trim', explode(',', $user['permissions'])));
            }
        }
        // Permissions par défaut basées sur le rôle
        else {
            $_SESSION['permissions'] = ['user'];
            if ($user['role'] === 'admin') {
                $_SESSION['permissions'][] = 'admin';
            }
        }
        
        error_log("Auth - Login successful for user: " . $user['username']);
        error_log("Auth - User role: " . $_SESSION['user_role']);
        error_log("Auth - Permissions set: " . print_r($_SESSION['permissions'], true));
    }
}

if (!function_exists('logout')) {
    /**
     * Déconnecte l'utilisateur
     */
    function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_PATH . '/login.php');
        exit;
    }
}
?>
