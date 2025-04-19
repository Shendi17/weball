<?php
require_once __DIR__ . '/../../config.php';

// Configuration spécifique au module Registre
define('UPLOAD_DIR', UPLOADS_ROOT . 'registre/');

// Création du répertoire d'upload s'il n'existe pas
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Fonctions utilitaires pour le module Registre
function getStatusLabel($status) {
    switch ($status) {
        case 'en_cours':
            return ['label' => 'En cours', 'class' => 'status-in-progress'];
        case 'termine':
            return ['label' => 'Terminé', 'class' => 'status-completed'];
        case 'en_attente':
            return ['label' => 'En attente', 'class' => 'status-pending'];
        case 'annule':
            return ['label' => 'Annulé', 'class' => 'status-cancelled'];
        default:
            return ['label' => 'Inconnu', 'class' => ''];
    }
}

function getPriorityLabel($priority) {
    switch ($priority) {
        case 'basse':
            return ['label' => 'Basse', 'class' => 'priority-low'];
        case 'normale':
            return ['label' => 'Normale', 'class' => 'priority-normal'];
        case 'haute':
            return ['label' => 'Haute', 'class' => 'priority-high'];
        case 'urgente':
            return ['label' => 'Urgente', 'class' => 'priority-urgent'];
        default:
            return ['label' => 'Inconnue', 'class' => ''];
    }
}

function getTagsArray($tags) {
    if (empty($tags)) return [];
    return array_map('trim', explode(',', $tags));
}

function getAttachmentsArray($attachments) {
    if (empty($attachments)) return [];
    $attachments = json_decode($attachments, true);
    return is_array($attachments) ? $attachments : [];
}

// Constantes pour les fichiers joints
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 Mo
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'gif']);
