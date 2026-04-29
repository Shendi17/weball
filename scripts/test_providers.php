<?php
/**
 * Script de test des providers
 * Permet de tester chaque provider individuellement
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/core/BaseProvider.php';

// Charger tous les providers
foreach (glob(__DIR__ . '/providers/*.php') as $provider) {
    require_once $provider;
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║           TEST DES PROVIDERS                               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Données de test
$testAutorite = [
    'id' => 9,
    'nom' => 'Airbus',
    'ville' => 'Toulouse',
    'pays' => 'France',
    'site_web' => 'https://www.airbus.com'
];

echo "🧪 Autorité de test: {$testAutorite['nom']}\n\n";

// Liste des providers à tester
$providers = [
    'WikipediaProvider',
    'WikidataProvider',
    'OpenstreetmapProvider',
    'GoogleplacesProvider',
    'ClearbitProvider',
    'AiProvider'
];

$results = [];

foreach ($providers as $providerClass) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📦 Test de $providerClass\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    try {
        $provider = new $providerClass();
        
        // Vérifier si nécessite une clé API
        if ($provider->requiresApiKey() && !$provider->hasApiKey()) {
            echo "⚠️  Clé API manquante - Provider ignoré\n\n";
            $results[$providerClass] = [
                'status' => 'skipped',
                'reason' => 'Clé API manquante'
            ];
            continue;
        }
        
        echo "🔍 Récupération des données...\n";
        $startTime = microtime(true);
        
        $data = $provider->fetchData($testAutorite);
        
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if (empty($data)) {
            echo "❌ Aucune donnée retournée\n";
            $results[$providerClass] = [
                'status' => 'empty',
                'duration' => $duration
            ];
        } else {
            echo "✅ Succès - " . count($data) . " champs récupérés en {$duration}ms\n\n";
            echo "📊 Données récupérées:\n";
            
            foreach ($data as $field => $value) {
                $preview = is_string($value) && strlen($value) > 60 
                    ? substr($value, 0, 60) . '...' 
                    : $value;
                echo "   • $field: " . var_export($preview, true) . "\n";
            }
            
            $results[$providerClass] = [
                'status' => 'success',
                'fields_count' => count($data),
                'duration' => $duration,
                'data' => $data
            ];
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
        $results[$providerClass] = [
            'status' => 'error',
            'error' => $e->getMessage()
        ];
    }
    
    echo "\n";
}

// Résumé
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    RÉSUMÉ DES TESTS                        ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$stats = [
    'success' => 0,
    'empty' => 0,
    'error' => 0,
    'skipped' => 0,
    'total_fields' => 0
];

foreach ($results as $provider => $result) {
    $status = $result['status'];
    $stats[$status]++;
    
    if ($status === 'success') {
        $stats['total_fields'] += $result['fields_count'];
    }
    
    $icon = match($status) {
        'success' => '✅',
        'empty' => '⚠️',
        'error' => '❌',
        'skipped' => '⏭️',
        default => '❓'
    };
    
    $statusText = strtoupper($status);
    $providerName = str_replace('Provider', '', $provider);
    
    echo "$icon $providerName: $statusText";
    
    if ($status === 'success') {
        echo " ({$result['fields_count']} champs, {$result['duration']}ms)";
    } elseif ($status === 'error') {
        echo " - {$result['error']}";
    } elseif ($status === 'skipped') {
        echo " - {$result['reason']}";
    }
    
    echo "\n";
}

echo "\n📊 Statistiques:\n";
echo "   Total providers testés: " . count($results) . "\n";
echo "   ✅ Succès: {$stats['success']}\n";
echo "   ⚠️  Vide: {$stats['empty']}\n";
echo "   ❌ Erreur: {$stats['error']}\n";
echo "   ⏭️  Ignoré: {$stats['skipped']}\n";
echo "   📝 Total champs récupérés: {$stats['total_fields']}\n\n";

// Sauvegarder les résultats
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logFile = $logDir . '/test_providers_' . date('Y-m-d_H-i-s') . '.json';
file_put_contents($logFile, json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'test_autorite' => $testAutorite,
    'results' => $results,
    'stats' => $stats
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "💾 Résultats sauvegardés dans: $logFile\n";

// Recommandations
echo "\n💡 Recommandations:\n";

if ($stats['success'] >= 3) {
    echo "   ✅ Vous avez au moins 3 providers fonctionnels, c'est excellent!\n";
} elseif ($stats['success'] >= 1) {
    echo "   ⚠️  Vous avez {$stats['success']} provider(s) fonctionnel(s).\n";
    echo "      Considérez activer plus de providers pour de meilleurs résultats.\n";
} else {
    echo "   ❌ Aucun provider fonctionnel.\n";
    echo "      Vérifiez votre connexion internet et les clés API.\n";
}

if ($stats['skipped'] > 0) {
    echo "   🔑 {$stats['skipped']} provider(s) nécessitent une clé API.\n";
    echo "      Consultez le README.md pour les configurer.\n";
}

echo "\n🚀 Prochaine étape:\n";
echo "   php scripts/sync_autorites.php --id=9 --dry-run\n";
