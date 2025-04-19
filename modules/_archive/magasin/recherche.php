<?php
session_start();
require_once 'config.php';

$error = null;
$query = isset($_GET['q']) ? cleanInput($_GET['q']) : '';
$type = isset($_GET['type']) ? cleanInput($_GET['type']) : 'all';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$fournisseur = isset($_GET['fournisseur']) ? intval($_GET['fournisseur']) : 0;
$status = isset($_GET['status']) ? cleanInput($_GET['status']) : '';

$produits = [];
$commandes = [];
$fournisseurs = [];

try {
    // Récupération des catégories pour le filtre
    $stmt = $pdo->query("SELECT * FROM magasin_categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
    // Récupération des fournisseurs pour le filtre
    $stmt = $pdo->query("SELECT * FROM magasin_fournisseurs ORDER BY name");
    $fournisseurs_list = $stmt->fetchAll();
    
    if (!empty($query) || $category > 0 || $fournisseur > 0 || !empty($status)) {
        // Recherche de produits
        if ($type === 'all' || $type === 'produits') {
            $sql = "SELECT p.*, c.name as category_name, c.color as category_color,
                           f.name as fournisseur_name
                    FROM magasin_produits p
                    LEFT JOIN magasin_categories c ON p.category_id = c.id
                    LEFT JOIN magasin_fournisseurs f ON p.fournisseur_id = f.id
                    WHERE 1=1";
            $params = [];
            
            if (!empty($query)) {
                $sql .= " AND (p.reference LIKE ? OR p.name LIKE ? OR p.description LIKE ?)";
                $searchTerm = "%$query%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            }
            
            if ($category > 0) {
                $sql .= " AND p.category_id = ?";
                $params[] = $category;
            }
            
            if ($fournisseur > 0) {
                $sql .= " AND p.fournisseur_id = ?";
                $params[] = $fournisseur;
            }
            
            $sql .= " ORDER BY c.name, p.name LIMIT 50";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $produits = $stmt->fetchAll();
        }
        
        // Recherche de commandes
        if ($type === 'all' || $type === 'commandes') {
            $sql = "SELECT c.*, f.name as fournisseur_name,
                           (SELECT COUNT(*) FROM magasin_commandes_lignes WHERE commande_id = c.id) as nb_produits,
                           (SELECT SUM(quantite * prix_unitaire) 
                            FROM magasin_commandes_lignes 
                            WHERE commande_id = c.id AND prix_unitaire IS NOT NULL) as total
                    FROM magasin_commandes c
                    LEFT JOIN magasin_fournisseurs f ON c.fournisseur_id = f.id
                    WHERE 1=1";
            $params = [];
            
            if (!empty($query)) {
                $sql .= " AND (c.reference LIKE ? OR c.notes LIKE ?)";
                $searchTerm = "%$query%";
                $params = array_merge($params, [$searchTerm, $searchTerm]);
            }
            
            if ($fournisseur > 0) {
                $sql .= " AND c.fournisseur_id = ?";
                $params[] = $fournisseur;
            }
            
            if (!empty($status)) {
                $sql .= " AND c.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY c.date_commande DESC LIMIT 50";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $commandes = $stmt->fetchAll();
        }
        
        // Recherche de fournisseurs
        if ($type === 'all' || $type === 'fournisseurs') {
            $sql = "SELECT f.*,
                           (SELECT COUNT(*) FROM magasin_produits WHERE fournisseur_id = f.id) as nb_produits,
                           (SELECT COUNT(*) FROM magasin_commandes WHERE fournisseur_id = f.id) as nb_commandes
                    FROM magasin_fournisseurs f
                    WHERE 1=1";
            $params = [];
            
            if (!empty($query)) {
                $sql .= " AND (f.name LIKE ? OR f.contact_name LIKE ? OR f.email LIKE ? OR f.phone LIKE ?)";
                $searchTerm = "%$query%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            $sql .= " ORDER BY f.name LIMIT 50";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $fournisseurs = $stmt->fetchAll();
        }
    }
    
} catch (PDOException $e) {
    $error = "Erreur lors de la recherche : " . $e->getMessage();
}

function highlightSearch($text, $search) {
    if (empty($search)) return htmlspecialchars($text);
    return preg_replace(
        '/(' . preg_quote($search, '/') . ')/i',
        '<mark>$1</mark>',
        htmlspecialchars($text)
    );
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - Magasin - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include '../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>Recherche</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Formulaire de recherche -->
            <form method="GET" class="search-form">
                <div class="search-main">
                    <div class="search-input">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" 
                               placeholder="Rechercher...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                    
                    <div class="search-type">
                        <label>
                            <input type="radio" name="type" value="all" 
                                   <?php echo $type === 'all' ? 'checked' : ''; ?>>
                            Tout
                        </label>
                        <label>
                            <input type="radio" name="type" value="produits" 
                                   <?php echo $type === 'produits' ? 'checked' : ''; ?>>
                            Produits
                        </label>
                        <label>
                            <input type="radio" name="type" value="commandes" 
                                   <?php echo $type === 'commandes' ? 'checked' : ''; ?>>
                            Commandes
                        </label>
                        <label>
                            <input type="radio" name="type" value="fournisseurs" 
                                   <?php echo $type === 'fournisseurs' ? 'checked' : ''; ?>>
                            Fournisseurs
                        </label>
                    </div>
                </div>

                <div class="search-filters">
                    <div class="filter-group">
                        <label for="category">Catégorie</label>
                        <select id="category" name="category">
                            <option value="">Toutes</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="fournisseur">Fournisseur</label>
                        <select id="fournisseur" name="fournisseur">
                            <option value="">Tous</option>
                            <?php foreach ($fournisseurs_list as $f): ?>
                                <option value="<?php echo $f['id']; ?>" 
                                        <?php echo $fournisseur == $f['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($f['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="status">Statut commande</label>
                        <select id="status" name="status">
                            <option value="">Tous</option>
                            <option value="en_attente" <?php echo $status === 'en_attente' ? 'selected' : ''; ?>>
                                En attente
                            </option>
                            <option value="validee" <?php echo $status === 'validee' ? 'selected' : ''; ?>>
                                Validée
                            </option>
                            <option value="recue" <?php echo $status === 'recue' ? 'selected' : ''; ?>>
                                Reçue
                            </option>
                            <option value="annulee" <?php echo $status === 'annulee' ? 'selected' : ''; ?>>
                                Annulée
                            </option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Résultats -->
            <?php if (!empty($query) || $category > 0 || $fournisseur > 0 || !empty($status)): ?>
                <div class="search-results">
                    <?php if ($type === 'all' || $type === 'produits'): ?>
                        <section class="results-section">
                            <h2>
                                <i class="fas fa-box"></i>
                                Produits (<?php echo count($produits); ?>)
                            </h2>
                            
                            <?php if (!empty($produits)): ?>
                                <div class="produits-grid">
                                    <?php foreach ($produits as $produit): ?>
                                        <div class="produit-card">
                                            <div class="produit-header">
                                                <div class="produit-category" 
                                                     style="background-color: <?php echo htmlspecialchars($produit['category_color']); ?>">
                                                    <?php echo highlightSearch($produit['category_name'], $query); ?>
                                                </div>
                                                <div class="produit-reference">
                                                    <?php echo highlightSearch($produit['reference'], $query); ?>
                                                </div>
                                            </div>
                                            <div class="produit-content">
                                                <h3>
                                                    <a href="produits/voir.php?id=<?php echo $produit['id']; ?>">
                                                        <?php echo highlightSearch($produit['name'], $query); ?>
                                                    </a>
                                                </h3>
                                                <?php if ($produit['fournisseur_name']): ?>
                                                    <div class="produit-fournisseur">
                                                        <?php echo highlightSearch($produit['fournisseur_name'], $query); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="produit-stock">
                                                    <span class="stock-value <?php echo $produit['quantite'] <= $produit['seuil_alerte'] ? ($produit['quantite'] == 0 ? 'stock-empty' : 'stock-low') : ''; ?>">
                                                        Stock : <?php echo $produit['quantite']; ?> <?php echo $produit['unite']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-results">Aucun produit trouvé</p>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>

                    <?php if ($type === 'all' || $type === 'commandes'): ?>
                        <section class="results-section">
                            <h2>
                                <i class="fas fa-shopping-cart"></i>
                                Commandes (<?php echo count($commandes); ?>)
                            </h2>
                            
                            <?php if (!empty($commandes)): ?>
                                <div class="commandes-list">
                                    <?php foreach ($commandes as $commande): ?>
                                        <div class="commande-item">
                                            <div class="commande-header">
                                                <div class="commande-reference">
                                                    <a href="commandes/voir.php?id=<?php echo $commande['id']; ?>">
                                                        <?php echo highlightSearch($commande['reference'], $query); ?>
                                                    </a>
                                                </div>
                                                <div class="commande-status">
                                                    <?php 
                                                    $status = getCommandeStatusLabel($commande['status']);
                                                    echo '<span class="' . $status['class'] . '">' . $status['label'] . '</span>';
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="commande-content">
                                                <?php if ($commande['fournisseur_name']): ?>
                                                    <div class="commande-fournisseur">
                                                        <?php echo highlightSearch($commande['fournisseur_name'], $query); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="commande-date">
                                                    <?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?>
                                                </div>
                                                <div class="commande-stats">
                                                    <span class="commande-produits">
                                                        <?php echo $commande['nb_produits']; ?> produit(s)
                                                    </span>
                                                    <?php if ($commande['total']): ?>
                                                        <span class="commande-total">
                                                            <?php echo formatPrice($commande['total']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-results">Aucune commande trouvée</p>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>

                    <?php if ($type === 'all' || $type === 'fournisseurs'): ?>
                        <section class="results-section">
                            <h2>
                                <i class="fas fa-building"></i>
                                Fournisseurs (<?php echo count($fournisseurs); ?>)
                            </h2>
                            
                            <?php if (!empty($fournisseurs)): ?>
                                <div class="fournisseurs-grid">
                                    <?php foreach ($fournisseurs as $fournisseur): ?>
                                        <div class="fournisseur-card">
                                            <div class="fournisseur-header">
                                                <h3>
                                                    <a href="fournisseurs/voir.php?id=<?php echo $fournisseur['id']; ?>">
                                                        <?php echo highlightSearch($fournisseur['name'], $query); ?>
                                                    </a>
                                                </h3>
                                            </div>
                                            <div class="fournisseur-content">
                                                <?php if ($fournisseur['contact_name']): ?>
                                                    <div class="fournisseur-contact">
                                                        <i class="fas fa-user"></i>
                                                        <?php echo highlightSearch($fournisseur['contact_name'], $query); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($fournisseur['email']): ?>
                                                    <div class="fournisseur-email">
                                                        <i class="fas fa-envelope"></i>
                                                        <?php echo highlightSearch($fournisseur['email'], $query); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($fournisseur['phone']): ?>
                                                    <div class="fournisseur-phone">
                                                        <i class="fas fa-phone"></i>
                                                        <?php echo highlightSearch($fournisseur['phone'], $query); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="fournisseur-stats">
                                                <div class="stat-item">
                                                    <i class="fas fa-box"></i>
                                                    <?php echo $fournisseur['nb_produits']; ?> produit(s)
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <?php echo $fournisseur['nb_commandes']; ?> commande(s)
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-results">Aucun fournisseur trouvé</p>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
