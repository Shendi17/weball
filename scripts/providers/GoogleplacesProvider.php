<?php
/**
 * Provider Google Places
 * Récupère horaires, téléphone, adresse depuis Google Places API
 * NÉCESSITE UNE CLÉ API (payant)
 */

require_once __DIR__ . '/../core/BaseProvider.php';

class GoogleplacesProvider extends BaseProvider {
    private $apiUrl = 'https://maps.googleapis.com/maps/api/place';
    
    public function getName() {
        return 'googleplaces';
    }
    
    public function requiresApiKey() {
        return true;
    }
    
    public function fetchData($autorite) {
        if (!$this->hasApiKey()) {
            throw new Exception("Clé API Google Places manquante");
        }
        
        $data = [];
        
        try {
            // Rechercher le lieu
            $placeId = $this->findPlace($autorite['nom'], $autorite['ville'] ?? null);
            
            if (!$placeId) {
                return $data;
            }
            
            // Récupérer les détails
            $details = $this->getPlaceDetails($placeId);
            
            if (!$details) {
                return $data;
            }
            
            // Extraire les informations
            if (isset($details['formatted_phone_number'])) {
                $data['telephone'] = $details['formatted_phone_number'];
            }
            
            if (isset($details['formatted_address'])) {
                $data['adresse'] = $details['formatted_address'];
            }
            
            if (isset($details['website'])) {
                $data['site_web'] = $details['website'];
            }
            
            // Horaires d'ouverture
            if (isset($details['opening_hours']['weekday_text'])) {
                $data['horaires'] = implode("\n", $details['opening_hours']['weekday_text']);
            }
            
            // Coordonnées GPS
            if (isset($details['geometry']['location'])) {
                $data['latitude'] = $details['geometry']['location']['lat'];
                $data['longitude'] = $details['geometry']['location']['lng'];
            }
            
            // Extraire ville et code postal depuis address_components
            if (isset($details['address_components'])) {
                foreach ($details['address_components'] as $component) {
                    if (in_array('locality', $component['types'])) {
                        $data['ville'] = $component['long_name'];
                    }
                    if (in_array('postal_code', $component['types'])) {
                        $data['code_postal'] = $component['long_name'];
                    }
                }
            }
            
        } catch (Exception $e) {
            $this->log("Erreur: " . $e->getMessage(), 'error');
        }
        
        return $data;
    }
    
    /**
     * Rechercher un lieu
     */
    private function findPlace($name, $city = null) {
        $query = $name;
        if ($city) {
            $query .= ' ' . $city;
        }
        
        $params = [
            'input' => $query,
            'inputtype' => 'textquery',
            'fields' => 'place_id',
            'key' => $this->apiKey
        ];
        
        $url = $this->apiUrl . '/findplacefromtext/json?' . http_build_query($params);
        $response = $this->httpGetJson($url);
        
        if (isset($response['candidates'][0]['place_id'])) {
            return $response['candidates'][0]['place_id'];
        }
        
        return null;
    }
    
    /**
     * Récupérer les détails d'un lieu
     */
    private function getPlaceDetails($placeId) {
        $params = [
            'place_id' => $placeId,
            'fields' => 'formatted_address,formatted_phone_number,website,opening_hours,geometry,address_components',
            'key' => $this->apiKey
        ];
        
        $url = $this->apiUrl . '/details/json?' . http_build_query($params);
        $response = $this->httpGetJson($url);
        
        if (isset($response['result'])) {
            return $response['result'];
        }
        
        return null;
    }
}
