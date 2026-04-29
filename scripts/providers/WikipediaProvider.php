<?php
/**
 * Provider Wikipedia
 * Récupère les données depuis l'API Wikipedia
 */

require_once __DIR__ . '/../core/BaseProvider.php';

class WikipediaProvider extends BaseProvider {
    private $apiUrl = 'https://fr.wikipedia.org/w/api.php';
    
    public function getName() {
        return 'wikipedia';
    }
    
    public function fetchData($autorite) {
        $data = [];
        
        try {
            // Rechercher la page Wikipedia
            $pageTitle = $this->findWikipediaPage($autorite['nom']);
            
            if (!$pageTitle) {
                return $data;
            }
            
            // Récupérer le contenu de la page
            $pageData = $this->getPageData($pageTitle);
            
            if (!$pageData) {
                return $data;
            }
            
            // Extraire les informations
            $data['wikipedia_url'] = "https://fr.wikipedia.org/wiki/" . urlencode(str_replace(' ', '_', $pageTitle));
            
            // Extraire la description (premier paragraphe)
            if (isset($pageData['extract'])) {
                $extract = $this->cleanString($pageData['extract']);
                if (strlen($extract) > 200) {
                    $data['description_longue'] = $extract;
                } else {
                    $data['description'] = $extract;
                }
            }
            
            // Extraire les coordonnées si disponibles
            if (isset($pageData['coordinates'][0])) {
                $data['latitude'] = $pageData['coordinates'][0]['lat'];
                $data['longitude'] = $pageData['coordinates'][0]['lon'];
            }
            
            // Extraire l'image principale
            if (isset($pageData['original']['source'])) {
                $data['logo'] = $pageData['original']['source'];
            }
            
        } catch (Exception $e) {
            $this->log("Erreur: " . $e->getMessage(), 'error');
        }
        
        return $data;
    }
    
    /**
     * Rechercher une page Wikipedia par nom
     */
    private function findWikipediaPage($name) {
        $params = [
            'action' => 'query',
            'format' => 'json',
            'list' => 'search',
            'srsearch' => $name,
            'srlimit' => 1
        ];
        
        $url = $this->apiUrl . '?' . http_build_query($params);
        $response = $this->httpGetJson($url);
        
        if (isset($response['query']['search'][0]['title'])) {
            return $response['query']['search'][0]['title'];
        }
        
        return null;
    }
    
    /**
     * Récupérer les données d'une page
     */
    private function getPageData($title) {
        $params = [
            'action' => 'query',
            'format' => 'json',
            'titles' => $title,
            'prop' => 'extracts|coordinates|pageimages|info',
            'exintro' => true,
            'explaintext' => true,
            'piprop' => 'original',
            'inprop' => 'url'
        ];
        
        $url = $this->apiUrl . '?' . http_build_query($params);
        $response = $this->httpGetJson($url);
        
        if (isset($response['query']['pages'])) {
            $pages = $response['query']['pages'];
            $page = reset($pages);
            
            if (isset($page['pageid'])) {
                return $page;
            }
        }
        
        return null;
    }
    
    /**
     * Extraire l'historique depuis le contenu complet
     */
    public function getFullContent($title) {
        $params = [
            'action' => 'query',
            'format' => 'json',
            'titles' => $title,
            'prop' => 'extracts',
            'explaintext' => true
        ];
        
        $url = $this->apiUrl . '?' . http_build_query($params);
        $response = $this->httpGetJson($url);
        
        if (isset($response['query']['pages'])) {
            $pages = $response['query']['pages'];
            $page = reset($pages);
            
            if (isset($page['extract'])) {
                return $page['extract'];
            }
        }
        
        return null;
    }
}
