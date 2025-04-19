<?php
$pageTitle = 'Modifier fiche Annuaire';
ob_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'utilisateur - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once dirname(__DIR__, 3) . '/includes/template.php'; ?>
    
    <div class="main-content">
        <?php include '../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>
                    <a href="index.php" class="btn btn-link">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    Modifier l'utilisateur
                </h1>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($user): ?>
                <form method="POST" class="form">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    
                    <div class="form-section">
                        <h3>Informations générales</h3>
                        
                        <div class="form-group">
                            <label for="full_name">Nom complet</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?>>
                                Compte actif
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Services et Rôles</h3>
                        <div id="services-container">
                            <?php foreach ($allServices as $service): ?>
                                <?php
                                $userService = array_filter($userServices, function($us) use ($service) {
                                    return $us['service_id'] == $service['id'];
                                });
                                $userService = reset($userService); // Get first element or false
                                ?>
                                <div class="service-card" style="border-left: 4px solid <?php echo htmlspecialchars($service['color']); ?>">
                                    <div class="service-header">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="services[]" value="<?php echo $service['id']; ?>"
                                                   <?php echo $userService ? 'checked' : ''; ?>>
                                            <?php echo htmlspecialchars($service['name']); ?>
                                        </label>
                                    </div>
                                    
                                    <div class="service-details" <?php echo $userService ? '' : 'style="display: none;"'; ?>>
                                        <div class="form-group">
                                            <label>Rôle</label>
                                            <select name="role_<?php echo $service['id']; ?>" required>
                                                <?php foreach ($allRoles as $role): ?>
                                                    <option value="<?php echo $role['id']; ?>"
                                                            <?php echo ($userService && $userService['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($role['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Téléphone fixe</label>
                                            <input type="tel" name="phone_<?php echo $service['id']; ?>"
                                                   value="<?php echo $userService ? htmlspecialchars($userService['phone']) : ''; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Mobile</label>
                                            <input type="tel" name="mobile_<?php echo $service['id']; ?>"
                                                   value="<?php echo $userService ? htmlspecialchars($userService['mobile']) : ''; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Bureau</label>
                                            <input type="text" name="office_<?php echo $service['id']; ?>"
                                                   value="<?php echo $userService ? htmlspecialchars($userService['office']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>

                <script>
                    // Afficher/masquer les détails du service quand la case est cochée/décochée
                    document.querySelectorAll('input[name="services[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const serviceDetails = this.closest('.service-card').querySelector('.service-details');
                            serviceDetails.style.display = this.checked ? 'block' : 'none';
                        });
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
