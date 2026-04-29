<?php
/**
 * Script de test pour vérifier que la page sync_admin fonctionne
 */

session_start();

// Simuler une connexion admin
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

echo "✅ Session configurée\n";
echo "   user_id: " . $_SESSION['user_id'] . "\n";
echo "   username: " . $_SESSION['username'] . "\n";
echo "   user_role: " . $_SESSION['user_role'] . "\n\n";

echo "🔗 Accédez maintenant à : http://localhost/pages/sync_admin.php\n";
echo "   Vous devriez être connecté automatiquement.\n\n";

echo "📋 Identifiants de connexion disponibles :\n";
echo "   - admin / admin\n";
echo "   - admin@weball.com / admin123\n";
