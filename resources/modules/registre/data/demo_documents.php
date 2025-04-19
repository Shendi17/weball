<?php

/**
 * Données de démonstration pour le module Registre
 * Ces données servent d'exemple et peuvent être remplacées par des données réelles
 */

$demo_documents = [
    [
        'id' => 'doc-001',
        'title' => 'Procès-verbal Assemblée Générale 2024',
        'type' => 'legal',
        'category' => 'assemblees',
        'date' => '2024-01-15',
        'status' => 'approved',
        'author' => 'Marie Dubois',
        'description' => 'Procès-verbal de l\'assemblée générale ordinaire du 15 janvier 2024 incluant les décisions majeures et les votes des membres.',
        'content' => 'Assemblée Générale Ordinaire du 15 janvier 2024\n\nOrdre du jour :\n1. Rapport moral\n2. Rapport financier\n3. Élection du nouveau bureau\n4. Questions diverses',
        'tags' => ['AG', '2024', 'Procès-verbal'],
        'file' => 'pv_ag_2024.pdf',
        'size' => '2.4 MB',
        'created_at' => '2024-01-15 14:30:00',
        'updated_at' => '2024-01-15 16:45:00'
    ],
    [
        'id' => 'doc-002',
        'title' => 'Rapport Financier 2023',
        'type' => 'financial',
        'category' => 'rapports',
        'date' => '2024-01-10',
        'status' => 'pending',
        'author' => 'Pierre Martin',
        'description' => 'Bilan financier complet de l\'année 2023 incluant les états financiers, le compte de résultat et les prévisions budgétaires.',
        'content' => 'Rapport Financier 2023\n\nBilan :\n- Actifs : 450,000 €\n- Passifs : 120,000 €\n- Résultat net : +85,000 €',
        'tags' => ['Finance', '2023', 'Rapport', 'Bilan'],
        'file' => 'rapport_financier_2023.pdf',
        'size' => '3.1 MB',
        'created_at' => '2024-01-10 09:15:00',
        'updated_at' => '2024-01-10 15:30:00'
    ],
    [
        'id' => 'doc-003',
        'title' => 'Contrat de Partenariat TechCorp',
        'type' => 'contract',
        'category' => 'contrats',
        'date' => '2023-12-20',
        'status' => 'active',
        'author' => 'Sophie Bernard',
        'description' => 'Contrat de partenariat stratégique avec TechCorp pour le développement de solutions innovantes.',
        'content' => 'Contrat de Partenariat\n\nEntre :\nNotre Société\nEt\nTechCorp\n\nDurée : 3 ans\nMontant : 250,000 €',
        'tags' => ['Contrat', 'Partenariat', 'TechCorp'],
        'file' => 'contrat_techcorp_2023.pdf',
        'size' => '1.8 MB',
        'created_at' => '2023-12-20 11:00:00',
        'updated_at' => '2023-12-20 14:20:00'
    ],
    [
        'id' => 'doc-004',
        'title' => 'Règlement Intérieur 2024',
        'type' => 'legal',
        'category' => 'reglements',
        'date' => '2024-01-02',
        'status' => 'active',
        'author' => 'Jean Dupont',
        'description' => 'Nouveau règlement intérieur applicable à partir du 1er janvier 2024.',
        'content' => 'Règlement Intérieur 2024\n\nArticle 1 : Dispositions générales\nArticle 2 : Horaires de travail\nArticle 3 : Congés\nArticle 4 : Télétravail',
        'tags' => ['Règlement', '2024', 'RH'],
        'file' => 'reglement_interieur_2024.pdf',
        'size' => '1.2 MB',
        'created_at' => '2024-01-02 08:00:00',
        'updated_at' => '2024-01-02 10:30:00'
    ],
    [
        'id' => 'doc-005',
        'title' => 'Plan Stratégique 2024-2026',
        'type' => 'strategic',
        'category' => 'rapports',
        'date' => '2024-01-05',
        'status' => 'draft',
        'author' => 'Alexandre Martin',
        'description' => 'Plan stratégique triennal définissant les objectifs et les axes de développement pour 2024-2026.',
        'content' => 'Plan Stratégique 2024-2026\n\n1. Vision et Mission\n2. Objectifs stratégiques\n3. Plan d\'action\n4. Indicateurs de performance',
        'tags' => ['Stratégie', '2024-2026', 'Développement'],
        'file' => 'plan_strategique_2024_2026.pdf',
        'size' => '4.5 MB',
        'created_at' => '2024-01-05 13:45:00',
        'updated_at' => '2024-01-05 17:20:00'
    ],
    [
        'id' => 'doc-006',
        'title' => 'Compte-rendu CODIR Janvier 2024',
        'type' => 'meeting',
        'category' => 'rapports',
        'date' => '2024-01-08',
        'status' => 'pending',
        'author' => 'Marie Dubois',
        'description' => 'Compte-rendu de la réunion du comité de direction de janvier 2024.',
        'content' => 'CODIR - Janvier 2024\n\nParticipants :\n- Direction Générale\n- Direction Financière\n- Direction Marketing\n\nPoints abordés :\n1. Résultats 2023\n2. Objectifs 2024\n3. Nouveaux projets',
        'tags' => ['CODIR', '2024', 'Réunion'],
        'file' => 'cr_codir_janvier_2024.pdf',
        'size' => '1.5 MB',
        'created_at' => '2024-01-08 14:00:00',
        'updated_at' => '2024-01-08 16:30:00'
    ],
    [
        'id' => 'doc-007',
        'title' => 'Politique RGPD',
        'type' => 'legal',
        'category' => 'reglements',
        'date' => '2023-12-15',
        'status' => 'active',
        'author' => 'Claire Dubois',
        'description' => 'Politique de protection des données personnelles conforme au RGPD.',
        'content' => 'Politique RGPD\n\n1. Collecte des données\n2. Traitement des données\n3. Droits des utilisateurs\n4. Sécurité des données',
        'tags' => ['RGPD', 'Protection données', 'Legal'],
        'file' => 'politique_rgpd_2023.pdf',
        'size' => '2.8 MB',
        'created_at' => '2023-12-15 09:30:00',
        'updated_at' => '2023-12-15 11:45:00'
    ],
    [
        'id' => 'doc-008',
        'title' => 'Budget Prévisionnel 2024',
        'type' => 'financial',
        'category' => 'rapports',
        'date' => '2023-12-10',
        'status' => 'approved',
        'author' => 'Pierre Martin',
        'description' => 'Budget prévisionnel détaillé pour l\'année 2024 par département.',
        'content' => 'Budget 2024\n\nRecettes prévisionnelles : 2,500,000 €\nDépenses prévisionnelles : 2,100,000 €\nInvestissements prévus : 300,000 €',
        'tags' => ['Budget', '2024', 'Finance'],
        'file' => 'budget_2024.pdf',
        'size' => '3.2 MB',
        'created_at' => '2023-12-10 10:00:00',
        'updated_at' => '2023-12-10 14:15:00'
    ],
    [
        'id' => 'doc-009',
        'title' => 'Contrat Maintenance Informatique',
        'type' => 'contract',
        'category' => 'contrats',
        'date' => '2024-01-03',
        'status' => 'active',
        'author' => 'Thomas Robert',
        'description' => 'Contrat de maintenance du parc informatique avec IT Services Plus.',
        'content' => 'Contrat de Maintenance\n\nPrestataire : IT Services Plus\nDurée : 12 mois\nMontant mensuel : 2,500 €\nServices inclus :\n- Maintenance préventive\n- Support utilisateurs\n- Sauvegardes',
        'tags' => ['IT', 'Maintenance', 'Contrat'],
        'file' => 'contrat_maintenance_it_2024.pdf',
        'size' => '2.1 MB',
        'created_at' => '2024-01-03 11:30:00',
        'updated_at' => '2024-01-03 14:45:00'
    ],
    [
        'id' => 'doc-010',
        'title' => 'Manuel Qualité 2024',
        'type' => 'procedure',
        'category' => 'reglements',
        'date' => '2024-01-04',
        'status' => 'active',
        'author' => 'Sophie Martin',
        'description' => 'Manuel qualité détaillant les procédures et normes qualité de l\'entreprise.',
        'content' => 'Manuel Qualité\n\n1. Politique qualité\n2. Objectifs qualité\n3. Procédures\n4. Indicateurs\n5. Actions correctives',
        'tags' => ['Qualité', '2024', 'Procédures'],
        'file' => 'manuel_qualite_2024.pdf',
        'size' => '5.6 MB',
        'created_at' => '2024-01-04 09:00:00',
        'updated_at' => '2024-01-04 16:30:00'
    ]
];

// Historique des documents
$demo_document_history = [
    'doc-001' => [
        [
            'action' => 'created',
            'date' => '2024-01-15 14:30:00',
            'user' => 'Marie Dubois',
            'details' => 'Création du document'
        ],
        [
            'action' => 'updated',
            'date' => '2024-01-15 15:20:00',
            'user' => 'Marie Dubois',
            'details' => 'Mise à jour du contenu'
        ],
        [
            'action' => 'approved',
            'date' => '2024-01-15 16:45:00',
            'user' => 'Jean Martin',
            'details' => 'Approbation du document'
        ]
    ],
    'doc-002' => [
        [
            'action' => 'created',
            'date' => '2024-01-10 09:15:00',
            'user' => 'Pierre Martin',
            'details' => 'Création du rapport financier'
        ],
        [
            'action' => 'updated',
            'date' => '2024-01-10 14:30:00',
            'user' => 'Pierre Martin',
            'details' => 'Ajout des annexes financières'
        ],
        [
            'action' => 'review',
            'date' => '2024-01-10 15:30:00',
            'user' => 'Sophie Bernard',
            'details' => 'En attente de validation par le comité financier'
        ]
    ]
];

// Documents liés
$demo_related_documents = [
    'doc-001' => [
        'doc-002' => [
            'relation' => 'reference',
            'description' => 'Rapport financier mentionné dans le PV'
        ],
        'doc-005' => [
            'relation' => 'context',
            'description' => 'Plan stratégique discuté lors de l\'AG'
        ]
    ],
    'doc-002' => [
        'doc-008' => [
            'relation' => 'suite',
            'description' => 'Budget prévisionnel basé sur ce rapport'
        ]
    ]
];

// Types de documents
$document_types = [
    'legal' => [
        'name' => 'Documents légaux',
        'icon' => 'fas fa-balance-scale',
        'color' => '#4299e1'
    ],
    'financial' => [
        'name' => 'Documents financiers',
        'icon' => 'fas fa-coins',
        'color' => '#48bb78'
    ],
    'contract' => [
        'name' => 'Contrats',
        'icon' => 'fas fa-file-signature',
        'color' => '#805ad5'
    ],
    'strategic' => [
        'name' => 'Documents stratégiques',
        'icon' => 'fas fa-chess',
        'color' => '#d69e2e'
    ],
    'meeting' => [
        'name' => 'Comptes-rendus',
        'icon' => 'fas fa-users',
        'color' => '#3182ce'
    ],
    'procedure' => [
        'name' => 'Procédures',
        'icon' => 'fas fa-tasks',
        'color' => '#e53e3e'
    ]
];

// Catégories de documents
$document_categories = [
    'assemblees' => [
        'name' => 'Assemblées',
        'icon' => 'fas fa-users',
        'description' => 'Documents relatifs aux assemblées générales'
    ],
    'rapports' => [
        'name' => 'Rapports',
        'icon' => 'fas fa-chart-line',
        'description' => 'Rapports d\'activité et analyses'
    ],
    'contrats' => [
        'name' => 'Contrats',
        'icon' => 'fas fa-file-signature',
        'description' => 'Contrats et conventions'
    ],
    'reglements' => [
        'name' => 'Règlements',
        'icon' => 'fas fa-gavel',
        'description' => 'Règlements et documents normatifs'
    ]
];

// Statuts des documents
$document_statuses = [
    'draft' => [
        'name' => 'Brouillon',
        'color' => '#718096',
        'icon' => 'fas fa-pencil-alt'
    ],
    'pending' => [
        'name' => 'En attente',
        'color' => '#ed8936',
        'icon' => 'fas fa-clock'
    ],
    'approved' => [
        'name' => 'Approuvé',
        'color' => '#48bb78',
        'icon' => 'fas fa-check'
    ],
    'active' => [
        'name' => 'Actif',
        'color' => '#4299e1',
        'icon' => 'fas fa-play'
    ],
    'archived' => [
        'name' => 'Archivé',
        'color' => '#a0aec0',
        'icon' => 'fas fa-archive'
    ]
];

?>
