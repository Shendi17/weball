<?php
session_start();
require_once 'config.php';

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = UPLOAD_DIR . $filename;
    
    // Vérification de sécurité
    if (!file_exists($filepath)) {
        die("Le fichier n'existe pas.");
    }
    
    // Détermination du type MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $filepath);
    finfo_close($finfo);
    
    // En-têtes pour le téléchargement
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache');
    
    // Lecture et envoi du fichier
    readfile($filepath);
    exit;
} else {
    header("Location: index.php");
    exit;
}
