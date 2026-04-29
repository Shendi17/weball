# Projet WeBall - Nettoyé le 11/10/2025 à 10:03

## Fichiers conservés

### Structure principale
- `/config/` - Configuration de l'application
- `/includes/` - Fichiers d'inclusion (router, functions, db)
- `/pages/` - Pages du site (personnalite, autorite, etc.)
- `/templates/` - Templates de mise en page
- `/modules/` - Modules métier
- `/public/` - Assets publics (CSS, JS, images)
- `/install/` - Scripts d'installation et de migration

### Fichiers racine importants
- `index.php` - Point d'entrée
- `login.php` / `logout.php` - Authentification
- `.htaccess` - Configuration Apache
- `composer.json` - Dépendances PHP

## Scripts d'installation conservés

### Scripts de structure
- `init_all_modules.php` - Initialisation complète de tous les modules
- `init_complete_database.php` - Création de la base de données
- `create_types_categories_system.php` - Système de types/catégories

### Scripts de données (Personnalités)
- `add_real_personnalites.php` - Personnalités initiales
- `add_new_personality_types.php` - Types philosophe, yogi, penseur, atypique
- `add_new_personalities.php` - Personnalités pour nouveaux types
- `add_inventeurs_insolites.php` - Types inventeur et catégories insolites
- `add_inventeurs_insolites_personnalites.php` - Inventeurs et personnalités insolites
- `add_historical_mystical_types.php` - Types historique, mystique, héros, militaire
- `add_historical_mystical_personnalites.php` - Personnalités historiques
- `enrich_personnalite_structure.php` - Enrichissement de la structure
- `enrich_personnalites_data.php` - Enrichissement des données
- `clean_and_enrich_personnalites.php` - Nettoyage et enrichissement
- `populate_types_categories.php` - Population des types/catégories

### Scripts utilitaires
- `apply_templates.php` - Application des templates dynamiques

## Nettoyage effectué

### Fichiers supprimés
- Tous les fichiers de test (test_*.php, verify_*.php)
- Fichiers de diagnostic obsolètes
- Documentation temporaire (.md)
- Scripts d'installation obsolètes
- Fichiers de configuration en double

### Dossiers supprimés
- Dossiers vides (app, src, storage, logs, uploads, vendor)

## Prochaines étapes

Pour réinstaller le projet :
1. Exécuter `install/init_complete_database.php`
2. Exécuter `install/init_all_modules.php`
3. Exécuter les scripts de données dans l'ordre ci-dessus
