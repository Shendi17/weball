# 🚀 Guide de Migration Complète WebAll

## 📋 Vue d'ensemble

Ce guide vous permet de migrer **toutes vos données** (personnalités, autorités, types, catégories) vers un nouvel appareil en conservant exactement le même contenu que votre site actuel.

## 📦 Fichiers de Migration Générés

Les fichiers suivants ont été créés dans `scripts/exports/` :

- ✅ **structure.sql** - Structure complète de toutes les tables
- ✅ **personalities_data.sql** - Personnalités + types + catégories  
- ✅ **authorities_data.sql** - Autorités + types + catégories
- ✅ **types_categories.sql** - TOUS les types et catégories de tous les modules
- ✅ **users_data.sql** - Comptes utilisateurs
- ✅ **import_complete.php** - Script d'importation automatique
- ✅ **migration_report.json** - Rapport détaillé de la migration

## 🔧 Procédure d'Installation sur Nouvel Appareil

### Étape 1: Installation de Base

1. **Copier tout le projet WebAll** sur le nouvel appareil
2. **Configurer la base de données** MySQL/MariaDB
3. **Créer la base de données** `weballone` (ou le nom que vous préférez)
4. **Mettre à jour la configuration** dans `config/database.php` si nécessaire

### Étape 2: Importation des Données

1. **Copier le dossier `scripts/exports/`** sur le nouvel appareil
2. **Exécuter le script d'importation** :

```bash
cd C:\chemin\votre\weball\scripts\exports
php import_complete.php
```

### Étape 3: Vérification

Après l'importation, vérifiez que vous avez bien :

- ✅ **Toutes vos personnalités** avec leurs photos et descriptions
- ✅ **Toutes vos autorités** avec leurs informations complètes  
- ✅ **TOUS les types et catégories** pour chaque module
- ✅ **Vos comptes utilisateurs** (admin, etc.)

## 📊 Contenu Exact Migré

### Personnalités
- **Types** : Artiste, Influenceur, Conférencier, Entrepreneur, Célébrité, Philosophe, Yogi, Personnalité Atypique, Penseur
- **Catégories** : Toutes les catégories associées à chaque type
- **Données** : Photos, biographies, informations complètes

### Autorités  
- **Types** : Gouvernementale, Judiciaire, Administrative, Régionale
- **Catégories** : Ministère, Agence, Commission, Tribunal, Cour, etc.
- **Données** : Descriptions, sites web, coordonnées, etc.

### Modules (32 au total)
- **Chaque module** avec ses types et catégories spécifiques
- **Données de démonstration** pour chaque module
- **Structure complète** des tables

## 🔄 Si Vous Ajoutez des Données Après la Migration

Si vous ajoutez de nouvelles personnalités ou autorités après la migration, vous pouvez :

1. **Relancer le script de migration** pour mettre à jour les fichiers
2. **Utiliser les scripts individuels** dans le dossier `install/` :
   - `add_new_personalities.php` - Ajouter des personnalités
   - `add_real_autorites.php` - Ajouter des autorités
   - `populate_types_categories.php` - Mettre à jour types/catégories

## 🚨 Points Importants

### ✅ Ce qui est migré
- **Toutes les données existantes** dans la base de données
- **Structure complète** des tables
- **Types et catégories** pour tous les modules
- **Comptes utilisateurs** et permissions

### ❌ Ce qui n'est PAS migré (à copier manuellement)
- **Fichiers uploadés** dans `uploads/` (photos, documents)
- **Logs** dans `logs/`
- **Cache** dans les dossiers de cache
- **Fichiers de configuration** personnalisés

## 📁 Copie Manuelle Recommandée

En plus de la migration SQL, copiez manuellement ces dossiers :

```
weball/
├── uploads/           # Photos des personnalités/autorités
├── logs/              # Logs d'activité  
├── .env               # Variables d'environnement
└── tout autre fichier personnalisé
```

## 🎯 Vérification Finale

Après migration, testez :

1. **Connexion** avec votre compte admin
2. **Affichage des personnalités** - vérifiez photos et descriptions
3. **Affichage des autorités** - vérifiez informations complètes
4. **Types et catégories** - doivent être identiques à l'original
5. **Fonctionnalités** - testez création/édition

## 🆘 En Cas de Problème

Si quelque chose ne fonctionne pas :

1. **Vérifiez les logs** PHP et MySQL
2. **Comparez les tables** avec l'original
3. **Relancez l'importation** (le script utilise INSERT IGNORE)
4. **Contactez-moi** avec les messages d'erreur précis

---

## 📞 Support

Pour toute question sur la migration :
1. Vérifiez le fichier `migration_report.json` pour voir ce qui a été exporté
2. Consultez les logs PHP/MySQL pour les erreurs
3. Le script d'importation est conçu pour être **sans risque** (INSERT IGNORE)

🎉 **Votre site sera identique à l'original avec toutes vos données !**
