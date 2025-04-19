<?php
session_start();
require_once '../config.php';

$error = null;
$success = false;
$fournisseurs = [];
$produits = [];
$produit_preselectionne = null;

try {
    // Récupération des fournisseurs
    $stmt = $pdo->query("SELECT * FROM magasin_fournisseurs ORDER BY name");
    $fournisseurs = $stmt->fetchAll();
    
    // Récupération des produits
    $sql = "SELECT p.*, c.name as category_name 
            FROM magasin_produits p 
            LEFT JOIN magasin_categories c ON p.category_id = c.id 
            WHERE p.status != 'inactif' 
            ORDER BY c.name, p.name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $produits = $stmt->fetchAll();
    
    // Si un produit est spécifié dans l'URL
    if (isset($_GET['produit'])) {
        $stmt = $pdo->prepare("SELECT * FROM magasin_produits WHERE id = ?");
        $stmt->execute([intval($_GET['produit'])]);
        $produit_preselectionne = $stmt->fetch();
    }
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fournisseur_id = !empty($_POST['fournisseur_id']) ? intval($_POST['fournisseur_id']) : null;
    $notes = cleanInput($_POST['notes']);
    $produits_commande = isset($_POST['produits']) ? $_POST['produits'] : [];
    $quantites = isset($_POST['quantites']) ? $_POST['quantites'] : [];
    $prix_unitaires = isset($_POST['prix_unitaires']) ? $_POST['prix_unitaires'] : [];
    
    // Validation
    if (empty($produits_commande)) {
        $error = "Veuillez sélectionner au moins un produit.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Création de la commande
            $reference = generateReference('CMD');
            $sql = "INSERT INTO magasin_commandes (
                        fournisseur_id, reference, status, notes
                    ) VALUES (?, ?, 'en_attente', ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fournisseur_id, $reference, $notes]);
            $commande_id = $pdo->lastInsertId();
            
            // Ajout des lignes de commande
            $sql = "INSERT INTO magasin_commandes_lignes (
                        commande_id, produit_id, quantite, prix_unitaire
                    ) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            foreach ($produits_commande as $index => $produit_id) {
                if (!empty($quantites[$index])) {
                    $quantite = intval($quantites[$index]);
                    $prix_unitaire = !empty($prix_unitaires[$index]) ? 
                        floatval(str_replace(',', '.', $prix_unitaires[$index])) : null;
                    
                    $stmt->execute([
                        $commande_id,
                        $produit_id,
                        $quantite,
                        $prix_unitaire
                    ]);
                }
            }
            
            $pdo->commit();
            header("Location: voir.php?id=$commande_id&success=1");
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erreur lors de la création de la commande : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande - Magasin - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once '../../../includes/template.php'; ?>
    
    <div class="main-content">
        <?php include '../../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>Nouvelle commande</h1>
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="form-grid">
                <div class="form-section">
                    <h2>Informations générales</h2>
                    
                    <div class="form-group">
                        <label for="fournisseur_id">Fournisseur</label>
                        <select id="fournisseur_id" name="fournisseur_id">
                            <option value="">Sélectionner un fournisseur</option>
                            <?php foreach ($fournisseurs as $fournisseur): ?>
                                <option value="<?php echo $fournisseur['id']; ?>">
                                    <?php echo htmlspecialchars($fournisseur['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Produits</h2>
                    
                    <div id="produits-container">
                        <div class="produit-ligne" data-index="0">
                            <div class="form-group">
                                <label>Produit</label>
                                <select name="produits[]" required>
                                    <option value="">Sélectionner un produit</option>
                                    <?php 
                                    $current_category = null;
                                    foreach ($produits as $produit):
                                        if ($produit['category_name'] !== $current_category):
                                            if ($current_category !== null) echo '</optgroup>';
                                            $current_category = $produit['category_name'];
                                            echo '<optgroup label="' . htmlspecialchars($current_category) . '">';
                                        endif;
                                    ?>
                                        <option value="<?php echo $produit['id']; ?>"
                                                data-prix="<?php echo $produit['prix_achat']; ?>"
                                                <?php echo $produit_preselectionne && $produit['id'] == $produit_preselectionne['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($produit['name']); ?>
                                            (Ref: <?php echo htmlspecialchars($produit['reference']); ?>)
                                        </option>
                                    <?php 
                                    endforeach;
                                    if ($current_category !== null) echo '</optgroup>';
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantité</label>
                                <input type="number" name="quantites[]" min="1" required>
                            </div>
                            <div class="form-group">
                                <label>Prix unitaire (€)</label>
                                <input type="number" name="prix_unitaires[]" step="0.01" min="0">
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-produit" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="button" id="add-produit" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('produits-container');
        const addButton = document.getElementById('add-produit');
        let index = 0;

        // Fonction pour mettre à jour l'affichage des boutons de suppression
        function updateRemoveButtons() {
            const lines = container.querySelectorAll('.produit-ligne');
            lines.forEach(line => {
                const button = line.querySelector('.remove-produit');
                button.style.display = lines.length > 1 ? 'block' : 'none';
            });
        }

        // Fonction pour ajouter une nouvelle ligne
        function addLine() {
            index++;
            const template = container.querySelector('.produit-ligne').cloneNode(true);
            template.dataset.index = index;
            
            // Réinitialisation des valeurs
            template.querySelectorAll('input').forEach(input => input.value = '');
            template.querySelector('select').selectedIndex = 0;
            
            // Ajout des événements
            template.querySelector('.remove-produit').addEventListener('click', function() {
                template.remove();
                updateRemoveButtons();
            });
            
            container.appendChild(template);
            updateRemoveButtons();
        }

        // Événement pour le bouton d'ajout
        addButton.addEventListener('click', addLine);

        // Mise en place des événements sur la première ligne
        container.querySelector('.remove-produit').addEventListener('click', function(e) {
            e.target.closest('.produit-ligne').remove();
            updateRemoveButtons();
        });

        // Gestion des prix unitaires
        container.addEventListener('change', function(e) {
            if (e.target.matches('select[name="produits[]"]')) {
                const option = e.target.selectedOptions[0];
                const prix = option.dataset.prix;
                const ligne = e.target.closest('.produit-ligne');
                const prixInput = ligne.querySelector('input[name="prix_unitaires[]"]');
                if (prix) {
                    prixInput.value = prix;
                }
            }
        });
    });
    </script>
</body>
</html>
