<?php
/**
 * Provider IA (OpenAI GPT-4 ou Claude)
 * Enrichissement avancé avec IA générative
 * NÉCESSITE UNE CLÉ API (payant)
 */

require_once __DIR__ . '/../core/BaseProvider.php';

class AiProvider extends BaseProvider {
    private $openaiUrl = 'https://api.openai.com/v1/chat/completions';
    private $model = 'gpt-4o-mini'; // Moins cher que gpt-4
    
    public function getName() {
        return 'ai';
    }
    
    public function requiresApiKey() {
        return true;
    }
    
    public function fetchData($autorite) {
        if (!$this->hasApiKey()) {
            throw new Exception("Clé API OpenAI manquante");
        }
        
        $data = [];
        
        try {
            // Générer l'historique si manquant
            if (empty($autorite['historique'])) {
                $historique = $this->generateHistorique($autorite);
                if ($historique) {
                    $data['historique'] = $historique;
                }
            }
            
            // Générer les missions si manquantes
            if (empty($autorite['missions'])) {
                $missions = $this->generateMissions($autorite);
                if ($missions) {
                    $data['missions'] = $missions;
                }
            }
            
            // Générer les valeurs si manquantes
            if (empty($autorite['valeurs'])) {
                $valeurs = $this->generateValeurs($autorite);
                if ($valeurs) {
                    $data['valeurs'] = $valeurs;
                }
            }
            
        } catch (Exception $e) {
            $this->log("Erreur: " . $e->getMessage(), 'error');
        }
        
        return $data;
    }
    
    /**
     * Générer l'historique de l'organisation
     */
    private function generateHistorique($autorite) {
        $prompt = "Tu es un expert en histoire d'entreprise. Rédige un historique concis (150-200 mots) de {$autorite['nom']}";
        
        if (!empty($autorite['date_creation_org'])) {
            $prompt .= ", fondée en {$autorite['date_creation_org']}";
        }
        
        if (!empty($autorite['pays'])) {
            $prompt .= " ({$autorite['pays']})";
        }
        
        $prompt .= ". Inclus les dates clés, les moments importants et l'évolution de l'entreprise. Sois factuel et précis.";
        
        return $this->callOpenAI($prompt);
    }
    
    /**
     * Générer les missions de l'organisation
     */
    private function generateMissions($autorite) {
        $prompt = "Décris en 100-150 mots les missions principales de {$autorite['nom']}";
        
        if (!empty($autorite['secteur'])) {
            $prompt .= " dans le secteur {$autorite['secteur']}";
        }
        
        $prompt .= ". Sois concis et professionnel.";
        
        return $this->callOpenAI($prompt);
    }
    
    /**
     * Générer les valeurs de l'organisation
     */
    private function generateValeurs($autorite) {
        $prompt = "Liste les 4-5 valeurs principales de {$autorite['nom']} sous forme de mots-clés séparés par des virgules (ex: Innovation, Excellence, Responsabilité). Sois concis.";
        
        return $this->callOpenAI($prompt);
    }
    
    /**
     * Appeler l'API OpenAI
     */
    private function callOpenAI($prompt) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];
        
        $payload = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Tu es un assistant qui fournit des informations factuelles et concises sur les entreprises et organisations.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3, // Plus factuel
            'max_tokens' => 500
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->openaiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Erreur API OpenAI: HTTP $httpCode");
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return trim($data['choices'][0]['message']['content']);
        }
        
        return null;
    }
    
    /**
     * Résumer un texte long
     */
    public function summarizeText($text, $maxWords = 150) {
        $prompt = "Résume ce texte en maximum $maxWords mots, en gardant les informations essentielles:\n\n$text";
        return $this->callOpenAI($prompt);
    }
    
    /**
     * Extraire des chiffres clés depuis un texte
     */
    public function extractKeyFigures($text) {
        $prompt = "Extrais les chiffres clés importants de ce texte et retourne-les au format JSON avec {\"label\": \"description\", \"valeur\": \"chiffre\"}:\n\n$text";
        
        $result = $this->callOpenAI($prompt);
        
        try {
            return json_decode($result, true);
        } catch (Exception $e) {
            return null;
        }
    }
}
