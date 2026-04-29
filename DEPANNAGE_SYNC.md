# 🔧 Dépannage - Synchronisation des Autorités

## 🚨 Problèmes Identifiés et Solutions

### 1. ❌ Connexion Admin Ne Fonctionne Pas

**Symptôme** : Impossible de se connecter avec `admin@weball.com / admin123`

**Solutions** :

#### Solution A : Vérifier la session
```bash
php test_login.php
```

#### Solution B : Se connecter manuellement
1. Aller sur : `http://weball.local/login.php`
2. Utiliser l'un de ces identifiants :
   - `admin@weball.com` / `admin123`
   - `admin` / `admin`
3. Vérifier que vous êtes redirigé

#### Solution C : Forcer la connexion (temporaire)
Créer un fichier `force_login.php` :
```php
<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';
header('Location: /pages/sync_admin.php');
?>
```

Accéder à : `http://weball.local/force_login.php`

### 2. ❌ Synchronisation Bloquée à 30%

**Symptôme** : La barre de progression reste bloquée à 30%

**Causes possibles** :
- Timeout PHP trop court
- L'API ne retourne pas de réponse
- Problème de connexion à la base de données

**Solutions** :

#### Solution A : Tester l'API directement
```bash
# Ouvrir : http://weball.local/test_api_sync.php
# Cliquer sur "Lancer le test"
```

#### Solution B : Vérifier les logs PHP
Regarder dans :
- `C:\wamp64\logs\php_error.log` (si WAMP)
- `C:\xampp\php\logs\php_error_log.txt` (si XAMPP)

#### Solution C : Tester en ligne de commande
```bash
php scripts/sync_autorites.php --id=9 --force
```

Si cela fonctionne, le problème vient de l'API web.

#### Solution D : Augmenter les timeouts
Modifier `php.ini` :
```ini
max_execution_time = 600
max_input_time = 600
```

Redémarrer Apache/Nginx.

### 3. ⚠️ Erreurs JavaScript dans la Console

**Symptôme** : Erreurs visibles dans la console du navigateur

**Solution** : Vérifier que l'URL de l'API est correcte

Dans `templates/pages/sync_admin_template.php`, ligne ~380 :
```javascript
fetch('/api/sync_autorites.php?mode=' + mode + '&id=' + id)
```

Si vous utilisez un sous-dossier, modifier en :
```javascript
fetch('http://weball.local/api/sync_autorites.php?mode=' + mode + '&id=' + id)
```

## 🧪 Tests à Effectuer

### Test 1 : Vérifier la Connexion
```bash
php test_login.php
```

**Résultat attendu** :
```
✅ Connecté
- user_id: 1
- username: admin
- user_role: admin
```

### Test 2 : Tester l'API
Ouvrir : `http://weball.local/test_api_sync.php`

**Résultat attendu** : JSON avec les statistiques de synchronisation

### Test 3 : Tester en CLI
```bash
php scripts/sync_autorites.php --id=9 --force
```

**Résultat attendu** :
```
✅ Succès - X champs mis à jour
```

## 📋 Checklist de Vérification

- [ ] PHP CLI fonctionne : `php -v`
- [ ] Base de données accessible
- [ ] Session démarrée correctement
- [ ] Utilisateur connecté en tant qu'admin
- [ ] Timeout PHP suffisant (600 secondes)
- [ ] Logs accessibles en écriture
- [ ] API accessible : `http://weball.local/api/sync_autorites.php`

## 🔍 Débogage Avancé

### Activer le mode debug dans l'API

Modifier `api/sync_autorites.php`, ajouter au début :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Voir la sortie brute du script

Modifier `api/sync_autorites.php`, ligne ~115 :
```php
echo json_encode([
    'success' => true,
    'message' => 'Synchronisation terminée',
    'stats' => $stats,
    'log_file' => basename($outputFile),
    'output' => $output,  // ← Ajouter ceci
    'errors' => $errors   // ← Et ceci
]);
```

### Tester avec cURL

```bash
curl -X GET "http://weball.local/api/sync_autorites.php?mode=single&id=9" \
     -H "Cookie: PHPSESSID=votre_session_id"
```

## 🆘 Solutions Rapides

### Problème : "Accès non autorisé"
**Solution** : Vous n'êtes pas connecté ou pas admin
```bash
php force_login.php
```

### Problème : "Aucune donnée à mettre à jour"
**Solution** : Les données existent déjà, utiliser `--force`
```bash
php scripts/sync_autorites.php --id=9 --force
```

### Problème : "MySQL server has gone away"
**Solution** : Déjà corrigé avec reconnexion automatique
Vérifier que MySQL est démarré.

### Problème : Timeout après 30 secondes
**Solution** : Augmenter `max_execution_time` dans `php.ini`

## 📞 Support

Si les problèmes persistent :

1. **Vérifier les logs** :
   - `scripts/logs/api_sync_*.log`
   - Logs PHP du serveur web

2. **Tester en CLI** :
   ```bash
   php scripts/sync_autorites.php --id=9 --force
   ```

3. **Vérifier la configuration** :
   - Session active
   - Permissions admin
   - Timeout PHP

4. **Consulter la documentation** :
   - `SYNC_GUIDE.md`
   - `INSTALLATION_SYNC.md`

---

**🎯 Actions Immédiates**

1. Exécuter : `php test_login.php`
2. Se connecter : `http://weball.local/login.php`
3. Tester l'API : `http://weball.local/test_api_sync.php`
4. Accéder à la page : `http://weball.local/pages/sync_admin.php`
