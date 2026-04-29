<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Weball</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.primary { border-left-color: #0d6efd; }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.warning { border-left-color: #ffc107; }
        .log-item {
            transition: background-color 0.2s;
        }
        .log-item:hover {
            background-color: #f8f9fa;
        }
        .provider-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .sync-running {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="list-group">
                    <a href="/pages/autorite.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-building me-2"></i>Autorités
                    </a>
                    <a href="/pages/sync_admin.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-sync me-2"></i>Synchronisation
                    </a>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="col-md-9 col-lg-10">
                <!-- En-tête -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="fas fa-sync-alt text-primary me-2"></i>
                            <?= htmlspecialchars($pageTitle) ?>
                        </h1>
                        <p class="text-muted mb-0"><?= htmlspecialchars($pageDescription) ?></p>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Autorités</h6>
                                        <h2 class="mb-0"><?= $stats['total_autorites'] ?></h2>
                                    </div>
                                    <div class="text-primary">
                                        <i class="fas fa-building fa-3x opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Fiches Complètes</h6>
                                        <h2 class="mb-0"><?= $stats['autorites_completes'] ?></h2>
                                        <small class="text-muted">
                                            <?= round(($stats['autorites_completes'] / max($stats['total_autorites'], 1)) * 100, 1) ?>%
                                        </small>
                                    </div>
                                    <div class="text-success">
                                        <i class="fas fa-check-circle fa-3x opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Fiches Incomplètes</h6>
                                        <h2 class="mb-0"><?= $stats['autorites_incompletes'] ?></h2>
                                        <small class="text-muted">À enrichir</small>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-exclamation-triangle fa-3x opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions de synchronisation -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-play-circle me-2"></i>
                            Lancer une Synchronisation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-globe text-primary me-2"></i>
                                            Synchronisation Complète
                                        </h6>
                                        <p class="card-text text-muted small">
                                            Synchroniser toutes les autorités avec les providers gratuits (Wikipedia, Wikidata, OpenStreetMap)
                                        </p>
                                        <button class="btn btn-primary" onclick="startSync('all')">
                                            <i class="fas fa-sync me-2"></i>Tout Synchroniser
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                            Fiches Incomplètes
                                        </h6>
                                        <p class="card-text text-muted small">
                                            Synchroniser uniquement les autorités avec des données manquantes
                                        </p>
                                        <button class="btn btn-warning" onclick="startSync('incomplete')">
                                            <i class="fas fa-sync me-2"></i>Synchroniser Incomplètes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Synchronisation Individuelle</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="autoriteId" placeholder="ID de l'autorité">
                                    <button class="btn btn-outline-primary" onclick="startSync('single')">
                                        <i class="fas fa-sync me-2"></i>Synchroniser
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Zone de progression -->
                        <div id="syncProgress" class="mt-4" style="display: none;">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm me-3 sync-running" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                    </div>
                                    <div>
                                        <strong>Synchronisation en cours...</strong>
                                        <div id="syncStatus" class="small mt-1">Initialisation...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div id="syncProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%">0%</div>
                            </div>
                        </div>

                        <!-- Résultat -->
                        <div id="syncResult" class="mt-4" style="display: none;"></div>
                    </div>
                </div>

                <!-- Configuration des Providers -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plug me-2"></i>
                            Providers Configurés
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">✓ Actif</span>
                                    <strong>Wikipedia</strong>
                                    <span class="badge bg-info ms-auto">Gratuit</span>
                                </div>
                                <small class="text-muted d-block mt-1">Descriptions, historique, images</small>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">✓ Actif</span>
                                    <strong>Wikidata</strong>
                                    <span class="badge bg-info ms-auto">Gratuit</span>
                                </div>
                                <small class="text-muted d-block mt-1">Données structurées, SIRET, effectifs</small>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">✓ Actif</span>
                                    <strong>OpenStreetMap</strong>
                                    <span class="badge bg-info ms-auto">Gratuit</span>
                                </div>
                                <small class="text-muted d-block mt-1">Coordonnées GPS, adresses</small>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">○ Inactif</span>
                                    <strong>Google Places</strong>
                                    <span class="badge bg-warning text-dark ms-auto">Payant</span>
                                </div>
                                <small class="text-muted d-block mt-1">Horaires, téléphone, avis</small>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">○ Inactif</span>
                                    <strong>Clearbit</strong>
                                    <span class="badge bg-warning text-dark ms-auto">Payant</span>
                                </div>
                                <small class="text-muted d-block mt-1">Logos HD, infos entreprises</small>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">○ Inactif</span>
                                    <strong>AI Provider</strong>
                                    <span class="badge bg-warning text-dark ms-auto">Payant</span>
                                </div>
                                <small class="text-muted d-block mt-1">Enrichissement par IA</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des synchronisations -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Historique des Synchronisations
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logData)): ?>
                            <p class="text-muted text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Aucune synchronisation effectuée pour le moment
                            </p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Taille</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logData as $log): ?>
                                            <tr class="log-item">
                                                <td>
                                                    <i class="fas fa-file-alt text-muted me-2"></i>
                                                    <?= htmlspecialchars($log['date']) ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <?= number_format($log['size'] / 1024, 2) ?> KB
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewLog('<?= htmlspecialchars($log['file']) ?>')">
                                                        <i class="fas fa-eye me-1"></i>Voir
                                                    </button>
                                                    <a href="/api/download_log.php?file=<?= urlencode($log['file']) ?>" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-download me-1"></i>Télécharger
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Planification automatique -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Planification Automatique
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Configuration Windows Task Scheduler
                            </h6>
                            <p class="mb-2">Pour activer la synchronisation automatique quotidienne :</p>
                            <ol class="mb-3">
                                <li>Ouvrez PowerShell en tant qu'administrateur</li>
                                <li>Naviguez vers le dossier : <code>cd scripts</code></li>
                                <li>Exécutez : <code>.\setup_task_scheduler.ps1</code></li>
                            </ol>
                            <p class="mb-0">
                                <strong>Horaire par défaut :</strong> Tous les jours à 3h00 du matin
                            </p>
                        </div>
                        <a href="#" class="btn btn-info" onclick="alert('Ouvrez PowerShell en tant qu\'administrateur et exécutez le script setup_task_scheduler.ps1'); return false;">
                            <i class="fas fa-calendar-check me-2"></i>
                            Instructions Détaillées
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher les logs -->
    <div class="modal fade" id="logModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>
                        Détails du Log
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="logContent" class="bg-dark text-light p-3 rounded" style="max-height: 600px; overflow-y: auto;"></pre>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let checkInterval = null;
        
        function startSync(mode) {
            const progressDiv = document.getElementById('syncProgress');
            const resultDiv = document.getElementById('syncResult');
            const progressBar = document.getElementById('syncProgressBar');
            const statusDiv = document.getElementById('syncStatus');
            
            // Réinitialiser
            resultDiv.style.display = 'none';
            resultDiv.innerHTML = '';
            progressDiv.style.display = 'block';
            progressBar.style.width = '10%';
            progressBar.textContent = '10%';
            
            let url = '/api/sync_autorites_async.php?mode=' + mode;
            
            if (mode === 'single') {
                const autoriteId = document.getElementById('autoriteId').value;
                if (!autoriteId) {
                    alert('Veuillez entrer un ID d\'autorité');
                    progressDiv.style.display = 'none';
                    return;
                }
                url += '&id=' + autoriteId;
            }
            
            statusDiv.textContent = 'Lancement de la synchronisation...';
            progressBar.style.width = '20%';
            progressBar.textContent = '20%';
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusDiv.textContent = 'Synchronisation en cours...';
                        progressBar.style.width = '40%';
                        progressBar.textContent = '40%';
                        
                        // Vérifier le statut toutes les 2 secondes
                        checkInterval = setInterval(() => {
                            checkSyncStatus(data.check_url, progressBar, statusDiv, progressDiv, resultDiv);
                        }, 2000);
                    } else {
                        showError(resultDiv, progressDiv, data.message);
                    }
                })
                .catch(error => {
                    showError(resultDiv, progressDiv, 'Erreur de connexion: ' + error.message);
                });
        }
        
        function checkSyncStatus(checkUrl, progressBar, statusDiv, progressDiv, resultDiv) {
            fetch(checkUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.has_results) {
                        // Augmenter la progression
                        let currentProgress = parseInt(progressBar.style.width);
                        if (currentProgress < 90) {
                            currentProgress += 10;
                            progressBar.style.width = currentProgress + '%';
                            progressBar.textContent = currentProgress + '%';
                        }
                        
                        statusDiv.textContent = 'Traitement en cours... (' + data.stats.total + ' autorités)';
                    }
                    
                    if (data.completed) {
                        clearInterval(checkInterval);
                        progressBar.style.width = '100%';
                        progressBar.textContent = '100%';
                        statusDiv.textContent = 'Synchronisation terminée !';
                        
                        setTimeout(() => {
                            progressDiv.style.display = 'none';
                            resultDiv.style.display = 'block';
                            
                            resultDiv.innerHTML = `
                                <div class="alert alert-success">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Synchronisation Réussie !
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Total traité :</strong> ${data.stats.total}
                                        </div>
                                        <div class="col-md-3">
                                            <strong class="text-success">Succès :</strong> ${data.stats.success}
                                        </div>
                                        <div class="col-md-3">
                                            <strong class="text-warning">Partiel :</strong> ${data.stats.partial}
                                        </div>
                                        <div class="col-md-3">
                                            <strong class="text-danger">Échec :</strong> ${data.stats.failed}
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <strong>Champs mis à jour :</strong> ${data.stats.fields_updated}
                                    </div>
                                </div>
                            `;
                            
                            // Recharger la page après 3 secondes pour mettre à jour les stats
                            setTimeout(() => location.reload(), 3000);
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du statut:', error);
                });
        }
        
        function showError(resultDiv, progressDiv, message) {
            progressDiv.style.display = 'none';
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h5 class="alert-heading">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Erreur
                    </h5>
                    <p class="mb-0">${message}</p>
                </div>
            `;
        }
        
        function viewLog(filename) {
            fetch('/api/view_log.php?file=' + encodeURIComponent(filename))
                .then(response => response.text())
                .then(data => {
                    document.getElementById('logContent').textContent = data;
                    new bootstrap.Modal(document.getElementById('logModal')).show();
                })
                .catch(error => {
                    alert('Erreur lors du chargement du log : ' + error.message);
                });
        }
        
        // Test au chargement de la page
        console.log('✅ Script de synchronisation chargé');
        console.log('Fonctions disponibles:', {
            startSync: typeof startSync,
            checkSyncStatus: typeof checkSyncStatus,
            showError: typeof showError,
            viewLog: typeof viewLog
        });
        
        // Vérifier que les éléments existent
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé');
            console.log('syncProgress:', document.getElementById('syncProgress'));
            console.log('syncResult:', document.getElementById('syncResult'));
        });
    </script>
</body>
</html>
