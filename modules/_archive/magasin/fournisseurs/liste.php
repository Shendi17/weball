<?php
session_start();
require_once '../config.php';

$error = null;
$success = isset($_GET['success']) ? true : false;
$fournisseurs = [];

try {
    // Récupération des fournisseurs avec statistiques
    $sql = "SELECT f.*,
                   (SELECT COUNT(*) FROM magasin_produits WHERE fournisseur_id = f.id) as nb_produits,
                   (SELECT COUNT(*) FROM magasin_commandes WHERE fournisseur_id = f.id) as nb_commandes,
                   (SELECT status FROM magasin_commandes 
                    WHERE fournisseur_id = f.id 
                    ORDER BY date_commande DESC LIMIT 1) as derniere_commande_status,
                   (SELECT date_commande FROM magasin_commandes 
                    WHERE fournisseur_id = f.id 
                    ORDER BY date_commande DESC LIMIT 1) as derniere_commande_date
            FROM magasin_fournisseurs f
            ORDER BY f.name";
            
    $stmt = $pdo->query($sql);
    $fournisseurs = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des fournisseurs : " . $e->getMessage();
}

// Définir le titre de la page
$pageTitle = "Liste des fournisseurs";

// Inclure le template
ob_start();
?>

<div class="fournisseurs-container">
    <div class="module-header">
        <h1>Liste des fournisseurs</h1>
        <div class="header-actions">
            <a href="ajouter.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau fournisseur
            </a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Opération effectuée avec succès
        </div>
    <?php endif; ?>

    <?php if (empty($fournisseurs)): ?>
        <div class="alert alert-info">
            Aucun fournisseur enregistré
        </div>
    <?php else: ?>
        <div class="fournisseurs-grid">
            <?php foreach ($fournisseurs as $fournisseur): ?>
                <div class="fournisseur-card">
                    <div class="fournisseur-header">
                        <h3>
                            <a href="voir.php?id=<?php echo $fournisseur['id']; ?>">
                                <?php echo htmlspecialchars($fournisseur['name']); ?>
                            </a>
                        </h3>
                        <?php if ($fournisseur['contact_name']): ?>
                            <div class="contact-name">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($fournisseur['contact_name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="fournisseur-content">
                        <?php if ($fournisseur['email'] || $fournisseur['phone']): ?>
                            <div class="contact-info">
                                <?php if ($fournisseur['email']): ?>
                                    <div class="email">
                                        <i class="fas fa-envelope"></i>
                                        <a href="mailto:<?php echo htmlspecialchars($fournisseur['email']); ?>">
                                            <?php echo htmlspecialchars($fournisseur['email']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($fournisseur['phone']): ?>
                                    <div class="phone">
                                        <i class="fas fa-phone"></i>
                                        <a href="tel:<?php echo htmlspecialchars($fournisseur['phone']); ?>">
                                            <?php echo htmlspecialchars($fournisseur['phone']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="fournisseur-stats">
                            <div class="stat-item">
                                <i class="fas fa-box"></i>
                                <?php echo $fournisseur['nb_produits']; ?> produit(s)
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-shopping-cart"></i>
                                <?php echo $fournisseur['nb_commandes']; ?> commande(s)
                            </div>
                            <?php if ($fournisseur['derniere_commande_date']): ?>
                                <div class="stat-item">
                                    <i class="fas fa-clock"></i>
                                    Dernière commande : 
                                    <?php echo date('d/m/Y', strtotime($fournisseur['derniere_commande_date'])); ?>
                                    <span class="status-badge <?php echo getStatusClass($fournisseur['derniere_commande_status']); ?>">
                                        <?php echo getStatusLabel($fournisseur['derniere_commande_status']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="fournisseur-actions">
                        <a href="modifier.php?id=<?php echo $fournisseur['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="voir.php?id=<?php echo $fournisseur['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once '../../../includes/template.php';

function getStatusClass($status) {
    switch ($status) {
        case 'en_attente':
            return 'warning';
        case 'validee':
            return 'success';
        case 'annulee':
            return 'danger';
        default:
            return '';
    }
}

function getStatusLabel($status) {
    switch ($status) {
        case 'en_attente':
            return 'En attente';
        case 'validee':
            return 'Validée';
        case 'annulee':
            return 'Annulée';
        default:
            return '';
    }
}
?>
