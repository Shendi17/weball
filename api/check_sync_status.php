<?php
/**
 * API pour vérifier le statut d'une synchronisation en cours
 */

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Accès non autorisé'
    ]);
    exit;
}

$statusFile = $_GET['file'] ?? '';

// Sécurité
if (empty($statusFile) || !preg_match('/^api_sync_[\w]+\.status$/', $statusFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Fichier invalide'
    ]);
    exit;
}

$statusPath = __DIR__ . '/../scripts/logs/' . $statusFile;
$logFile = str_replace('.status', '.log', $statusPath);

// Vérifier si le processus est terminé
$completed = file_exists($statusPath);

// Lire le log
$output = '';
$stats = [
    'total' => 0,
    'success' => 0,
    'partial' => 0,
    'failed' => 0,
    'skipped' => 0,
    'fields_updated' => 0
];

if (file_exists($logFile)) {
    $output = file_get_contents($logFile);
    
    // Parser les statistiques
    if (preg_match('/Total traité:\s+(\d+)/', $output, $matches)) {
        $stats['total'] = (int)$matches[1];
    }
    if (preg_match('/Succès:\s+(\d+)/', $output, $matches)) {
        $stats['success'] = (int)$matches[1];
    }
    if (preg_match('/Partiel:\s+(\d+)/', $output, $matches)) {
        $stats['partial'] = (int)$matches[1];
    }
    if (preg_match('/Échec:\s+(\d+)/', $output, $matches)) {
        $stats['failed'] = (int)$matches[1];
    }
    if (preg_match('/Ignoré:\s+(\d+)/', $output, $matches)) {
        $stats['skipped'] = (int)$matches[1];
    }
    if (preg_match('/Champs mis à jour:\s+(\d+)/', $output, $matches)) {
        $stats['fields_updated'] = (int)$matches[1];
    }
}

echo json_encode([
    'success' => true,
    'completed' => $completed,
    'stats' => $stats,
    'has_results' => $stats['total'] > 0
]);
