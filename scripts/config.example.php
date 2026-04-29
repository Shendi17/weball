<?php
/**
 * Configuration du système de synchronisation
 * Copiez ce fichier en config.php et ajustez les paramètres
 */

return [
    // Configuration des providers
    'providers' => [
        'wikipedia' => [
            'enabled' => true,
            'priority' => 1,
            'description' => 'API Wikipedia - Descriptions, historique, images'
        ],
        'wikidata' => [
            'enabled' => true,
            'priority' => 2,
            'description' => 'API Wikidata - Données structurées (SIRET, effectifs, etc.)'
        ],
        'openstreetmap' => [
            'enabled' => true,
            'priority' => 3,
            'description' => 'Nominatim OSM - Coordonnées GPS et adresses'
        ],
        'googleplaces' => [
            'enabled' => false,  // Nécessite clé API
            'priority' => 4,
            'description' => 'Google Places - Horaires, téléphone (PAYANT)'
        ],
        'clearbit' => [
            'enabled' => false,  // Nécessite clé API
            'priority' => 5,
            'description' => 'Clearbit - Logos HD, infos entreprise (PAYANT)'
        ],
        'ai' => [
            'enabled' => false,  // Nécessite clé API
            'priority' => 10,
            'description' => 'OpenAI GPT-4 - Enrichissement IA (PAYANT)'
        ]
    ],
    
    // Clés API (à configurer via variables d'environnement de préférence)
    'api_keys' => [
        'google_places' => getenv('GOOGLE_PLACES_API_KEY') ?: null,
        'clearbit' => getenv('CLEARBIT_API_KEY') ?: null,
        'openai' => getenv('OPENAI_API_KEY') ?: null,
    ],
    
    // Options de synchronisation
    'options' => [
        // Mode test sans sauvegarde
        'dry_run' => false,
        
        // Forcer la mise à jour même si données existantes
        'force_update' => false,
        
        // Résoudre automatiquement les conflits
        'auto_resolve_conflicts' => true,
        
        // Nombre de requêtes simultanées
        'max_concurrent' => 5,
        
        // Délai entre les requêtes (ms)
        'delay_between_requests' => 1000,
        
        // Timeout pour les requêtes HTTP (secondes)
        'http_timeout' => 30,
        
        // Générer un rapport HTML après chaque sync
        'generate_html_report' => true,
        
        // Envoyer une notification par email après sync
        'email_notification' => false,
        'notification_email' => 'admin@weball.local'
    ],
    
    // Champs critiques (déclenchent une mise à jour si vides)
    'critical_fields' => [
        'latitude',
        'longitude',
        'historique',
        'siret',
        'wikipedia_url',
        'linkedin_url'
    ],
    
    // Champs à ne jamais écraser (même en mode force)
    'protected_fields' => [
        'id',
        'nom',
        'date_creation',
        'date_modification'
    ],
    
    // Configuration des logs
    'logging' => [
        'enabled' => true,
        'level' => 'info', // debug, info, warning, error
        'retention_days' => 30, // Supprimer les logs après X jours
        'max_file_size' => 10485760 // 10 MB
    ],
    
    // Rate limiting par provider
    'rate_limits' => [
        'wikipedia' => 200,      // requêtes par seconde
        'wikidata' => 1000,      // requêtes par seconde
        'openstreetmap' => 1,    // requêtes par seconde (strict!)
        'googleplaces' => 10,    // requêtes par seconde
        'clearbit' => 5,         // requêtes par seconde
        'ai' => 3                // requêtes par seconde
    ],
    
    // Budget mensuel estimé (en euros)
    'budget' => [
        'google_places' => 50,
        'clearbit' => 99,
        'openai' => 50,
        'total_max' => 200
    ]
];
