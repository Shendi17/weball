<?php
session_start();
require_once '../config.php';

$error = null;
$success = isset($_GET['success']) ? true : false;
$commandes = [];

// Filtres
$status = isset($_GET['status']) ? $_GET['status'] : '';
$fournisseur = isset($_GET['fournisseur']) ? intval($_GET['fournisseur']) : 0;
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';

try {
    // Construction de la requête
    $sql = "SELECT c.*, f.name as fournisseur_name,
                   (SELECT COUNT(*) FROM magasin_commandes_lignes WHERE commande_id = c.id) as nb_produits,
                   (SELECT SUM(quantite * prix_unitaire) 
                    FROM magasin_commandes_lignes 
                    WHERE commande_id = c.id AND prix_unitaire IS NOT NULL) as total
            FROM magasin_commandes c
            LEFT JOIN magasin_fournisseurs f ON c.fournisseur_id = f.id
            WHERE 1=1";
    
    if ($status) {
        $sql .= " AND c.status = :status";
    }
    if ($fournisseur) {
        $sql .= " AND c.fournisseur_id = :fournisseur";
    }
    if ($date_debut) {
        $sql .= " AND c.date_commande >= :date_debut";
    }
    if ($date_fin) {
        $sql .= " AND c.date_commande <= :date_fin";
    }
    
    $sql .= " ORDER BY c.date_commande DESC";
    
    $stmt = $pdo->prepare($sql);
    
    if ($status) {
        $stmt->bindParam(':status', $status);
    }
    if ($fournisseur) {
        $stmt->bindParam(':fournisseur', $fournisseur);
    }
    if ($date_debut) {
        $stmt->bindParam(':date_debut', $date_debut);
    }
    if ($date_fin) {
        $stmt->bindParam(':date_fin', $date_fin);
    }
    
    $stmt->execute();
    $commandes = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des commandes : " . $e->getMessage();
}

// Définir le titre de la page
$pageTitle = "Liste des commandes";

// Inclure le template
ob_start();
?>

<div class="commandes-container">
    <div class="module-header">
        <h1>Liste des commandes</h1>
        <div class="header-actions">
            <a href="ajouter.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle commande
            </a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Opération réalisée avec succès
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="filters-section">
        <form action="" method="GET" class="filters-form">
            <div class="form-group">
                <label for="status">Statut</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Tous</option>
                    <option value="en_attente" <?php echo $status === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                    <option value="validee" <?php echo $status === 'validee' ? 'selected' : ''; ?>>Validée</option>
                    <option value="annulee" <?php echo $status === 'annulee' ? 'selected' : ''; ?>>Annulée</option>
                </select>
            </div>

            <div class="form-group">
                <label for="date_debut">Date début</label>
                <input type="date" name="date_debut" id="date_debut" class="form-control" 
                       value="<?php echo $date_debut; ?>">
            </div>

            <div class="form-group">
                <label for="date_fin">Date fin</label>
                <input type="date" name="date_fin" id="date_fin" class="form-control" 
                       value="<?php echo $date_fin; ?>">
            </div>

            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="liste.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>

    <div class="commandes-list">
        <?php if (!empty($commandes)): ?>
            <?php foreach ($commandes as $commande): ?>
                <div class="commande-card">
                    <div class="commande-header">
                        <div class="commande-info">
                            <h3>
                                <a href="voir.php?id=<?php echo $commande['id']; ?>">
                                    Commande #<?php echo $commande['id']; ?>
                                </a>
                            </h3>
                            <span class="commande-date">
                                <?php echo date('d/m/Y', strtotime($commande['date_commande'])); ?>
                            </span>
                        </div>
                        <div class="commande-status">
                            <?php
                            $status_class = '';
                            switch ($commande['status']) {
                                case 'en_attente':
                                    $status_class = 'warning';
                                    break;
                                case 'validee':
                                    $status_class = 'success';
                                    break;
                                case 'annulee':
                                    $status_class = 'danger';
                                    break;
                            }
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $commande['status'])); ?>
                            </span>
                        </div>
                    </div>

                    <div class="commande-details">
                        <div class="detail-item">
                            <i class="fas fa-building"></i>
                            <span><?php echo htmlspecialchars($commande['fournisseur_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-box"></i>
                            <span><?php echo $commande['nb_produits']; ?> produit(s)</span>
                        </div>
                        <?php if ($commande['total']): ?>
                            <div class="detail-item">
                                <i class="fas fa-euro-sign"></i>
                                <span><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="commande-actions">
                        <a href="voir.php?id=<?php echo $commande['id']; ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <?php if ($commande['status'] === 'en_attente'): ?>
                            <a href="modifier.php?id=<?php echo $commande['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="annuler.php?id=<?php echo $commande['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <p>Aucune commande trouvée</p>
                <a href="ajouter.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer une commande
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once '../../../includes/template.php';
?>
