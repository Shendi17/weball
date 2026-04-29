<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let checkInterval = null;

function startSync(mode) {
    console.log('startSync appelé avec mode:', mode);
    
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
    
    console.log('URL:', url);
    statusDiv.textContent = 'Lancement de la synchronisation...';
    progressBar.style.width = '20%';
    progressBar.textContent = '20%';
    
    fetch(url)
        .then(response => {
            console.log('Réponse reçue:', response);
            return response.json();
        })
        .then(data => {
            console.log('Données:', data);
            
            if (data.success) {
                statusDiv.textContent = 'Synchronisation en cours...';
                progressBar.style.width = '40%';
                progressBar.textContent = '40%';
                
                // Vérifier le statut toutes les 2 secondes
                checkInterval = setInterval(() => {
                    checkSyncStatus(data.check_url, progressBar, statusDiv, progressDiv, resultDiv);
                }, 2000);
            } else {
                showError(resultDiv, progressDiv, data.message || 'Erreur inconnue');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError(resultDiv, progressDiv, 'Erreur de connexion: ' + error.message);
        });
}

function checkSyncStatus(checkUrl, progressBar, statusDiv, progressDiv, resultDiv) {
    console.log('Vérification du statut:', checkUrl);
    
    fetch(checkUrl)
        .then(response => response.json())
        .then(data => {
            console.log('Statut:', data);
            
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
                    
                    // Recharger la page après 3 secondes
                    setTimeout(() => location.reload(), 3000);
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification:', error);
        });
}

function showError(resultDiv, progressDiv, message) {
    console.log('Affichage erreur:', message);
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
    console.log('Affichage du log:', filename);
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

// Test au chargement
console.log('Script chargé. Fonctions disponibles:', {
    startSync: typeof startSync,
    checkSyncStatus: typeof checkSyncStatus,
    showError: typeof showError,
    viewLog: typeof viewLog
});
</script>
