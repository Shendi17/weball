<?php
// Test backend : vérification de la requête annuaire
require_once __DIR__ . '/config.db.php';
header('Content-Type: text/plain; charset=utf-8');
try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT id, full_name, email, is_active FROM users ORDER BY full_name");
    $users = $stmt->fetchAll();
    if (empty($users)) {
        echo "Aucun utilisateur trouvé dans la base.\n";
    } else {
        echo "Utilisateurs trouvés :\n";
        foreach ($users as $user) {
            echo "- [" . ($user['is_active'] ? 'actif' : 'inactif') . "] ";
            echo $user['full_name'] . " (" . $user['email'] . ")\n";
        }
    }
} catch (PDOException $e) {
    echo "ERREUR SQL : " . $e->getMessage();
}
