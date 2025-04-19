<?php
session_start();
require_once '../config.php';

$error = null;
$success = isset($_GET['success']) ? true : false;
$commande = null;
$lignes = [];

// Récupération de l'ID de la commande
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: ../index.php");
    exit;
}

try {
    // Récupération de la commande
    $sql = "SELECT c.*, f.name as fournisseur_name, f.contact_name as fournisseur_contact
            FROM magasin_commandes c
            LEFT JOIN magasin_fournisseurs f ON c.fournisseur_id = f.id
            WHERE c.id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $commande = $stmt->fetch();
    
    if (!$commande) {
        header("Location: ../index.php");
        exit;
    }
    
    // Récupération des lignes de commande
    $sql = "SELECT l.*, p.name as produit_name, p.reference as produit_reference,
                   p.unite, c.name as category_name, c.color as category_color
            FROM magasin_commandes_lignes l
            JOIN magasin_produits p ON l.produit_id = p.id
            LEFT JOIN magasin_categories c ON p.category_id = c.id
            WHERE l.commande_id = ?
            ORDER BY c.name, p.name";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $lignes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            $pdo->beginTransaction();
            
            switch ($_POST['action']) {
                case 'valider':
                    $stmt = $pdo->prepare("UPDATE magasin_commandes SET status = 'validee' WHERE id = ?");
                    $stmt->execute([$id]);
                    break;
                    
                case 'recevoir':
                    // Mise à jour du statut de la commande
                    $stmt = $pdo->prepare("
                        UPDATE magasin_commandes 
                        SET status = 'recue', date_reception = CURRENT_TIMESTAMP 
                        WHERE id = ?
                    ");
                    $stmt->execute([$id]);
                    
                    // Pour chaque ligne, on met à jour le stock et on crée un mouvement
                    foreach ($lignes as $ligne) {
                        // Mise à jour du stock
                        $stmt = $pdo->prepare("
                            UPDATE magasin_produits 
                            SET quantite = quantite + ?,
                                status = CASE 
                                    WHEN quantite + ? > 0 THEN 'actif'
                                    ELSE 'rupture'
                                END
                            WHERE id = ?
                        ");
                        $stmt->execute([
                            $ligne['quantite'],
                            $ligne['quantite'],
                            $ligne['produit_id']
                        ]);
                        
                        // Création du mouvement de stock
                        $stmt = $pdo->prepare("
                            INSERT INTO magasin_mouvements (
                                produit_id, type, quantite, prix_unitaire, motif
                            ) VALUES (?, 'entree', ?, ?, ?)
                        ");
                        $stmt->execute([
                            $ligne['produit_id'],
                            $ligne['quantite'],
                            $ligne['prix_unitaire'],
                            "Réception commande " . $commande['reference']
                        ]);
                    }
                    break;
                    
                case 'annuler':
                    $stmt = $pdo->prepare("UPDATE magasin_commandes SET status = 'annulee' WHERE id = ?");
                    $stmt->execute([$id]);
                    break;
            }
            
            $pdo->commit();
            header("Location: voir.php?id=$id&success=1");
            exit;
            
        } elseif (isset($_POST['delete']) && $_POST['delete'] === 'confirm') {
            // Suppression de la commande
            $stmt = $pdo->prepare("DELETE FROM magasin_commandes WHERE id = ?");
            $stmt->execute([$id]);
            
            header("Location: ../index.php?success=1");
            exit;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Erreur lors du traitement : " . $e->getMessage();
    }
}

// Calcul du total
$total = 0;
foreach ($lignes as $ligne) {
    if ($ligne['prix_unitaire']) {
        $total += $ligne['prix_unitaire'] * $ligne['quantite'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande <?php echo htmlspecialchars($commande['reference'] ?? ''); ?> - Magasin - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>Commande <?php echo htmlspecialchars($commande['reference'] ?? ''); ?></h1>
                <div class="header-actions">
                    <?php if ($commande['status'] === 'en_attente'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="valider">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Valider
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" 
                                onclick="if(confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
                                    document.querySelector('input[name=action]').value = 'annuler';
                                    document.querySelector('form').submit();
                                }">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    <?php elseif ($commande['status'] === 'validee'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="recevoir">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-box"></i> Marquer comme reçue
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($commande['status'] === 'en_attente'): ?>
                        <button type="button" class="btn btn-danger" 
                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')) {
                                    document.getElementById('delete-form').submit();
                                }">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    <?php endif; ?>
                    
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">Opération effectuée avec succès.</div>
            <?php endif; ?>

            <div class="commande-details">
                <div class="commande-header">
                    <div class="commande-info">
                        <div class="commande-status">
                            <?php 
                            $status = getCommandeStatusLabel($commande['status']);
                            echo '<span class="' . $status['class'] . '">' . $status['label'] . '</span>';
                            ?>
                        </div>
                        <div class="commande-dates">
                            Créée le : <?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?>
                            <?php if ($commande['date_reception']): ?>
                                <br>
                                Reçue le : <?php echo date('d/m/Y H:i', strtotime($commande['date_reception'])); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($commande['fournisseur_id']): ?>
                        <div class="commande-fournisseur">
                            <strong>Fournisseur :</strong>
                            <?php echo htmlspecialchars($commande['fournisseur_name']); ?>
                            <?php if ($commande['fournisseur_contact']): ?>
                                <br>
                                <small>Contact : <?php echo htmlspecialchars($commande['fournisseur_contact']); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($commande['notes'])): ?>
                    <div class="commande-notes">
                        <h3>Notes</h3>
                        <?php echo nl2br(htmlspecialchars($commande['notes'])); ?>
                    </div>
                <?php endif; ?>

                <div class="commande-produits">
                    <h2>Produits commandés</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Référence</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lignes as $ligne): ?>
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo htmlspecialchars($ligne['category_color']); ?>">
                                            <?php echo htmlspecialchars($ligne['category_name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($ligne['produit_reference']); ?></td>
                                    <td>
                                        <a href="../produits/voir.php?id=<?php echo $ligne['produit_id']; ?>">
                                            <?php echo htmlspecialchars($ligne['produit_name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $ligne['quantite']; ?> <?php echo $ligne['unite']; ?></td>
                                    <td><?php echo $ligne['prix_unitaire'] ? formatPrice($ligne['prix_unitaire']) : '-'; ?></td>
                                    <td>
                                        <?php 
                                        if ($ligne['prix_unitaire']) {
                                            echo formatPrice($ligne['prix_unitaire'] * $ligne['quantite']);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php if ($total > 0): ?>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Total</strong></td>
                                    <td><strong><?php echo formatPrice($total); ?></strong></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Formulaire de suppression -->
            <form id="delete-form" method="POST" style="display: none;">
                <input type="hidden" name="delete" value="confirm">
            </form>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
