<?php
/**
 * Validateur de données
 * Vérifie la cohérence et la qualité des données avant sauvegarde
 */

class DataValidator {
    private $rules = [];
    
    public function __construct() {
        $this->defineRules();
    }
    
    /**
     * Définir les règles de validation
     */
    private function defineRules() {
        $this->rules = [
            'latitude' => ['type' => 'float', 'min' => -90, 'max' => 90],
            'longitude' => ['type' => 'float', 'min' => -180, 'max' => 180],
            'email' => ['type' => 'email'],
            'email_presse' => ['type' => 'email'],
            'telephone' => ['type' => 'phone'],
            'telephone_presse' => ['type' => 'phone'],
            'site_web' => ['type' => 'url'],
            'wikipedia_url' => ['type' => 'url', 'domain' => 'wikipedia.org'],
            'linkedin_url' => ['type' => 'url', 'domain' => 'linkedin.com'],
            'twitter_url' => ['type' => 'url', 'domain' => 'twitter.com'],
            'facebook_url' => ['type' => 'url', 'domain' => 'facebook.com'],
            'instagram_url' => ['type' => 'url', 'domain' => 'instagram.com'],
            'youtube_url' => ['type' => 'url', 'domain' => 'youtube.com'],
            'siret' => ['type' => 'siret'],
            'code_postal' => ['type' => 'postal_code'],
            'effectifs' => ['type' => 'string', 'max_length' => 100],
            'chiffre_affaires' => ['type' => 'string', 'max_length' => 100],
        ];
    }
    
    /**
     * Valider un ensemble de données
     */
    public function validate($data) {
        $validated = [];
        $errors = [];
        
        foreach ($data as $field => $value) {
            if (empty($value)) {
                continue;
            }
            
            // Appliquer les règles si elles existent
            if (isset($this->rules[$field])) {
                $result = $this->validateField($field, $value, $this->rules[$field]);
                
                if ($result['valid']) {
                    $validated[$field] = $result['value'];
                } else {
                    $errors[$field] = $result['error'];
                }
            } else {
                // Pas de règle spécifique, validation basique
                $validated[$field] = $this->sanitize($value);
            }
        }
        
        if (!empty($errors)) {
            foreach ($errors as $field => $error) {
                echo "      ⚠️  Validation $field: $error\n";
            }
        }
        
        return $validated;
    }
    
    /**
     * Valider un champ selon ses règles
     */
    private function validateField($field, $value, $rules) {
        $type = $rules['type'] ?? 'string';
        
        switch ($type) {
            case 'float':
                return $this->validateFloat($value, $rules);
            
            case 'email':
                return $this->validateEmail($value);
            
            case 'url':
                return $this->validateUrl($value, $rules);
            
            case 'phone':
                return $this->validatePhone($value);
            
            case 'siret':
                return $this->validateSiret($value);
            
            case 'postal_code':
                return $this->validatePostalCode($value);
            
            case 'string':
            default:
                return $this->validateString($value, $rules);
        }
    }
    
    /**
     * Valider un float
     */
    private function validateFloat($value, $rules) {
        $float = (float)$value;
        
        if (isset($rules['min']) && $float < $rules['min']) {
            return ['valid' => false, 'error' => "Valeur trop petite (min: {$rules['min']})"];
        }
        
        if (isset($rules['max']) && $float > $rules['max']) {
            return ['valid' => false, 'error' => "Valeur trop grande (max: {$rules['max']})"];
        }
        
        return ['valid' => true, 'value' => $float];
    }
    
    /**
     * Valider un email
     */
    private function validateEmail($value) {
        $email = filter_var($value, FILTER_VALIDATE_EMAIL);
        
        if ($email === false) {
            return ['valid' => false, 'error' => 'Email invalide'];
        }
        
        return ['valid' => true, 'value' => strtolower($email)];
    }
    
    /**
     * Valider une URL
     */
    private function validateUrl($value, $rules) {
        $url = filter_var($value, FILTER_VALIDATE_URL);
        
        if ($url === false) {
            return ['valid' => false, 'error' => 'URL invalide'];
        }
        
        // Vérifier le domaine si spécifié
        if (isset($rules['domain'])) {
            $parsed = parse_url($url);
            $host = $parsed['host'] ?? '';
            
            if (strpos($host, $rules['domain']) === false) {
                return ['valid' => false, 'error' => "Domaine attendu: {$rules['domain']}"];
            }
        }
        
        return ['valid' => true, 'value' => $url];
    }
    
    /**
     * Valider un numéro de téléphone
     */
    private function validatePhone($value) {
        // Nettoyer le numéro
        $phone = preg_replace('/[^0-9+\s\-\(\)]/', '', $value);
        
        if (strlen($phone) < 10) {
            return ['valid' => false, 'error' => 'Numéro trop court'];
        }
        
        return ['valid' => true, 'value' => $phone];
    }
    
    /**
     * Valider un SIRET
     */
    private function validateSiret($value) {
        $siret = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($siret) !== 14) {
            return ['valid' => false, 'error' => 'SIRET doit contenir 14 chiffres'];
        }
        
        return ['valid' => true, 'value' => $siret];
    }
    
    /**
     * Valider un code postal
     */
    private function validatePostalCode($value) {
        $postal = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($postal) !== 5) {
            return ['valid' => false, 'error' => 'Code postal invalide'];
        }
        
        return ['valid' => true, 'value' => $postal];
    }
    
    /**
     * Valider une chaîne
     */
    private function validateString($value, $rules) {
        $string = $this->sanitize($value);
        
        if (isset($rules['max_length']) && strlen($string) > $rules['max_length']) {
            $string = substr($string, 0, $rules['max_length']);
        }
        
        return ['valid' => true, 'value' => $string];
    }
    
    /**
     * Nettoyer une valeur
     */
    private function sanitize($value) {
        if (is_string($value)) {
            $value = trim($value);
            $value = strip_tags($value);
            $value = htmlspecialchars_decode($value);
        }
        
        return $value;
    }
    
    /**
     * Vérifier la cohérence entre plusieurs champs
     */
    public function checkConsistency($data) {
        $warnings = [];
        
        // Vérifier que latitude et longitude sont ensemble
        if (isset($data['latitude']) xor isset($data['longitude'])) {
            $warnings[] = 'Latitude et longitude doivent être définies ensemble';
        }
        
        // Vérifier que l'adresse contient ville/code postal
        if (isset($data['adresse']) && !isset($data['ville'])) {
            $warnings[] = 'Adresse définie sans ville';
        }
        
        return $warnings;
    }
}
