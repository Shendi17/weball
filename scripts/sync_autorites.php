<?php
/**
 * Script d'orchestration pour la synchronisation des données des autorités
 * 
 * Usage:
 *   php scripts/sync_autorites.php                    # Sync toutes les autorités
 *   php scripts/sync_autorites.php --id=9             # Sync une autorité spécifique
 *   php scripts/sync_autorites.php --incomplete       # Sync uniquement les fiches incomplètes
 *   php scripts/sync_autorites.php --provider=wikipedia # Utiliser un provider spécifique
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/core/SyncOrchestrator.php';
require_once __DIR__ . '/core/DataValidator.php';
require_once __DIR__ . '/core/ConflictResolver.php';
require_once __DIR__ . '/core/SyncLogger.php';

// Charger tous les providers
foreach (glob(__DIR__ . '/providers/*.php') as $provider) {
    require_once $provider;
}

// Configuration
$config = [
    'providers' => [
        'wikipedia' => ['enabled' => true, 'priority' => 1],
        'wikidata' => ['enabled' => true, 'priority' => 2],
        'openstreetmap' => ['enabled' => true, 'priority' => 3],
        'googleplaces' => ['enabled' => false, 'priority' => 4], // Nécessite API key
        'clearbit' => ['enabled' => false, 'priority' => 5],     // Nécessite API key
        'ai' => ['enabled' => false, 'priority' => 10]           // Nécessite API key
    ],
    'api_keys' => [
        'google_places' => getenv('GOOGLE_PLACES_API_KEY') ?: null,
        'clearbit' => getenv('CLEARBIT_API_KEY') ?: null,
        'openai' => getenv('OPENAI_API_KEY') ?: null,
    ],
    'options' => [
        'dry_run' => false,              // Mode test sans sauvegarde
        'force_update' => false,         // Forcer la mise à jour même si données existantes
        'auto_resolve_conflicts' => true, // Résoudre automatiquement les conflits
        'max_concurrent' => 5,           // Nombre de requêtes simultanées
        'delay_between_requests' => 1000 // Délai en ms entre les requêtes
    ]
];

// Parser les arguments
$options = parseArguments($argv);

// Appliquer les options de ligne de commande à la configuration
if (isset($options['dry-run'])) {
    $config['options']['dry_run'] = true;
}
if (isset($options['force'])) {
    $config['options']['force_update'] = true;
}

// Initialiser l'orchestrateur
$orchestrator = new SyncOrchestrator($config);
$logger = new SyncLogger(__DIR__ . '/logs');

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     SYNCHRONISATION DES DONNÉES AUTORITÉS                  ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

try {
    $pdo = getPDO();
    
    // Déterminer quelles autorités synchroniser
    if (isset($options['id'])) {
        $autorites = [$pdo->query("SELECT * FROM autorite WHERE id = " . (int)$options['id'])->fetch(PDO::FETCH_ASSOC)];
        echo "📋 Mode: Autorité spécifique (ID: {$options['id']})\n\n";
    } elseif (isset($options['incomplete'])) {
        $autorites = $pdo->query("
            SELECT * FROM autorite 
            WHERE latitude IS NULL 
               OR longitude IS NULL 
               OR historique IS NULL 
               OR siret IS NULL
            ORDER BY id
        ")->fetchAll(PDO::FETCH_ASSOC);
        echo "📋 Mode: Fiches incomplètes (" . count($autorites) . " trouvées)\n\n";
    } else {
        $autorites = $pdo->query("SELECT * FROM autorite ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
        echo "📋 Mode: Toutes les autorités (" . count($autorites) . " trouvées)\n\n";
    }
    
    if (empty($autorites)) {
        echo "❌ Aucune autorité à synchroniser.\n";
        exit(0);
    }
    
    // Afficher les providers actifs
    echo "🔌 Providers actifs:\n";
    foreach ($config['providers'] as $name => $settings) {
        if ($settings['enabled']) {
            echo "   ✓ " . ucfirst($name) . " (priorité: {$settings['priority']})\n";
        }
    }
    echo "\n";
    
    if ($config['options']['dry_run']) {
        echo "⚠️  MODE TEST - Aucune modification ne sera sauvegardée\n\n";
    }
    
    // Synchroniser chaque autorité
    $stats = [
        'total' => count($autorites),
        'success' => 0,
        'partial' => 0,
        'failed' => 0,
        'skipped' => 0,
        'fields_updated' => 0
    ];
    
    foreach ($autorites as $index => $autorite) {
        $num = $index + 1;
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📍 [{$num}/{$stats['total']}] {$autorite['nom']}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        try {
            // Reconnecter à la base avant chaque autorité pour éviter les timeouts
            getPDO(true);
            
            $result = $orchestrator->syncAutorite($autorite, $options);
            
            if ($result['status'] === 'success') {
                $stats['success']++;
                $stats['fields_updated'] += $result['fields_updated'];
                echo "✅ Succès - {$result['fields_updated']} champs mis à jour\n";
            } elseif ($result['status'] === 'partial') {
                $stats['partial']++;
                $stats['fields_updated'] += $result['fields_updated'];
                echo "⚠️  Partiel - {$result['fields_updated']} champs mis à jour\n";
            } elseif ($result['status'] === 'skipped') {
                $stats['skipped']++;
                echo "⏭️  Ignoré - {$result['reason']}\n";
            } else {
                $stats['failed']++;
                echo "❌ Échec - {$result['error']}\n";
            }
            
            // Logger le résultat
            $logger->log($autorite['id'], $result);
            
        } catch (Exception $e) {
            $stats['failed']++;
            echo "❌ Erreur: " . $e->getMessage() . "\n";
            $logger->logError($autorite['id'], $e);
        }
        
        echo "\n";
        
        // Délai entre les requêtes
        if ($num < $stats['total']) {
            usleep($config['options']['delay_between_requests'] * 1000);
        }
    }
    
    // Afficher les statistiques finales
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║                    RÉSUMÉ FINAL                            ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    echo "📊 Statistiques:\n";
    echo "   Total traité:      {$stats['total']}\n";
    echo "   ✅ Succès:         {$stats['success']}\n";
    echo "   ⚠️  Partiel:        {$stats['partial']}\n";
    echo "   ❌ Échec:          {$stats['failed']}\n";
    echo "   ⏭️  Ignoré:         {$stats['skipped']}\n";
    echo "   📝 Champs mis à jour: {$stats['fields_updated']}\n\n";
    
    $logger->writeSummary($stats);
    
    echo "📄 Logs sauvegardés dans: scripts/logs/\n";
    
} catch (Exception $e) {
    echo "\n❌ ERREUR FATALE: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Parser les arguments de ligne de commande
 */
function parseArguments($argv) {
    $options = [];
    
    foreach ($argv as $arg) {
        if (strpos($arg, '--') === 0) {
            $parts = explode('=', substr($arg, 2), 2);
            $key = $parts[0];
            $value = isset($parts[1]) ? $parts[1] : true;
            $options[$key] = $value;
        }
    }
    
    return $options;
}
