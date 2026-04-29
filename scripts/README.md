# 🤖 Système de Synchronisation Automatique des Données Autorités

## 📋 Vue d'ensemble

Ce système permet d'enrichir automatiquement les fiches autorités avec des données réelles provenant de multiples sources (Wikipedia, Wikidata, OpenStreetMap, etc.).

## 🏗️ Architecture

```
scripts/
├── sync_autorites.php          # Script principal (orchestrateur)
├── core/                        # Composants principaux
│   ├── SyncOrchestrator.php    # Coordination des providers
│   ├── BaseProvider.php        # Classe de base pour providers
│   ├── DataValidator.php       # Validation des données
│   ├── ConflictResolver.php    # Résolution des conflits
│   └── SyncLogger.php          # Logging et rapports
├── providers/                   # Sources de données
│   ├── WikipediaProvider.php   # ✅ Gratuit - Descriptions, images
│   ├── WikidataProvider.php    # ✅ Gratuit - Données structurées
│   ├── OpenstreetmapProvider.php # ✅ Gratuit - Coordonnées GPS
│   ├── GoogleplacesProvider.php  # 💰 Payant - Horaires, téléphone
│   ├── ClearbitProvider.php      # 💰 Payant - Logos HD, infos
│   └── AiProvider.php            # 💰 Payant - Enrichissement IA
└── logs/                        # Logs de synchronisation
```

## 🚀 Utilisation

### Commandes de base

```bash
# Synchroniser toutes les autorités
php scripts/sync_autorites.php

# Synchroniser une autorité spécifique
php scripts/sync_autorites.php --id=9

# Synchroniser uniquement les fiches incomplètes
php scripts/sync_autorites.php --incomplete

# Forcer la mise à jour (même si données existantes)
php scripts/sync_autorites.php --force

# Mode test (sans sauvegarder)
php scripts/sync_autorites.php --dry-run

# Utiliser un provider spécifique
php scripts/sync_autorites.php --provider=wikipedia
```

### Exemples pratiques

```bash
# Enrichir Airbus avec toutes les sources gratuites
php scripts/sync_autorites.php --id=9

# Tester sur LVMH sans sauvegarder
php scripts/sync_autorites.php --id=7 --dry-run

# Mettre à jour toutes les fiches sans coordonnées GPS
php scripts/sync_autorites.php --incomplete
```

## 📊 Providers Disponibles

### ✅ Option A - Providers Gratuits (Activés par défaut)

#### 1. **WikipediaProvider**
- **Source**: API Wikipedia
- **Données**: Description, historique, images, coordonnées
- **Coût**: Gratuit
- **Rate limit**: 200 req/sec
- **Qualité**: ⭐⭐⭐⭐

#### 2. **WikidataProvider**
- **Source**: API Wikidata
- **Données**: SIRET, forme juridique, dirigeant, effectifs, CA, réseaux sociaux
- **Coût**: Gratuit
- **Rate limit**: Illimité
- **Qualité**: ⭐⭐⭐⭐⭐

#### 3. **OpenstreetmapProvider**
- **Source**: Nominatim (OpenStreetMap)
- **Données**: Coordonnées GPS, adresse complète, code postal
- **Coût**: Gratuit
- **Rate limit**: 1 req/sec
- **Qualité**: ⭐⭐⭐⭐

### 💰 Option B - Providers Payants (Désactivés par défaut)

#### 4. **GoogleplacesProvider**
- **Source**: Google Places API
- **Données**: Horaires, téléphone, adresse vérifiée
- **Coût**: ~$17/1000 requêtes
- **Qualité**: ⭐⭐⭐⭐⭐
- **Configuration**: Nécessite `GOOGLE_PLACES_API_KEY`

#### 5. **ClearbitProvider**
- **Source**: Clearbit Company API
- **Données**: Logo HD, effectifs, CA, réseaux sociaux
- **Coût**: ~$99/mois (plan Enrichment)
- **Qualité**: ⭐⭐⭐⭐⭐
- **Configuration**: Nécessite `CLEARBIT_API_KEY`

### 🤖 Option C - Provider IA (Désactivé par défaut)

#### 6. **AiProvider**
- **Source**: OpenAI GPT-4
- **Données**: Historique généré, missions, valeurs, résumés
- **Coût**: ~$0.03/1000 tokens (~$0.01 par autorité)
- **Qualité**: ⭐⭐⭐⭐
- **Configuration**: Nécessite `OPENAI_API_KEY`

## ⚙️ Configuration

### Activer les providers gratuits (par défaut)

Aucune configuration nécessaire ! Les providers gratuits sont activés automatiquement.

### Activer les providers payants

1. **Obtenir les clés API**:
   - Google Places: https://console.cloud.google.com/
   - Clearbit: https://clearbit.com/
   - OpenAI: https://platform.openai.com/

2. **Configurer les variables d'environnement**:

```bash
# Windows (PowerShell)
$env:GOOGLE_PLACES_API_KEY = "votre_cle_ici"
$env:CLEARBIT_API_KEY = "votre_cle_ici"
$env:OPENAI_API_KEY = "votre_cle_ici"

# Linux/Mac
export GOOGLE_PLACES_API_KEY="votre_cle_ici"
export CLEARBIT_API_KEY="votre_cle_ici"
export OPENAI_API_KEY="votre_cle_ici"
```

3. **Activer dans le script** (`sync_autorites.php`):

```php
'providers' => [
    'wikipedia' => ['enabled' => true, 'priority' => 1],
    'wikidata' => ['enabled' => true, 'priority' => 2],
    'openstreetmap' => ['enabled' => true, 'priority' => 3],
    'googleplaces' => ['enabled' => true, 'priority' => 4],  // ✅ Activé
    'clearbit' => ['enabled' => true, 'priority' => 5],      // ✅ Activé
    'ai' => ['enabled' => true, 'priority' => 10]            // ✅ Activé
]
```

## 📈 Données Récupérées par Provider

| Champ | Wikipedia | Wikidata | OSM | Google | Clearbit | IA |
|-------|-----------|----------|-----|--------|----------|-----|
| Description | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Historique | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Latitude/Longitude | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Adresse | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |
| Téléphone | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Horaires | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| SIRET | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Forme juridique | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Effectifs | ❌ | ✅ | ❌ | ❌ | ✅ | ❌ |
| Chiffre d'affaires | ❌ | ✅ | ❌ | ❌ | ✅ | ❌ |
| Logo | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ |
| Réseaux sociaux | ❌ | ✅ | ❌ | ❌ | ✅ | ❌ |
| Missions | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Valeurs | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

## 🔄 Gestion des Conflits

Lorsque plusieurs providers retournent des valeurs différentes pour un même champ, le système applique des stratégies de résolution :

- **Coordonnées GPS**: Valeur la plus précise
- **Textes (description, historique)**: Texte le plus long
- **URLs**: Préférence aux sources officielles
- **Données légales**: Préférence aux sources gouvernementales
- **Par défaut**: Première valeur (selon priorité du provider)

## 📝 Logs et Rapports

Après chaque synchronisation, 3 fichiers sont générés dans `scripts/logs/` :

1. **`.log`** - Rapport texte détaillé
2. **`.json`** - Données brutes en JSON
3. **`.html`** - Rapport visuel avec statistiques

Exemple :
```
scripts/logs/sync_2025-01-13_14-30-45_abc123.log
scripts/logs/sync_2025-01-13_14-30-45_abc123.json
scripts/logs/sync_2025-01-13_14-30-45_abc123.html
```

## 🔧 Personnalisation

### Ajouter un nouveau provider

1. Créer `scripts/providers/MonProvider.php` :

```php
<?php
require_once __DIR__ . '/../core/BaseProvider.php';

class MonProvider extends BaseProvider {
    public function getName() {
        return 'monprovider';
    }
    
    public function fetchData($autorite) {
        // Votre logique ici
        return [
            'champ1' => 'valeur1',
            'champ2' => 'valeur2'
        ];
    }
}
```

2. Activer dans `sync_autorites.php` :

```php
'providers' => [
    // ...
    'monprovider' => ['enabled' => true, 'priority' => 6]
]
```

## 🎯 Recommandations

### Pour démarrer (Gratuit)
```bash
# Utiliser uniquement les providers gratuits
php scripts/sync_autorites.php
```
**Résultat**: ~80% des données pour 0€

### Pour une qualité optimale (Budget ~50€/mois)
```bash
# Activer Google Places + providers gratuits
php scripts/sync_autorites.php
```
**Résultat**: ~90% des données

### Pour un enrichissement complet (Budget ~150€/mois)
```bash
# Activer tous les providers
php scripts/sync_autorites.php
```
**Résultat**: ~95% des données

## 🔒 Sécurité

- ✅ Validation de toutes les données avant sauvegarde
- ✅ Rate limiting respecté pour chaque API
- ✅ Gestion des erreurs et timeouts
- ✅ Logs détaillés de toutes les opérations
- ✅ Mode dry-run pour tester sans risque

## 📞 Support

Pour toute question ou problème :
1. Consulter les logs dans `scripts/logs/`
2. Vérifier la configuration des clés API
3. Tester en mode `--dry-run` d'abord

## 🖥️ Interface Web d'Administration

Une interface web complète est disponible pour gérer les synchronisations :

**URL d'accès** : `/pages/sync_admin.php`

### Fonctionnalités

- 📊 **Tableau de bord** avec statistiques en temps réel
- ▶️ **Lancement manuel** des synchronisations (complète, incomplète, individuelle)
- 📜 **Historique** des synchronisations avec visualisation des logs
- 📥 **Téléchargement** des fichiers de logs
- ⚙️ **État des providers** (actifs/inactifs)
- 📅 **Instructions** pour la planification automatique

### Captures d'écran

L'interface permet de :
1. Voir le nombre total d'autorités et leur statut de complétude
2. Lancer une synchronisation complète ou partielle en un clic
3. Synchroniser une autorité spécifique par son ID
4. Consulter l'historique des synchronisations passées
5. Visualiser et télécharger les logs détaillés

## 📅 Planification Automatique (CRON)

### Configuration sous Windows

1. **Ouvrir PowerShell en tant qu'administrateur**
2. **Naviguer vers le dossier scripts** :
   ```powershell
   cd C:\Users\Anthony\CascadeProjects\weball\scripts
   ```
3. **Exécuter le script de configuration** :
   ```powershell
   .\setup_task_scheduler.ps1
   ```

Cela créera une tâche planifiée nommée `WeballAutoSync` qui s'exécutera **tous les jours à 3h00 du matin**.

### Modifier l'horaire

Pour changer l'horaire d'exécution :
1. Ouvrir le Planificateur de tâches Windows : `taskschd.msc`
2. Trouver la tâche `WeballAutoSync`
3. Modifier le déclencheur selon vos besoins

### Configuration manuelle

Si vous préférez configurer manuellement :
1. Ouvrir le Planificateur de tâches Windows
2. Créer une nouvelle tâche
3. **Déclencheur** : Quotidien à l'heure souhaitée
4. **Action** : Exécuter `C:\Users\Anthony\CascadeProjects\weball\scripts\sync_daily.bat`

### Vérifier les logs CRON

Les logs de la tâche planifiée sont sauvegardés dans :
```
scripts/logs/cron_last_run.log
```

## 🚀 Prochaines Étapes

1. ✅ Architecture de base créée
2. ✅ Providers gratuits implémentés (Wikipedia, Wikidata, OSM)
3. ✅ Providers payants préparés (Google, Clearbit, IA)
4. ✅ Interface web d'administration créée
5. ✅ Planification automatique configurée
6. 🎯 **Prêt à l'emploi !**
