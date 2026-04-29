<?php
/**
 * Test de l'API de synchronisation
 */

session_start();

// Simuler une connexion admin pour le test
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test API Sync</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Test de l'API de Synchronisation</h1>
    
    <p>Session configurée : <span class='success'>✅ Admin connecté</span></p>
    
    <h2>Test 1 : Synchronisation d'une autorité (ID 9)</h2>
    <button onclick=\"testSync('single', 9)\">Lancer le test</button>
    <div id='result1'></div>
    
    <h2>Test 2 : Vérifier l'accès à l'API</h2>
    <button onclick=\"testAccess()\">Tester l'accès</button>
    <div id='result2'></div>
    
    <script>
        function testSync(mode, id) {
            document.getElementById('result1').innerHTML = '<p>⏳ Test en cours...</p>';
            
            fetch('/api/sync_autorites.php?mode=' + mode + '&id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('result1').innerHTML = 
                        '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('result1').innerHTML = 
                        '<p class=\"error\">❌ Erreur: ' + error.message + '</p>';
                });
        }
        
        function testAccess() {
            document.getElementById('result2').innerHTML = '<p>⏳ Test en cours...</p>';
            
            fetch('/api/sync_autorites.php?mode=test')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('result2').innerHTML = 
                        '<pre>' + data + '</pre>';
                })
                .catch(error => {
                    document.getElementById('result2').innerHTML = 
                        '<p class=\"error\">❌ Erreur: ' + error.message + '</p>';
                });
        }
    </script>
</body>
</html>";
