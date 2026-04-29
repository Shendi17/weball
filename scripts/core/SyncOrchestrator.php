<?php
/**
 * Orchestrateur principal de synchronisation
 * Coordonne les différents providers et gère le flux de données
 */

class SyncOrchestrator {
    private $config;
    private $providers = [];
    private $validator;
    private $conflictResolver;
    
    public function __construct($config) {
        $this->config = $config;
        $this->validator = new DataValidator();
        $this->conflictResolver = new ConflictResolver();
        
        // Initialiser les providers activés
        $this->initializeProviders();
    }
    
    /**
     * Initialiser les providers selon la configuration
     */
    private function initializeProviders() {
        foreach ($this->config['providers'] as $name => $settings) {
            if (!$settings['enabled']) {
                continue;
            }
            
            $className = ucfirst($name) . 'Provider';
            
            if (class_exists($className)) {
                $apiKey = $this->config['api_keys'][strtolower($name)] ?? null;
                $this->providers[$name] = new $className($apiKey);
                $this->providers[$name]->setPriority($settings['priority']);
            }
        }
        
        // Trier par priorité
        uasort($this->providers, function($a, $b) {
            return $a->getPriority() <=> $b->getPriority();
        });
    }
    
    /**
     * Synchroniser une autorité
     */
    public function syncAutorite($autorite, $options = []) {
        $result = [
            'status' => 'pending',
            'fields_updated' => 0,
            'providers_used' => [],
            'conflicts' => [],
            'errors' => []
        ];
        
        // Vérifier si la mise à jour est nécessaire
        if (!$this->needsUpdate($autorite, $options)) {
            return [
                'status' => 'skipped',
                'reason' => 'Données déjà complètes',
                'fields_updated' => 0
            ];
        }
        
        $enrichedData = [];
        $providerResults = [];
        
        // Collecter les données de chaque provider
        foreach ($this->providers as $name => $provider) {
            try {
                echo "   🔍 {$name}... ";
                
                $data = $provider->fetchData($autorite);
                
                if (!empty($data)) {
                    $providerResults[$name] = $data;
                    $result['providers_used'][] = $name;
                    echo "✓ (" . count($data) . " champs)\n";
                } else {
                    echo "⊘ (aucune donnée)\n";
                }
                
            } catch (Exception $e) {
                echo "✗ ({$e->getMessage()})\n";
                $result['errors'][] = [
                    'provider' => $name,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        if (empty($providerResults)) {
            return [
                'status' => 'failed',
                'error' => 'Aucun provider n\'a retourné de données',
                'fields_updated' => 0
            ];
        }
        
        // Fusionner les données avec résolution de conflits
        echo "   🔀 Fusion des données...\n";
        $enrichedData = $this->mergeData($autorite, $providerResults, $result);
        
        // Valider les données
        echo "   ✓ Validation...\n";
        $validatedData = $this->validator->validate($enrichedData);
        
        // Sauvegarder si pas en mode dry-run
        if (!($this->config['options']['dry_run'] ?? false)) {
            $updated = $this->saveData($autorite['id'], $validatedData);
            $result['fields_updated'] = $updated;
        } else {
            $result['fields_updated'] = count($validatedData);
            echo "   ⚠️  Mode test - Données non sauvegardées\n";
        }
        
        // Déterminer le statut final
        if ($result['fields_updated'] > 0) {
            $result['status'] = empty($result['errors']) ? 'success' : 'partial';
        } else {
            $result['status'] = 'failed';
            $result['error'] = 'Aucune donnée à mettre à jour';
        }
        
        return $result;
    }
    
    /**
     * Vérifier si une autorité nécessite une mise à jour
     */
    private function needsUpdate($autorite, $options) {
        // Forcer la mise à jour si demandé
        if ($options['force'] ?? false) {
            return true;
        }
        
        // Vérifier si des champs importants sont vides
        $criticalFields = [
            'latitude', 'longitude', 'historique', 'siret', 
            'wikipedia_url', 'linkedin_url'
        ];
        
        foreach ($criticalFields as $field) {
            if (empty($autorite[$field])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Fusionner les données de plusieurs providers
     */
    private function mergeData($autorite, $providerResults, &$result) {
        $merged = [];
        
        foreach ($providerResults as $providerName => $data) {
            foreach ($data as $field => $value) {
                // Ignorer les valeurs vides
                if (empty($value)) {
                    continue;
                }
                
                // Si le champ existe déjà dans autorite et n'est pas vide
                if (!empty($autorite[$field]) && !($this->config['options']['force_update'] ?? false)) {
                    continue; // Ne pas écraser
                }
                
                // Si le champ existe déjà dans merged (conflit entre providers)
                if (isset($merged[$field]) && $merged[$field] !== $value) {
                    // Résoudre le conflit
                    if ($this->config['options']['auto_resolve_conflicts'] ?? true) {
                        $resolved = $this->conflictResolver->resolve($field, [
                            $providerName => $value,
                            $merged[$field . '_provider'] => $merged[$field]
                        ]);
                        $merged[$field] = $resolved;
                    } else {
                        // Enregistrer le conflit pour résolution manuelle
                        $result['conflicts'][] = [
                            'field' => $field,
                            'values' => [
                                $merged[$field . '_provider'] => $merged[$field],
                                $providerName => $value
                            ]
                        ];
                    }
                } else {
                    $merged[$field] = $value;
                    $merged[$field . '_provider'] = $providerName;
                }
            }
        }
        
        // Nettoyer les champs _provider
        foreach (array_keys($merged) as $key) {
            if (strpos($key, '_provider') !== false) {
                unset($merged[$key]);
            }
        }
        
        return $merged;
    }
    
    /**
     * Sauvegarder les données dans la base
     */
    private function saveData($autoriteId, $data) {
        if (empty($data)) {
            return 0;
        }
        
        // Reconnecter à la base pour éviter "MySQL server has gone away"
        $pdo = getPDO(true); // Force reconnection
        
        $fields = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $fields[] = "$field = ?";
            $values[] = $value;
        }
        
        $values[] = $autoriteId;
        
        $sql = "UPDATE autorite SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        
        return count($data);
    }
    
    /**
     * Obtenir les statistiques des providers
     */
    public function getProvidersStats() {
        $stats = [];
        
        foreach ($this->providers as $name => $provider) {
            $stats[$name] = [
                'enabled' => true,
                'priority' => $provider->getPriority(),
                'requires_api_key' => $provider->requiresApiKey(),
                'has_api_key' => $provider->hasApiKey()
            ];
        }
        
        return $stats;
    }
}
