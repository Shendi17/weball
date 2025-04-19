<?php
session_start();
require_once '../config.php';

$error = null;
$success = isset($_GET['success']) ? true : false;
$fournisseur = null;
$produits = [];
$commandes = [];

// Récupération de l'ID du fournisseur
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: liste.php");
    exit;
}

try {
    // Récupération du fournisseur
    $stmt = $pdo->prepare("SELECT * FROM magasin_fournisseurs WHERE id = ?");
    $stmt->execute([$id]);
    $fournisseur = $stmt->fetch();
    
    if (!$fournisseur) {
        header("Location: liste.php");
        exit;
    }
    
    // Récupération des produits du fournisseur
    $stmt = $pdo->prepare("SELECT * FROM magasin_produits WHERE fournisseur_id = ? ORDER BY name");
    $stmt->execute([$id]);
    $produits = $stmt->fetchAll();
    
    // Récupération des dernières commandes
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM magasin_commandes_lignes WHERE commande_id = c.id) as nb_produits,
               (SELECT SUM(quantite * prix_unitaire) 
                FROM magasin_commandes_lignes 
                WHERE commande_id = c.id) as total
        FROM magasin_commandes c 
        WHERE c.fournisseur_id = ? 
        ORDER BY c.date_commande DESC 
        LIMIT 5
    ");
    $stmt->execute([$id]);
    $commandes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    try {
        // Vérification des dépendances
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM magasin_produits WHERE fournisseur_id = ?");
        $stmt->execute([$id]);
        $nbProduits = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM magasin_commandes WHERE fournisseur_id = ?");
        $stmt->execute([$id]);
        $nbCommandes = $stmt->fetchColumn();
        
        if ($nbProduits > 0 || $nbCommandes > 0) {
            $error = "Impossible de supprimer ce fournisseur car il a des produits ou des commandes associés";
        } else {
            $stmt = $pdo->prepare("DELETE FROM magasin_fournisseurs WHERE id = ?");
            $stmt->execute([$id]);
            
            header("Location: liste.php?success=1");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Définir le titre de la page
$pageTitle = $fournisseur['name'];

// Inclure le template
ob_start();
?>

<div class="fournisseur-details-container">
    <div class="module-header">
        <h1><?php echo htmlspecialchars($fournisseur['name']); ?></h1>
        <div class="header-actions">
            <a href="../commandes/ajouter.php?fournisseur=<?php echo $fournisseur['id']; ?>" 
               class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle commande
            </a>
            <a href="modifier.php?id=<?php echo $fournisseur['id']; ?>" 
               class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                <i class="fas fa-trash"></i> Supprimer
            </button>
            <a href="liste.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
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

    <div class="fournisseur-details">
        <div class="fournisseur-grid">
            <!-- Informations générales -->
            <div class="fournisseur-section">
                <h2><i class="fas fa-info-circle"></i> Informations de contact</h2>
                <div class="info-grid">
                    <?php if ($fournisseur['contact_name']): ?>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-user"></i> Contact</span>
                            <span class="value"><?php echo htmlspecialchars($fournisseur['contact_name']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($fournisseur['email']): ?>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-envelope"></i> Email</span>
                            <span class="value">
                                <a href="mailto:<?php echo htmlspecialchars($fournisseur['email']); ?>">
                                    <?php echo htmlspecialchars($fournisseur['email']); ?>
                                </a>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($fournisseur['phone']): ?>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-phone"></i> Téléphone</span>
                            <span class="value">
                                <a href="tel:<?php echo htmlspecialchars($fournisseur['phone']); ?>">
                                    <?php echo htmlspecialchars($fournisseur['phone']); ?>
                                </a>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($fournisseur['address']): ?>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-map-marker-alt"></i> Adresse</span>
                            <span class="value"><?php echo nl2br(htmlspecialchars($fournisseur['address'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Produits -->
            <?php if (!empty($produits)): ?>
                <div class="fournisseur-section">
                    <h2><i class="fas fa-box"></i> Produits (<?php echo count($produits); ?>)</h2>
                    <div class="produits-grid">
                        <?php foreach ($produits as $produit): ?>
                            <div class="produit-card">
                                <h3><?php echo htmlspecialchars($produit['name']); ?></h3>
                                <div class="produit-details">
                                    <span class="reference">
                                        <?php echo htmlspecialchars($produit['reference']); ?>
                                    </span>
                                    <?php if ($produit['prix_unitaire']): ?>
                                        <span class="prix">
                                            <?php echo number_format($produit['prix_unitaire'], 2, ',', ' '); ?> €
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Commandes récentes -->
            <?php if (!empty($commandes)): ?>
                <div class="fournisseur-section">
                    <h2><i class="fas fa-shopping-cart"></i> Dernières commandes</h2>
                    <div class="commandes-list">
                        <?php foreach ($commandes as $commande): ?>
                            <div class="commande-card">
                                <div class="commande-header">
                                    <a href="../commandes/voir.php?id=<?php echo $commande['id']; ?>" class="commande-link">
                                        Commande #<?php echo $commande['id']; ?>
                                    </a>
                                    <span class="commande-date">
                                        <?php echo date('d/m/Y', strtotime($commande['date_commande'])); ?>
                                    </span>
                                </div>
                                <div class="commande-details">
                                    <span class="status-badge <?php echo getStatusClass($commande['status']); ?>">
                                        <?php echo getStatusLabel($commande['status']); ?>
                                    </span>
                                    <span class="produits-count">
                                        <?php echo $commande['nb_produits']; ?> produit(s)
                                    </span>
                                    <?php if ($commande['total']): ?>
                                        <span class="total">
                                            <?php echo number_format($commande['total'], 2, ',', ' '); ?> €
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce fournisseur ?</p>
                <p class="text-danger">
                    <strong>Attention :</strong> Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="delete" value="confirm">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once '../../../includes/template.php';
?>
