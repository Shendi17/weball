<?php
/**
 * Script pour forcer la connexion en tant qu'admin (DÉVELOPPEMENT UNIQUEMENT)
 * À SUPPRIMER EN PRODUCTION !
 */

session_start();

$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Connexion Forcée</title>
    <meta http-equiv='refresh' content='2;url=/pages/sync_admin.php'>
    <style>
        body {
            font-family: Arial;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class='box'>
        <div class='success'>✅</div>
        <h2>Connexion Réussie !</h2>
        <p>Vous êtes maintenant connecté en tant qu'<strong>admin</strong>.</p>
        <p>Redirection vers la page de synchronisation...</p>
        <p><a href='/pages/sync_admin.php'>Cliquez ici si la redirection ne fonctionne pas</a></p>
    </div>
</body>
</html>";
