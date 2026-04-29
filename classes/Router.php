<?php
class Router {
    private $routes = [];
    
    public function __construct() {
        $this->routes = [
            // Routes du magasin
            'magasin/commandes/liste' => [
                'file' => '/modules/magasin/commandes/liste.php',
                'title' => 'Liste des commandes'
            ],
            'magasin/commandes/ajouter' => [
                'file' => '/modules/magasin/commandes/ajouter.php',
                'title' => 'Ajouter une commande'
            ],
            'magasin/commandes/voir' => [
                'file' => '/modules/magasin/commandes/voir.php',
                'title' => 'Détails de la commande'
            ],
            'magasin/fournisseurs/liste' => [
                'file' => '/modules/magasin/fournisseurs/liste.php',
                'title' => 'Liste des fournisseurs'
            ],
            'magasin/fournisseurs/voir' => [
                'file' => '/modules/magasin/fournisseurs/voir.php',
                'title' => 'Détails du fournisseur'
            ],
            'magasin/recherche' => [
                'file' => '/modules/magasin/recherche.php',
                'title' => 'Recherche magasin'
            ],
            
            // Nouvelles routes pages principales (migration)
            'home' => [
                'file' => '/pages/home.php',
                'title' => 'Accueil'
            ],
            'annuaire' => [
                'file' => '/pages/annuaire.php',
                'title' => 'Annuaire'
            ],
            'autorite' => [
                'file' => '/pages/autorite.php',
                'title' => 'Autorité'
            ],
            'cadran' => [
                'file' => '/pages/cadran.php',
                'title' => 'Cadran'
            ],
            'banque' => [
                'file' => '/pages/banque.php',
                'title' => 'Banque'
            ],
            'ecran' => [
                'file' => '/pages/ecran.php',
                'title' => 'Ecran'
            ],
            'registre' => [
                'file' => '/pages/registre.php',
                'title' => 'Registre'
            ],
            
            // Routes des outils
            'outils' => [
                'file' => '/modules/outils/index.php',
                'title' => 'Outils'
            ],
            
            // Routes des formations
            'formations' => [
                'file' => '/templates/formations/index.php',
                'title' => 'Formations'
            ],
            
            // Routes du registre
            'registre-old' => [
                'file' => '/modules/registre/index.php',
                'title' => 'Registre'
            ],
            
            // Routes des outils externes
            'outils-externes' => [
                'file' => '/modules/outils-externes/index.php',
                'title' => 'Outils externes'
            ],
            
            // Routes des articles
            'articles' => [
                'file' => '/modules/articles/index.php',
                'title' => 'Articles'
            ]
        ];
    }
    
    public function dispatch() {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $uri = explode('?', $uri)[0];
        
        // Retirer le préfixe weball de l'URI
        $uri = str_replace('weball/', '', $uri);
        
        // Démarrer la mise en tampon
        ob_start();
        
        $pageTitle = 'WAO';
        $contentFile = BASE_PATH . '/templates/home.php';
        
        if (!empty($uri)) {
            foreach ($this->routes as $route => $config) {
                if ($uri === $route) {
                    if (file_exists(BASE_PATH . $config['file'])) {
                        $pageTitle = $config['title'];
                        $contentFile = BASE_PATH . $config['file'];
                        break;
                    }
                }
            }
        }
        
        // Inclure le fichier de contenu
        if (file_exists($contentFile)) {
            include $contentFile;
        } else {
            include BASE_PATH . '/templates/error/404.php';
        }
        
        // Capturer le contenu
        $content = ob_get_clean();
        
        // Inclure le template principal
        require BASE_PATH . '/includes/template.php';
    }
}