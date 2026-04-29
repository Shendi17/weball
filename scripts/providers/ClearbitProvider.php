<?php
/**
 * Provider Clearbit
 * Récupère logos HD et informations entreprise
 * NÉCESSITE UNE CLÉ API (payant)
 */

require_once __DIR__ . '/../core/BaseProvider.php';

class ClearbitProvider extends BaseProvider {
    private $apiUrl = 'https://company.clearbit.com/v2/companies';
    
    public function getName() {
        return 'clearbit';
    }
    
    public function requiresApiKey() {
        return true;
    }
    
    public function fetchData($autorite) {
        if (!$this->hasApiKey()) {
            throw new Exception("Clé API Clearbit manquante");
        }
        
        $data = [];
        
        try {
            // Clearbit nécessite un domaine
            $domain = $this->extractDomainFromAutorite($autorite);
            
            if (!$domain) {
                return $data;
            }
            
            // Récupérer les données de l'entreprise
            $companyData = $this->getCompanyData($domain);
            
            if (!$companyData) {
                return $data;
            }
            
            // Logo HD
            if (isset($companyData['logo'])) {
                $data['logo'] = $companyData['logo'];
            }
            
            // Description
            if (isset($companyData['description'])) {
                $data['description'] = $companyData['description'];
            }
            
            // Effectifs
            if (isset($companyData['metrics']['employees'])) {
                $data['effectifs'] = number_format($companyData['metrics']['employees'], 0, ',', ' ') . ' employés';
            }
            
            // Chiffre d'affaires
            if (isset($companyData['metrics']['estimatedAnnualRevenue'])) {
                $revenue = $companyData['metrics']['estimatedAnnualRevenue'];
                $data['chiffre_affaires'] = number_format($revenue, 0, ',', ' ') . ' $';
            }
            
            // Secteur
            if (isset($companyData['category']['industry'])) {
                $data['secteur'] = $companyData['category']['industry'];
            }
            
            // Réseaux sociaux
            if (isset($companyData['twitter']['handle'])) {
                $data['twitter_url'] = 'https://twitter.com/' . $companyData['twitter']['handle'];
            }
            
            if (isset($companyData['facebook']['handle'])) {
                $data['facebook_url'] = 'https://facebook.com/' . $companyData['facebook']['handle'];
            }
            
            if (isset($companyData['linkedin']['handle'])) {
                $data['linkedin_url'] = 'https://linkedin.com/company/' . $companyData['linkedin']['handle'];
            }
            
            // Localisation
            if (isset($companyData['geo']['city'])) {
                $data['ville'] = $companyData['geo']['city'];
            }
            
            if (isset($companyData['geo']['country'])) {
                $data['pays'] = $companyData['geo']['country'];
            }
            
        } catch (Exception $e) {
            $this->log("Erreur: " . $e->getMessage(), 'error');
        }
        
        return $data;
    }
    
    /**
     * Récupérer les données d'une entreprise
     */
    private function getCompanyData($domain) {
        $url = $this->apiUrl . '/find?domain=' . urlencode($domain);
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey
        ];
        
        try {
            $response = $this->httpGetJson($url, $headers);
            return $response;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Extraire le domaine depuis les données de l'autorité
     */
    private function extractDomainFromAutorite($autorite) {
        // Essayer depuis site_web
        if (!empty($autorite['site_web'])) {
            return $this->extractDomain($autorite['site_web']);
        }
        
        // Essayer depuis email
        if (!empty($autorite['email'])) {
            $parts = explode('@', $autorite['email']);
            if (isset($parts[1])) {
                return $parts[1];
            }
        }
        
        return null;
    }
}
