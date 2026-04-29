<?php
/**
 * Vider le cache OPcache
 */

echo "<h1>Vidage du cache PHP</h1>";

if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<p style='color:green;font-size:20px;'>✓ Cache OPcache vidé avec succès !</p>";
    } else {
        echo "<p style='color:red;'>✗ Erreur lors du vidage du cache OPcache</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠ OPcache n'est pas activé</p>";
}

echo "<hr>";
echo "<p><a href='/office'>Tester la page Office</a></p>";
echo "<p><a href='/'>Retour à l'accueil</a></p>";
