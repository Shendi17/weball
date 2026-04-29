<?php
/**
 * Classe de base pour tous les providers
 * Définit l'interface commune et les méthodes utilitaires
 */

abstract class BaseProvider {
    protected $apiKey;
    protected $priority = 10;
    protected $cache = [];
    protected $rateLimitDelay = 1000; // ms
    protected $lastRequestTime = 0;
    
    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Méthode principale à implémenter par chaque provider
     * Doit retourner un tableau associatif [champ => valeur]
     */
    abstract public function fetchData($autorite);
    
    /**
     * Nom du provider
     */
    abstract public function getName();
    
    /**
     * Indique si ce provider nécessite une clé API
     */
    public function requiresApiKey() {
        return false;
    }
    
    /**
     * Vérifier si la clé API est configurée
     */
    public function hasApiKey() {
        return !empty($this->apiKey);
    }
    
    /**
     * Définir la priorité
     */
    public function setPriority($priority) {
        $this->priority = $priority;
    }
    
    /**
     * Obtenir la priorité
     */
    public function getPriority() {
        return $this->priority;
    }
    
    /**
     * Effectuer une requête HTTP avec gestion du rate limiting
     */
    protected function httpGet($url, $headers = []) {
        // Respecter le rate limit
        $elapsed = (microtime(true) * 1000) - $this->lastRequestTime;
        if ($elapsed < $this->rateLimitDelay) {
            usleep((int)(($this->rateLimitDelay - $elapsed) * 1000));
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'WeBall Data Sync Bot/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $this->lastRequestTime = microtime(true) * 1000;
        
        if ($error) {
            throw new Exception("Erreur HTTP: $error");
        }
        
        if ($httpCode >= 400) {
            throw new Exception("Erreur HTTP $httpCode");
        }
        
        return $response;
    }
    
    /**
     * Effectuer une requête HTTP et décoder le JSON
     */
    protected function httpGetJson($url, $headers = []) {
        $response = $this->httpGet($url, $headers);
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erreur de décodage JSON: " . json_last_error_msg());
        }
        
        return $data;
    }
    
    /**
     * Nettoyer et normaliser une chaîne
     */
    protected function cleanString($string) {
        if (empty($string)) {
            return null;
        }
        
        $string = trim($string);
        $string = preg_replace('/\s+/', ' ', $string);
        
        return $string ?: null;
    }
    
    /**
     * Extraire un domaine d'une URL
     */
    protected function extractDomain($url) {
        $parsed = parse_url($url);
        return $parsed['host'] ?? null;
    }
    
    /**
     * Vérifier si une URL est valide
     */
    protected function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Mettre en cache un résultat
     */
    protected function cacheSet($key, $value, $ttl = 3600) {
        $this->cache[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }
    
    /**
     * Récupérer depuis le cache
     */
    protected function cacheGet($key) {
        if (!isset($this->cache[$key])) {
            return null;
        }
        
        if ($this->cache[$key]['expires'] < time()) {
            unset($this->cache[$key]);
            return null;
        }
        
        return $this->cache[$key]['value'];
    }
    
    /**
     * Logger une information
     */
    protected function log($message, $level = 'info') {
        $timestamp = date('Y-m-d H:i:s');
        $providerName = $this->getName();
        echo "      [$timestamp] [$providerName] [$level] $message\n";
    }
    
    /**
     * Mapper les champs du provider vers les champs de la base
     */
    protected function mapFields($data, $mapping) {
        $mapped = [];
        
        foreach ($mapping as $sourceField => $targetField) {
            if (isset($data[$sourceField]) && !empty($data[$sourceField])) {
                $mapped[$targetField] = $data[$sourceField];
            }
        }
        
        return $mapped;
    }
    
    /**
     * Rechercher une entité par nom
     */
    protected function searchByName($name) {
        // À implémenter par chaque provider si nécessaire
        return null;
    }
    
    /**
     * Obtenir des métadonnées sur le provider
     */
    public function getMetadata() {
        return [
            'name' => $this->getName(),
            'priority' => $this->priority,
            'requires_api_key' => $this->requiresApiKey(),
            'has_api_key' => $this->hasApiKey(),
            'rate_limit_delay' => $this->rateLimitDelay
        ];
    }
}
