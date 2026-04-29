<?php
/**
 * Résolveur de conflits
 * Gère les cas où plusieurs providers retournent des valeurs différentes
 */

class ConflictResolver {
    private $strategies = [];
    
    public function __construct() {
        $this->defineStrategies();
    }
    
    /**
     * Définir les stratégies de résolution par champ
     */
    private function defineStrategies() {
        $this->strategies = [
            // Coordonnées GPS : prendre la plus précise
            'latitude' => 'most_precise',
            'longitude' => 'most_precise',
            
            // URLs : préférer les sources officielles
            'site_web' => 'prefer_official',
            'wikipedia_url' => 'prefer_wikipedia',
            'linkedin_url' => 'prefer_linkedin',
            
            // Textes : prendre le plus long (plus détaillé)
            'description' => 'longest',
            'description_longue' => 'longest',
            'historique' => 'longest',
            'missions' => 'longest',
            
            // Données légales : préférer les sources gouvernementales
            'siret' => 'prefer_official',
            'forme_juridique' => 'prefer_official',
            
            // Par défaut : prendre la première valeur (priorité du provider)
            'default' => 'first'
        ];
    }
    
    /**
     * Résoudre un conflit pour un champ donné
     */
    public function resolve($field, $values) {
        $strategy = $this->strategies[$field] ?? $this->strategies['default'];
        
        switch ($strategy) {
            case 'most_precise':
                return $this->resolveMostPrecise($values);
            
            case 'longest':
                return $this->resolveLongest($values);
            
            case 'prefer_official':
                return $this->resolvePreferOfficial($values);
            
            case 'prefer_wikipedia':
                return $this->resolvePreferSource($values, 'wikipedia');
            
            case 'prefer_linkedin':
                return $this->resolvePreferSource($values, 'linkedin');
            
            case 'first':
            default:
                return $this->resolveFirst($values);
        }
    }
    
    /**
     * Prendre la valeur la plus précise (pour les nombres)
     */
    private function resolveMostPrecise($values) {
        $maxPrecision = 0;
        $selected = null;
        
        foreach ($values as $provider => $value) {
            if (is_numeric($value)) {
                $precision = strlen(substr(strrchr($value, "."), 1));
                if ($precision > $maxPrecision) {
                    $maxPrecision = $precision;
                    $selected = $value;
                }
            }
        }
        
        return $selected ?? reset($values);
    }
    
    /**
     * Prendre la valeur la plus longue
     */
    private function resolveLongest($values) {
        $maxLength = 0;
        $selected = null;
        
        foreach ($values as $provider => $value) {
            $length = strlen($value);
            if ($length > $maxLength) {
                $maxLength = $length;
                $selected = $value;
            }
        }
        
        return $selected ?? reset($values);
    }
    
    /**
     * Préférer les sources officielles
     */
    private function resolvePreferOfficial($values) {
        $officialProviders = ['wikidata', 'datagouv', 'googleplaces'];
        
        foreach ($officialProviders as $provider) {
            if (isset($values[$provider])) {
                return $values[$provider];
            }
        }
        
        return reset($values);
    }
    
    /**
     * Préférer une source spécifique
     */
    private function resolvePreferSource($values, $preferredSource) {
        if (isset($values[$preferredSource])) {
            return $values[$preferredSource];
        }
        
        return reset($values);
    }
    
    /**
     * Prendre la première valeur (selon priorité du provider)
     */
    private function resolveFirst($values) {
        return reset($values);
    }
    
    /**
     * Calculer un score de confiance pour une valeur
     */
    public function calculateConfidence($field, $value, $provider) {
        $confidence = 0.5; // Score de base
        
        // Augmenter selon le provider
        $providerScores = [
            'wikidata' => 0.9,
            'wikipedia' => 0.85,
            'googleplaces' => 0.8,
            'openstreetmap' => 0.75,
            'clearbit' => 0.7,
            'ai' => 0.6
        ];
        
        $confidence = $providerScores[$provider] ?? 0.5;
        
        // Ajuster selon la complétude de la valeur
        if (is_string($value)) {
            $length = strlen($value);
            if ($length > 100) $confidence += 0.1;
            if ($length > 500) $confidence += 0.1;
        }
        
        // Ajuster selon la validation
        $validator = new DataValidator();
        $result = $validator->validate([$field => $value]);
        if (isset($result[$field])) {
            $confidence += 0.1;
        }
        
        return min(1.0, $confidence);
    }
    
    /**
     * Fusionner plusieurs textes (pour descriptions, historiques, etc.)
     */
    public function mergeTexts($texts) {
        // Prendre le texte le plus long comme base
        $base = $this->resolveLongest($texts);
        
        // Extraire les phrases uniques des autres textes
        $uniqueSentences = [];
        foreach ($texts as $text) {
            if ($text === $base) continue;
            
            $sentences = preg_split('/[.!?]+/', $text);
            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if (strlen($sentence) > 20 && stripos($base, $sentence) === false) {
                    $uniqueSentences[] = $sentence;
                }
            }
        }
        
        // Ajouter les phrases uniques à la fin
        if (!empty($uniqueSentences)) {
            $base .= ' ' . implode('. ', $uniqueSentences) . '.';
        }
        
        return $base;
    }
    
    /**
     * Détecter les incohérences flagrantes
     */
    public function detectInconsistencies($field, $values) {
        $inconsistencies = [];
        
        // Pour les coordonnées GPS
        if ($field === 'latitude' || $field === 'longitude') {
            $numericValues = array_map('floatval', $values);
            $min = min($numericValues);
            $max = max($numericValues);
            
            // Si l'écart est > 1 degré, c'est suspect
            if (($max - $min) > 1) {
                $inconsistencies[] = "Écart important entre les valeurs ($min à $max)";
            }
        }
        
        // Pour les URLs
        if (strpos($field, '_url') !== false) {
            $domains = [];
            foreach ($values as $url) {
                $parsed = parse_url($url);
                $domains[] = $parsed['host'] ?? '';
            }
            
            if (count(array_unique($domains)) > 1) {
                $inconsistencies[] = "Domaines différents: " . implode(', ', array_unique($domains));
            }
        }
        
        return $inconsistencies;
    }
}
