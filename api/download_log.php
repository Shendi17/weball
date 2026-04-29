<?php
/**
 * API pour télécharger un fichier log
 */

// Vérifier les permissions
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Accès non autorisé';
    exit;
}

$filename = $_GET['file'] ?? '';

// Sécurité : vérifier que le nom de fichier est valide
if (empty($filename) || !preg_match('/^sync_[\w-]+\.log$/', $filename)) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Nom de fichier invalide';
    exit;
}

$logPath = __DIR__ . '/../scripts/logs/' . $filename;

if (!file_exists($logPath)) {
    header('HTTP/1.1 404 Not Found');
    echo 'Fichier log introuvable';
    exit;
}

// Forcer le téléchargement
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($logPath));
readfile($logPath);
