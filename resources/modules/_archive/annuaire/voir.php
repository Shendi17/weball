<?php
session_start();
require_once 'config.php';

$error = null;
$user = null;
$services = [];

if (isset($_GET['id'])) {
    try {
        // Récupération de l'utilisateur
        $query = "SELECT u.*, us.phone, us.mobile, us.office, s.name as service_name, s.color as service_color, r.name as role_name
                FROM users u
                LEFT JOIN user_services us ON u.id = us.user_id
                LEFT JOIN services s ON us.service_id = s.id
                LEFT JOIN roles r ON us.role_id = r.id
                WHERE u.id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $_GET['id']]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = "Utilisateur non trouvé.";
        }

        // Récupération de tous les services de l'utilisateur
        $query = "SELECT s.name as service_name, s.color as service_color, r.name as role_name, 
                        us.phone, us.mobile, us.office
                 FROM user_services us
                 JOIN services s ON us.service_id = s.id
                 JOIN roles r ON us.role_id = r.id
                 WHERE us.user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $_GET['id']]);
        $services = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des données : " . $e->getMessage();
    }
} else {
    $error = "ID de l'utilisateur non spécifié.";
}

$pageTitle = 'Voir fiche Annuaire';
ob_start();
?>

<div class="container mt-5">
    <div class="module-header">
        <h1>
            <a href="index.php" class="btn btn-link">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php echo $user ? htmlspecialchars($user['full_name']) : 'Utilisateur'; ?>
        </h1>
        <?php if ($user): ?>
            <div class="header-actions">
                <a href="modifier.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="supprimer.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" 
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                    <i class="fas fa-trash"></i> Supprimer
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($user): ?>
        <div class="user-details">
            <div class="user-header">
                <div class="user-avatar">
                    <i class="fas fa-user-circle fa-5x"></i>
                </div>
                <div class="user-main-info">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p class="username">@<?php echo htmlspecialchars($user['username']); ?></p>
                    <p class="status <?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $user['is_active'] ? 'Actif' : 'Inactif'; ?>
                    </p>
                </div>
            </div>

            <div class="info-section">
                <h3>Informations de contact</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span class="label">Email :</span>
                        <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3>Services et Rôles</h3>
                <?php foreach ($services as $service): ?>
                    <div class="service-card" style="border-left: 4px solid <?php echo htmlspecialchars($service['service_color']); ?>">
                        <div class="service-header">
                            <h4><?php echo htmlspecialchars($service['service_name']); ?></h4>
                            <span class="role-badge"><?php echo htmlspecialchars($service['role_name']); ?></span>
                        </div>
                        <div class="service-details">
                            <?php if ($service['phone']): ?>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($service['phone']); ?></p>
                            <?php endif; ?>
                            <?php if ($service['mobile']): ?>
                                <p><i class="fas fa-mobile-alt"></i> <?php echo htmlspecialchars($service['mobile']); ?></p>
                            <?php endif; ?>
                            <?php if ($service['office']): ?>
                                <p><i class="fas fa-building"></i> <?php echo htmlspecialchars($service['office']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
