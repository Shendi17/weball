<?php
/**
 * Script de test pour vérifier la connexion
 */

session_start();

echo "=== TEST DE CONNEXION ===\n\n";

// Test 1 : Vérifier la session actuelle
echo "1. Session actuelle :\n";
if (isset($_SESSION['user_id'])) {
    echo "   ✅ Connecté\n";
    echo "   - user_id: " . $_SESSION['user_id'] . "\n";
    echo "   - username: " . ($_SESSION['username'] ?? 'non défini') . "\n";
    echo "   - user_role: " . ($_SESSION['user_role'] ?? 'non défini') . "\n";
} else {
    echo "   ❌ Non connecté\n";
}

echo "\n2. Pour vous connecter :\n";
echo "   - Allez sur : http://weball.local/login.php\n";
echo "   - Utilisez : admin@weball.com / admin123\n";
echo "   - Ou : admin / admin\n";

echo "\n3. Après connexion, accédez à :\n";
echo "   http://weball.local/pages/sync_admin.php\n";

echo "\n=== FIN DU TEST ===\n";
