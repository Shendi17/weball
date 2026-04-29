<?php
/**
 * Page d'administration de la synchronisation des autorités
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Vérifier les permissions admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /');
    exit;
}

$pdo = getPDO();

$pageTitle = "Synchronisation des Autorités";
$pageDescription = "Gestion de la synchronisation automatique des données";
$moduleName = 'sync_admin';

// Récupérer les statistiques
$stats = [
    'total_autorites' => $pdo->query("SELECT COUNT(*) FROM autorite")->fetchColumn(),
    'autorites_completes' => $pdo->query("
        SELECT COUNT(*) FROM autorite 
        WHERE latitude IS NOT NULL 
        AND longitude IS NOT NULL 
        AND historique IS NOT NULL
    ")->fetchColumn(),
    'autorites_incompletes' => $pdo->query("
        SELECT COUNT(*) FROM autorite 
        WHERE latitude IS NULL 
        OR longitude IS NULL 
        OR historique IS NULL
    ")->fetchColumn()
];

// Récupérer les derniers logs
$logFiles = glob(__DIR__ . '/../scripts/logs/sync_*.log');
rsort($logFiles);
$recentLogs = array_slice($logFiles, 0, 10);

$logData = [];
foreach ($recentLogs as $logFile) {
    $filename = basename($logFile);
    preg_match('/sync_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $filename, $matches);
    $date = isset($matches[1]) ? str_replace('_', ' ', $matches[1]) : 'Inconnu';
    
    // Lire les dernières lignes du log
    $content = file_get_contents($logFile);
    $lines = explode("\n", $content);
    $summary = '';
    
    foreach (array_reverse($lines) as $line) {
        if (strpos($line, 'Status:') !== false) {
            $summary = $line;
            break;
        }
    }
    
    $logData[] = [
        'file' => $filename,
        'date' => $date,
        'path' => $logFile,
        'size' => filesize($logFile),
        'summary' => $summary
    ];
}

// Charger le template
include __DIR__ . '/../templates/pages/sync_admin_template.php';
