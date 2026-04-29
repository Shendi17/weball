<?php
/**
 * API pour afficher le contenu d'un fichier log
 */

header('Content-Type: text/plain; charset=utf-8');

// Vérifier les permissions
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo 'Accès non autorisé';
    exit;
}

$filename = $_GET['file'] ?? '';

// Sécurité : vérifier que le nom de fichier est valide
if (empty($filename) || !preg_match('/^sync_[\w-]+\.log$/', $filename)) {
    echo 'Nom de fichier invalide';
    exit;
}

$logPath = __DIR__ . '/../scripts/logs/' . $filename;

if (!file_exists($logPath)) {
    echo 'Fichier log introuvable';
    exit;
}

// Afficher le contenu du log
echo file_get_contents($logPath);
