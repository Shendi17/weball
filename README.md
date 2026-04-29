# WeBall - Plateforme Multi-Modules

> Projet nettoyé et optimisé le 11/10/2025

## 📋 Description

WeBall est une plateforme web modulaire complète permettant de gérer différents types de contenus : personnalités, autorités, annuaires, entités, et bien plus.

## 🚀 Fonctionnalités Principales

### Module Personnalité (★ COMPLET)
- **53 personnalités réelles** avec biographies complètes
- **33 types** : Artiste, Musicien, Philosophe, Yogi, Inventeur, Historique, Mystique, Héros, Militaire, etc.
- **370+ catégories** couvrant tous les domaines
- **Système de navigation dynamique** : Types (navbar) + Catégories (sidebar)
- **2 modes d'affichage** : Liste et Grille
- **Fiches enrichies** : dates, lieux, œuvres, citations, anecdotes, sources, liens externes

### Autres Modules (28 modules)
- Cadran, Journal, Écran
- Autorité, Annuaire, Entité, Localité
- Instrument, Office, Plateforme, Banque
- Réseau, Publication, Archive, École
- Media, Formation, Annonce, Catalogue
- Marché, Boutique, Adhésion, Projet
- Carrière, Discipline, Concours, Campagne

## 📁 Structure du Projet

```
weball/
├── config/                 # Configuration (database.php, config.php)
├── includes/               # Fichiers d'inclusion
│   ├── router.php         # Routeur principal
│   ├── functions.php      # Fonctions utilitaires
│   └── db.php             # Connexion base de données
├── pages/                  # Pages principales (✓ ACTIF)
│   ├── personnalite.php
│   ├── personnalite_detail.php
│   └── [autres modules].php
├── templates/              # Templates de mise en page
│   ├── layout/            # Layouts (main.php, header.php, footer.php)
│   └── pages/             # Templates de pages
│       ├── dynamic_module.php      # Template dynamique avec types/catégories
│       └── personnalite_detail.php # Template de détail
├── modules/                # Modules métier
│   ├── personnalite/
│   │   └── PersonnaliteService.php
│   └── [autres modules]/
├── public/                 # Assets publics
│   ├── css/
│   ├── js/
│   └── images/
├── resources/modules/      # Ressources des modules
├── install/                # Scripts d'installation
│   ├── init_complete_database.php
│   ├── init_all_modules.php
│   └── [scripts de données]
├── logs/                   # Logs (vide)
├── uploads/                # Uploads (vide)
├── index.php               # Point d'entrée
├── login.php               # Authentification
├── logout.php
└── .htaccess               # Configuration Apache
```

## 🛠️ Installation

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Apache avec mod_rewrite
- Serveur local (waohost configuré)

### Étapes d'installation

1. **Créer la base de données**
```bash
php install/init_complete_database.php
```

2. **Initialiser tous les modules**
```bash
php install/init_all_modules.php
```

3. **Peupler les données (Personnalités)**

Exécuter dans l'ordre :
```bash
# Système de types/catégories
php install/create_types_categories_system.php
php install/populate_types_categories.php

# Personnalités initiales
php install/add_real_personnalites.php

# Types supplémentaires
php install/add_new_personality_types.php
php install/add_new_personalities.php

# Inventeurs et insolites
php install/add_inventeurs_insolites.php
php install/add_inventeurs_insolites_personnalites.php

# Historiques et mystiques
php install/add_historical_mystical_types.php
php install/add_historical_mystical_personnalites.php

# Enrichissement
php install/enrich_personnalite_structure.php
php install/enrich_personnalites_data.php
php install/clean_and_enrich_personnalites.php
```

4. **Accéder au site**
```
http://weball.local
```

## 🎨 Fonctionnalités Techniques

### Système de Navigation Dynamique
- **Navbar secondaire** : Onglets horizontaux pour les types
- **Sidebar secondaire** : Liste verticale pour les catégories
- **Filtrage en temps réel** : JavaScript pour filtrer sans rechargement
- **Recherche** : Barre de recherche instantanée
- **Tri** : Par date, nom, etc.

### Templates Réutilisables
- `dynamic_module.php` : Template universel pour tous les modules avec types/catégories
- `personnalite_detail.php` : Template de détail enrichi
- Système de layout avec header/footer/sidebar

### Base de Données
- **Tables principales** : `personnalite`, `autorite`, `annuaire`, etc.
- **Tables de types** : `personnalite_types`, `autorite_types`, etc.
- **Tables de catégories** : `personnalite_categories`, etc.
- **Colonnes enrichies** : dates, lieux, œuvres, citations, sources, liens

## 📊 Statistiques

### Module Personnalité
- **53 fiches** de personnalités réelles
- **33 types** différents
- **370+ catégories**
- **100% enrichi** (dates, œuvres, citations, anecdotes, sources)

### Projet Global
- **28 modules** actifs
- **Architecture modulaire** et extensible
- **Code propre** : 65+ fichiers obsolètes supprimés
- **82 Mo de logs** nettoyés

## 🔐 Authentification

- Login : `login.php`
- Logout : `logout.php`
- Gestion des sessions
- Rôles : Admin, User

## 📝 Configuration

### Base de données
Fichier : `config/database.php`
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'weballone');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Hôte virtuel
Domaine : `weball.local` (configuré dans waohost)

## 🚧 Développement

### Ajouter un nouveau module

1. Créer la table dans la base de données
2. Créer le fichier `pages/[module].php`
3. Créer le service `modules/[module]/[Module]Service.php`
4. Ajouter la route dans `includes/router.php`
5. (Optionnel) Créer les tables `_types` et `_categories`

### Utiliser le template dynamique

```php
// Dans pages/[module].php
$moduleName = 'mon_module';
$types = $pdo->query("SELECT * FROM mon_module_types")->fetchAll();
$categories = $pdo->query("SELECT * FROM mon_module_categories")->fetchAll();
$items = $pdo->query("SELECT * FROM mon_module")->fetchAll();

ob_start();
include __DIR__ . '/../templates/pages/dynamic_module.php';
$content = ob_get_clean();
include __DIR__ . '/../templates/layout/main.php';
```

## 📚 Documentation

- `PROJET_NETTOYE.md` : Détails du nettoyage effectué
- `CLEANUP_README.md` : Scripts d'installation conservés
- `MODULES_DOCUMENTATION.md` : Documentation des modules

## 🎯 Roadmap

- [ ] Appliquer le système types/catégories aux 27 autres modules
- [ ] Ajouter l'édition en ligne des fiches
- [ ] Système d'upload d'images
- [ ] API REST
- [ ] Export PDF des fiches
- [ ] Recherche avancée multi-critères

## 👥 Auteur

Projet WeBall - 2025

## 📄 Licence

Propriétaire
