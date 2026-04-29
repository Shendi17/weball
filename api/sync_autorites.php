<?php
/**
 * API pour lancer la synchronisation des autorités
 * Retourne un JSON avec les résultats
 */

// Augmenter le timeout pour les longues synchronisations
set_time_limit(600); // 10 minutes
ini_set('max_execution_time', 600);

header('Content-Type: application/json');

// Vérifier les permissions (à adapter selon votre système d'authentification)
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

// Fichier de log pour sauvegarder les résultats
$outputFile = __DIR__ . '/../scripts/logs/api_sync_' . time() . '.log';

// Exécuter la commande directement (synchrone pour avoir les résultats)
$descriptorspec = [
    0 => ["pipe", "r"],  // stdin
    1 => ["pipe", "w"],  // stdout
    2 => ["pipe", "w"]   // stderr
];

$process = proc_open($command, $descriptorspec, $pipes);

if (is_resource($process)) {
    // Fermer stdin
    fclose($pipes[0]);
    
    // Lire stdout
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    
    // Lire stderr
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    
    // Attendre la fin du processus
    $returnCode = proc_close($process);
    
    // Parser les statistiques de la sortie
    $stats = [
        'total' => 0,
        'success' => 0,
        'partial' => 0,
        'failed' => 0,
        'skipped' => 0,
        'fields_updated' => 0
    ];
    
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
    
    // Sauvegarder la sortie dans le fichier de log
    file_put_contents($outputFile, $output);
    
    echo json_encode([
        'success' => true,
        'message' => 'Synchronisation terminée',
        'stats' => $stats,
        'log_file' => basename($outputFile),
        'output' => $output
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Impossible de lancer la synchronisation'
    ]);
}
