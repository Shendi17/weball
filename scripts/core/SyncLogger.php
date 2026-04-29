<?php
/**
 * Logger pour la synchronisation
 * Enregistre tous les événements et résultats
 */

class SyncLogger {
    private $logDir;
    private $sessionId;
    private $logs = [];
    
    public function __construct($logDir) {
        $this->logDir = $logDir;
        $this->sessionId = date('Y-m-d_H-i-s') . '_' . uniqid();
        
        // Créer le dossier de logs s'il n'existe pas
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Logger le résultat d'une synchronisation
     */
    public function log($autoriteId, $result) {
        $this->logs[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'autorite_id' => $autoriteId,
            'status' => $result['status'],
            'fields_updated' => $result['fields_updated'] ?? 0,
            'providers_used' => $result['providers_used'] ?? [],
            'conflicts' => $result['conflicts'] ?? [],
            'errors' => $result['errors'] ?? []
        ];
    }
    
    /**
     * Logger une erreur
     */
    public function logError($autoriteId, $exception) {
        $this->logs[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'autorite_id' => $autoriteId,
            'status' => 'error',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ];
    }
    
    /**
     * Écrire le résumé dans un fichier
     */
    public function writeSummary($stats) {
        $filename = $this->logDir . '/sync_' . $this->sessionId . '.log';
        
        $content = "╔════════════════════════════════════════════════════════════╗\n";
        $content .= "║           RAPPORT DE SYNCHRONISATION                       ║\n";
        $content .= "╚════════════════════════════════════════════════════════════╝\n\n";
        
        $content .= "Session ID: {$this->sessionId}\n";
        $content .= "Date: " . date('Y-m-d H:i:s') . "\n\n";
        
        $content .= "STATISTIQUES GLOBALES:\n";
        $content .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $content .= "Total traité:        {$stats['total']}\n";
        $content .= "Succès:              {$stats['success']}\n";
        $content .= "Partiel:             {$stats['partial']}\n";
        $content .= "Échec:               {$stats['failed']}\n";
        $content .= "Ignoré:              {$stats['skipped']}\n";
        $content .= "Champs mis à jour:   {$stats['fields_updated']}\n\n";
        
        $content .= "DÉTAILS PAR AUTORITÉ:\n";
        $content .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        foreach ($this->logs as $log) {
            $content .= "\n[{$log['timestamp']}] Autorité #{$log['autorite_id']}\n";
            $content .= "  Status: {$log['status']}\n";
            
            if (isset($log['fields_updated'])) {
                $content .= "  Champs mis à jour: {$log['fields_updated']}\n";
            }
            
            if (!empty($log['providers_used'])) {
                $content .= "  Providers: " . implode(', ', $log['providers_used']) . "\n";
            }
            
            if (!empty($log['conflicts'])) {
                $content .= "  Conflits: " . count($log['conflicts']) . "\n";
                foreach ($log['conflicts'] as $conflict) {
                    $content .= "    - {$conflict['field']}\n";
                }
            }
            
            if (!empty($log['errors'])) {
                $content .= "  Erreurs:\n";
                foreach ($log['errors'] as $error) {
                    if (is_array($error)) {
                        $content .= "    - {$error['provider']}: {$error['error']}\n";
                    } else {
                        $content .= "    - $error\n";
                    }
                }
            }
            
            if (isset($log['error'])) {
                $content .= "  ERREUR: {$log['error']}\n";
            }
        }
        
        file_put_contents($filename, $content);
        
        return $filename;
    }
    
    /**
     * Écrire les détails en JSON
     */
    public function writeJson() {
        $filename = $this->logDir . '/sync_' . $this->sessionId . '.json';
        
        $data = [
            'session_id' => $this->sessionId,
            'timestamp' => date('Y-m-d H:i:s'),
            'logs' => $this->logs
        ];
        
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $filename;
    }
    
    /**
     * Obtenir les logs de la session
     */
    public function getLogs() {
        return $this->logs;
    }
    
    /**
     * Générer un rapport HTML
     */
    public function generateHtmlReport($stats) {
        $filename = $this->logDir . '/sync_' . $this->sessionId . '.html';
        
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Synchronisation - ' . $this->sessionId . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-card.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-card.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.error { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .stat-value { font-size: 36px; font-weight: bold; margin: 10px 0; }
        .stat-label { font-size: 14px; opacity: 0.9; }
        .log-entry { background: #f8f9fa; margin: 10px 0; padding: 15px; border-left: 4px solid #007bff; border-radius: 4px; }
        .log-entry.success { border-left-color: #28a745; }
        .log-entry.error { border-left-color: #dc3545; }
        .log-entry.warning { border-left-color: #ffc107; }
        .timestamp { color: #6c757d; font-size: 12px; }
        .providers { display: inline-block; background: #e9ecef; padding: 4px 8px; border-radius: 4px; margin: 2px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Rapport de Synchronisation</h1>
        <p><strong>Session:</strong> ' . $this->sessionId . '<br>
        <strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</p>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value">' . $stats['total'] . '</div>
                <div class="stat-label">Total traité</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">' . $stats['success'] . '</div>
                <div class="stat-label">Succès</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">' . $stats['partial'] . '</div>
                <div class="stat-label">Partiel</div>
            </div>
            <div class="stat-card error">
                <div class="stat-value">' . $stats['failed'] . '</div>
                <div class="stat-label">Échec</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">' . $stats['fields_updated'] . '</div>
                <div class="stat-label">Champs mis à jour</div>
            </div>
        </div>
        
        <h2>Détails des opérations</h2>';
        
        foreach ($this->logs as $log) {
            $statusClass = $log['status'] === 'success' ? 'success' : ($log['status'] === 'failed' ? 'error' : 'warning');
            
            $html .= '<div class="log-entry ' . $statusClass . '">
                <div class="timestamp">' . $log['timestamp'] . '</div>
                <strong>Autorité #' . $log['autorite_id'] . '</strong> - ' . strtoupper($log['status']) . '<br>';
            
            if (isset($log['fields_updated'])) {
                $html .= 'Champs mis à jour: ' . $log['fields_updated'] . '<br>';
            }
            
            if (!empty($log['providers_used'])) {
                $html .= 'Providers: ';
                foreach ($log['providers_used'] as $provider) {
                    $html .= '<span class="providers">' . $provider . '</span> ';
                }
                $html .= '<br>';
            }
            
            if (!empty($log['errors'])) {
                $html .= '<div style="color: #dc3545; margin-top: 10px;">Erreurs:<ul>';
                foreach ($log['errors'] as $error) {
                    if (is_array($error)) {
                        $html .= '<li>' . $error['provider'] . ': ' . $error['error'] . '</li>';
                    }
                }
                $html .= '</ul></div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>
</body>
</html>';
        
        file_put_contents($filename, $html);
        
        return $filename;
    }
}
