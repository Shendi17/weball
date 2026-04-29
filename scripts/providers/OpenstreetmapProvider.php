<?php
/**
 * Provider OpenStreetMap (Nominatim)
 * Récupère les coordonnées GPS et adresses
 */

require_once __DIR__ . '/../core/BaseProvider.php';

class OpenstreetmapProvider extends BaseProvider {
    private $nominatimUrl = 'https://nominatim.openstreetmap.org';
    
    public function getName() {
        return 'openstreetmap';
    }
    
    public function fetchData($autorite) {
        $data = [];
        
        try {
            // Construire la requête de recherche
            $query = $autorite['nom'];
            if (!empty($autorite['ville'])) {
                $query .= ', ' . $autorite['ville'];
            }
            if (!empty($autorite['pays'])) {
                $query .= ', ' . $autorite['pays'];
            }
            
            // Rechercher le lieu
            $place = $this->searchPlace($query);
            
            if (!$place) {
                return $data;
            }
            
            // Extraire les coordonnées
            if (isset($place['lat'], $place['lon'])) {
                $data['latitude'] = (float)$place['lat'];
                $data['longitude'] = (float)$place['lon'];
            }
            
            // Extraire l'adresse
            if (isset($place['address'])) {
                $address = $place['address'];
                
                // Adresse complète
                if (isset($place['display_name'])) {
                    $data['adresse'] = $this->formatAddress($place['display_name']);
                }
                
                // Code postal
                if (isset($address['postcode'])) {
                    $data['code_postal'] = $address['postcode'];
                }
                
                // Ville
                if (isset($address['city'])) {
                    $data['ville'] = $address['city'];
                } elseif (isset($address['town'])) {
                    $data['ville'] = $address['town'];
                } elseif (isset($address['village'])) {
                    $data['ville'] = $address['village'];
                }
                
                // Région
                if (isset($address['state'])) {
                    $data['region'] = $address['state'];
                }
                
                // Pays
                if (isset($address['country'])) {
                    $data['pays'] = $address['country'];
                }
            }
            
        } catch (Exception $e) {
            $this->log("Erreur: " . $e->getMessage(), 'error');
        }
        
        return $data;
    }
    
    /**
     * Rechercher un lieu via Nominatim
     */
    private function searchPlace($query) {
        $params = [
            'q' => $query,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 1,
            'accept-language' => 'fr'
        ];
        
        $url = $this->nominatimUrl . '/search?' . http_build_query($params);
        
        // Nominatim exige un User-Agent spécifique
        $headers = [
            'User-Agent: WeBall Data Sync Bot/1.0 (contact@weball.local)'
        ];
        
        $response = $this->httpGetJson($url, $headers);
        
        if (!empty($response) && isset($response[0])) {
            return $response[0];
        }
        
        return null;
    }
    
    /**
     * Recherche inversée (coordonnées vers adresse)
     */
    public function reverseGeocode($lat, $lon) {
        $params = [
            'lat' => $lat,
            'lon' => $lon,
            'format' => 'json',
            'addressdetails' => 1,
            'accept-language' => 'fr'
        ];
        
        $url = $this->nominatimUrl . '/reverse?' . http_build_query($params);
        
        $headers = [
            'User-Agent: WeBall Data Sync Bot/1.0 (contact@weball.local)'
        ];
        
        try {
            $response = $this->httpGetJson($url, $headers);
            return $response;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Formater une adresse
     */
    private function formatAddress($displayName) {
        // Nettoyer l'adresse (enlever les parties redondantes)
        $parts = explode(',', $displayName);
        
        // Garder les 3-4 premières parties (numéro, rue, ville, pays)
        $formatted = array_slice($parts, 0, min(4, count($parts)));
        
        return implode(', ', array_map('trim', $formatted));
    }
}
