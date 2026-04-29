<?php
/**
 * Provider Wikidata
 * Récupère les données structurées depuis Wikidata
 */

require_once __DIR__ . '/../core/BaseProvider.php';

class WikidataProvider extends BaseProvider {
    private $apiUrl = 'https://www.wikidata.org/w/api.php';
    private $sparqlUrl = 'https://query.wikidata.org/sparql';
    
    public function getName() {
        return 'wikidata';
    }
    
    public function fetchData($autorite) {
        $data = [];
        
        try {
            // Rechercher l'entité Wikidata
            $entityId = $this->findEntity($autorite['nom']);
            
            if (!$entityId) {
                return $data;
            }
            
            // Récupérer les données de l'entité
            $entityData = $this->getEntityData($entityId);
            
            if (!$entityData) {
                return $data;
            }
            
            // Extraire les propriétés importantes
            $claims = $entityData['claims'] ?? [];
            
            // P31: instance de (type d'organisation)
            // P159: siège social (coordonnées)
            if (isset($claims['P159'][0]['mainsnak']['datavalue']['value'])) {
                $headquarters = $claims['P159'][0]['mainsnak']['datavalue']['value'];
                if (isset($headquarters['latitude'], $headquarters['longitude'])) {
                    $data['latitude'] = $headquarters['latitude'];
                    $data['longitude'] = $headquarters['longitude'];
                }
            }
            
            // P571: date de création
            if (isset($claims['P571'][0]['mainsnak']['datavalue']['value']['time'])) {
                $time = $claims['P571'][0]['mainsnak']['datavalue']['value']['time'];
                $data['date_creation_org'] = $this->formatWikidataDate($time);
            }
            
            // P1128: effectifs
            if (isset($claims['P1128'][0]['mainsnak']['datavalue']['value']['amount'])) {
                $amount = $claims['P1128'][0]['mainsnak']['datavalue']['value']['amount'];
                $data['effectifs'] = number_format(abs($amount), 0, ',', ' ') . ' employés';
            }
            
            // P2139: chiffre d'affaires
            if (isset($claims['P2139'][0]['mainsnak']['datavalue']['value']['amount'])) {
                $amount = $claims['P2139'][0]['mainsnak']['datavalue']['value']['amount'];
                $data['chiffre_affaires'] = number_format(abs($amount), 0, ',', ' ') . ' €';
            }
            
            // P856: site web officiel
            if (isset($claims['P856'][0]['mainsnak']['datavalue']['value'])) {
                $data['site_web'] = $claims['P856'][0]['mainsnak']['datavalue']['value'];
            }
            
            // P1454: forme juridique
            if (isset($claims['P1454'][0]['mainsnak']['datavalue']['value']['id'])) {
                $legalFormId = $claims['P1454'][0]['mainsnak']['datavalue']['value']['id'];
                $legalForm = $this->getEntityLabel($legalFormId);
                if ($legalForm) {
                    $data['forme_juridique'] = $legalForm;
                }
            }
            
            // P414: bourse
            if (isset($claims['P414'][0]['mainsnak']['datavalue']['value']['id'])) {
                $exchangeId = $claims['P414'][0]['mainsnak']['datavalue']['value']['id'];
                $exchange = $this->getEntityLabel($exchangeId);
                if ($exchange) {
                    $data['bourse_cotation'] = $exchange;
                }
            }
            
            // P249: code ticker
            if (isset($claims['P249'][0]['mainsnak']['datavalue']['value'])) {
                $data['code_bourse'] = $claims['P249'][0]['mainsnak']['datavalue']['value'];
            }
            
            // P2427: identifiant SIRET (rare sur Wikidata)
            if (isset($claims['P2427'][0]['mainsnak']['datavalue']['value'])) {
                $data['siret'] = $claims['P2427'][0]['mainsnak']['datavalue']['value'];
            }
            
            // P17: pays
            if (isset($claims['P17'][0]['mainsnak']['datavalue']['value']['id'])) {
                $countryId = $claims['P17'][0]['mainsnak']['datavalue']['value']['id'];
                $country = $this->getEntityLabel($countryId);
                if ($country) {
                    $data['pays'] = $country;
                }
            }
            
            // P452: secteur d'activité
            if (isset($claims['P452'][0]['mainsnak']['datavalue']['value']['id'])) {
                $industryId = $claims['P452'][0]['mainsnak']['datavalue']['value']['id'];
                $industry = $this->getEntityLabel($industryId);
                if ($industry) {
                    $data['secteur'] = $industry;
                }
            }
            
            // P169: PDG/dirigeant
            if (isset($claims['P169'][0]['mainsnak']['datavalue']['value']['id'])) {
                $ceoId = $claims['P169'][0]['mainsnak']['datavalue']['value']['id'];
                $ceo = $this->getEntityLabel($ceoId);
                if ($ceo) {
                    $data['dirigeant'] = $ceo;
                }
            }
            
            // Liens sociaux
            // P2003: Instagram
            if (isset($claims['P2003'][0]['mainsnak']['datavalue']['value'])) {
                $data['instagram_url'] = 'https://www.instagram.com/' . $claims['P2003'][0]['mainsnak']['datavalue']['value'];
            }
            
            // P2002: Twitter
            if (isset($claims['P2002'][0]['mainsnak']['datavalue']['value'])) {
                $data['twitter_url'] = 'https://twitter.com/' . $claims['P2002'][0]['mainsnak']['datavalue']['value'];
            }
            
            // P2013: Facebook
            if (isset($claims['P2013'][0]['mainsnak']['datavalue']['value'])) {
                $data['facebook_url'] = 'https://www.facebook.com/' . $claims['P2013'][0]['mainsnak']['datavalue']['value'];
            }
            
            // P4264: LinkedIn
            if (isset($claims['P4264'][0]['mainsnak']['datavalue']['value'])) {
                $data['linkedin_url'] = 'https://www.linkedin.com/company/' . $claims['P4264'][0]['mainsnak']['datavalue']['value'];
            }
            
            // P2397: YouTube
            if (isset($claims['P2397'][0]['mainsnak']['datavalue']['value'])) {
                $data['youtube_url'] = 'https://www.youtube.com/channel/' . $claims['P2397'][0]['mainsnak']['datavalue']['value'];
            }
            
        } catch (Exception $e) {
            $this->log("Erreur: " . $e->getMessage(), 'error');
        }
        
        return $data;
    }
    
    /**
     * Rechercher une entité Wikidata par nom
     */
    private function findEntity($name) {
        $params = [
            'action' => 'wbsearchentities',
            'format' => 'json',
            'language' => 'fr',
            'type' => 'item',
            'search' => $name,
            'limit' => 1
        ];
        
        $url = $this->apiUrl . '?' . http_build_query($params);
        $response = $this->httpGetJson($url);
        
        if (isset($response['search'][0]['id'])) {
            return $response['search'][0]['id'];
        }
        
        return null;
    }
    
    /**
     * Récupérer les données d'une entité
     */
    private function getEntityData($entityId) {
        $params = [
            'action' => 'wbgetentities',
            'format' => 'json',
            'ids' => $entityId,
            'languages' => 'fr'
        ];
        
        $url = $this->apiUrl . '?' . http_build_query($params);
        $response = $this->httpGetJson($url);
        
        if (isset($response['entities'][$entityId])) {
            return $response['entities'][$entityId];
        }
        
        return null;
    }
    
    /**
     * Obtenir le label d'une entité
     */
    private function getEntityLabel($entityId) {
        $entityData = $this->getEntityData($entityId);
        
        if (isset($entityData['labels']['fr']['value'])) {
            return $entityData['labels']['fr']['value'];
        }
        
        if (isset($entityData['labels']['en']['value'])) {
            return $entityData['labels']['en']['value'];
        }
        
        return null;
    }
    
    /**
     * Formater une date Wikidata
     */
    private function formatWikidataDate($wikidataTime) {
        // Format: +1970-12-31T00:00:00Z
        preg_match('/([+-]\d+)-(\d+)-(\d+)/', $wikidataTime, $matches);
        
        if (count($matches) >= 4) {
            $year = abs((int)$matches[1]);
            return $year;
        }
        
        return null;
    }
}
