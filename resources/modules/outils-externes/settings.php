<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_once 'config.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

$success_message = '';
$error_message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Mise à jour des clés API
        $openweathermap_key = trim($_POST['openweathermap_key'] ?? '');
        $exchangerate_key = trim($_POST['exchangerate_key'] ?? '');
        $googletranslate_key = trim($_POST['googletranslate_key'] ?? '');

        // Mise à jour des configurations
        $config_updates = [
            'default_view' => $_POST['default_view'] ?? 'grid',
            'items_per_page' => (int)($_POST['items_per_page'] ?? 12),
            'cache_duration' => (int)($_POST['cache_duration'] ?? 3600)
        ];

        // Mise à jour des limites d'utilisation
        foreach (['weather', 'currency', 'translate'] as $tool) {
            if (isset($_POST[$tool . '_daily_limit'])) {
                $usage_limits[$tool]['daily_limit'] = (int)$_POST[$tool . '_daily_limit'];
            }
            if (isset($_POST[$tool . '_rate_limit'])) {
                $usage_limits[$tool]['rate_limit'] = (int)$_POST[$tool . '_rate_limit'];
            }
        }

        // TODO: Sauvegarder les configurations dans un fichier ou une base de données
        
        $success_message = 'Les paramètres ont été mis à jour avec succès.';
    } catch (Exception $e) {
        $error_message = 'Une erreur est survenue lors de la mise à jour des paramètres : ' . $e->getMessage();
    }
}

$pageTitle = "Paramètres des outils externes";
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Paramètres des outils externes</h1>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" class="needs-validation" novalidate>
        <!-- Clés API -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-key"></i> Clés API
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="openweathermap_key" class="form-label">OpenWeatherMap API Key</label>
                        <input type="password" class="form-control" id="openweathermap_key" 
                               name="openweathermap_key" value="<?php echo OPENWEATHERMAP_API_KEY; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="exchangerate_key" class="form-label">ExchangeRate API Key</label>
                        <input type="password" class="form-control" id="exchangerate_key" 
                               name="exchangerate_key" value="<?php echo EXCHANGERATE_API_KEY; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="googletranslate_key" class="form-label">Google Translate API Key</label>
                        <input type="password" class="form-control" id="googletranslate_key" 
                               name="googletranslate_key" value="<?php echo GOOGLE_TRANSLATE_API_KEY; ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration générale -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cog"></i> Configuration générale
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="default_view" class="form-label">Vue par défaut</label>
                        <select class="form-select" id="default_view" name="default_view">
                            <option value="grid" <?php echo $config['default_view'] === 'grid' ? 'selected' : ''; ?>>Grille</option>
                            <option value="list" <?php echo $config['default_view'] === 'list' ? 'selected' : ''; ?>>Liste</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="items_per_page" class="form-label">Éléments par page</label>
                        <input type="number" class="form-control" id="items_per_page" 
                               name="items_per_page" value="<?php echo $config['items_per_page']; ?>" 
                               min="1" max="100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="cache_duration" class="form-label">Durée du cache (secondes)</label>
                        <input type="number" class="form-control" id="cache_duration" 
                               name="cache_duration" value="<?php echo $config['cache_duration']; ?>" 
                               min="0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Limites d'utilisation -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line"></i> Limites d'utilisation
                </h5>
            </div>
            <div class="card-body">
                <?php foreach ($usage_limits as $tool => $limits): ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6><?php echo ucfirst($tool); ?></h6>
                        </div>
                        <div class="col-md-6">
                            <label for="<?php echo $tool; ?>_daily_limit" class="form-label">Limite quotidienne</label>
                            <input type="number" class="form-control" 
                                   id="<?php echo $tool; ?>_daily_limit" 
                                   name="<?php echo $tool; ?>_daily_limit"
                                   value="<?php echo $limits['daily_limit']; ?>" 
                                   min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="<?php echo $tool; ?>_rate_limit" class="form-label">Limite par minute</label>
                            <input type="number" class="form-control" 
                                   id="<?php echo $tool; ?>_rate_limit" 
                                   name="<?php echo $tool; ?>_rate_limit"
                                   value="<?php echo $limits['rate_limit']; ?>" 
                                   min="0">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="reset" class="btn btn-outline-secondary me-2">
                <i class="fas fa-undo"></i> Réinitialiser
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script>
// Validation du formulaire
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Afficher/masquer les clés API
document.querySelectorAll('input[type="password"]').forEach(input => {
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'btn btn-outline-secondary';
    toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
    toggleBtn.style.position = 'absolute';
    toggleBtn.style.right = '10px';
    toggleBtn.style.top = '50%';
    toggleBtn.style.transform = 'translateY(-50%)';
    
    input.parentElement.style.position = 'relative';
    input.parentElement.appendChild(toggleBtn);
    
    toggleBtn.addEventListener('click', () => {
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        toggleBtn.innerHTML = `<i class="fas fa-eye${type === 'password' ? '' : '-slash'}"></i>`;
    });
});
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
