<?php
/**
 * API asynchrone pour lancer la synchronisation des autorités
 * Lance le processus en arrière-plan et retourne immédiatement
 */

// Augmenter le timeout
set_time_limit(30);

header('Content-Type: application/json');

// Vérifier les permissions
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Accès non autorisé'
    ]);
    exit;
}

$mode = $_GET['mode'] ?? 'all';
$autoriteId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Construire la commande
$scriptPath = __DIR__ . '/../scripts/sync_autorites.php';
$command = 'php ' . escapeshellarg($scriptPath);

switch ($mode) {
    case 'single':
        if (!$autoriteId) {
            echo json_encode([
                'success' => false,
                'message' => 'ID d\'autorité manquant'
            ]);
            exit;
        }
        $command .= ' --id=' . $autoriteId . ' --force';
        break;
    
    case 'incomplete':
        $command .= ' --incomplete --force';
        break;
    
    case 'all':
    default:
        $command .= ' --all --force';
        break;
}

// Fichier de log
$logFile = __DIR__ . '/../scripts/logs/api_sync_' . time() . '.log';
$statusFile = __DIR__ . '/../scripts/logs/api_sync_' . time() . '.status';

// Lancer en arrière-plan
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows
    $fullCommand = 'start /B cmd /C "' . $command . ' > ' . escapeshellarg($logFile) . ' 2>&1 & echo DONE > ' . escapeshellarg($statusFile) . '"';
    pclose(popen($fullCommand, 'r'));
} else {
    // Linux/Mac
    $fullCommand = $command . ' > ' . escapeshellarg($logFile) . ' 2>&1 & echo $! > ' . escapeshellarg($statusFile);
    exec($fullCommand);
}

// Retourner immédiatement
echo json_encode([
    'success' => true,
    'message' => 'Synchronisation lancée en arrière-plan',
    'log_file' => basename($logFile),
    'status_file' => basename($statusFile),
    'check_url' => '/api/check_sync_status.php?file=' . urlencode(basename($statusFile))
]);
