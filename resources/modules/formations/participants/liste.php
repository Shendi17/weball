<?php
session_start();
require_once '../config.php';

$error = null;
$success = isset($_GET['success']) ? true : false;

// Paramètres de pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Paramètres de filtrage
$formation = isset($_GET['formation']) ? intval($_GET['formation']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';
$service = isset($_GET['service']) ? trim($_GET['service']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Construction de la requête
    $where_clauses = [];
    $params = [];
    
    if ($formation > 0) {
        $where_clauses[] = "p.formation_id = ?";
        $params[] = $formation;
    }
    
    if ($status !== '') {
        $where_clauses[] = "p.status = ?";
        $params[] = $status;
    }
    
    if ($service !== '') {
        $where_clauses[] = "p.service = ?";
        $params[] = $service;
    }
    
    if ($search !== '') {
        $where_clauses[] = "(p.nom LIKE ? OR p.prenom LIKE ? OR p.email LIKE ? OR p.telephone LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    // Récupération du nombre total de participants
    $sql = "SELECT COUNT(*) FROM formations_participants p $where_sql";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    
    // Calcul du nombre total de pages
    $total_pages = ceil($total / $limit);
    $page = min($page, $total_pages);
    
    // Récupération des participants
    $sql = "SELECT p.*, f.titre as formation_titre, f.date_debut as formation_date,
                   c.name as category_name, c.color as category_color
            FROM formations_participants p
            LEFT JOIN formations_formations f ON p.formation_id = f.id
            LEFT JOIN formations_categories c ON f.category_id = c.id
            $where_sql
            ORDER BY p.nom, p.prenom
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $participants = $stmt->fetchAll();
    
    // Récupération des formations pour le filtre
    $stmt = $pdo->query("SELECT f.*, c.name as category_name 
                         FROM formations_formations f
                         LEFT JOIN formations_categories c ON f.category_id = c.id
                         ORDER BY f.date_debut DESC");
    $formations = $stmt->fetchAll();
    
    // Récupération des services uniques pour le filtre
    $stmt = $pdo->query("SELECT DISTINCT service FROM formations_participants WHERE service != '' ORDER BY service");
    $services = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des participants - Formations - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    // Ancienne inclusion supprimée
    // <?php include '../../../includes/sidebar.php'; ?>
    // Ajout du template global
    require_once dirname(__DIR__, 4) . '/includes/template.php';
    ?>
    
    <div class="main-content">
        <?php include '../../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>Liste des participants</h1>
                <a href="ajouter.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau participant
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">Opération effectuée avec succès.</div>
            <?php endif; ?>

            <!-- Filtres -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="formation">Formation</label>
                            <select id="formation" name="formation">
                                <option value="">Toutes</option>
                                <?php foreach ($formations as $f): ?>
                                    <option value="<?php echo $f['id']; ?>" 
                                            <?php echo $formation == $f['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($f['titre']); ?>
                                        <?php if ($f['category_name']): ?>
                                            (<?php echo htmlspecialchars($f['category_name']); ?>)
                                        <?php endif; ?>
                                        - <?php echo date('d/m/Y', strtotime($f['date_debut'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="">Tous</option>
                                <option value="inscrit" <?php echo $status === 'inscrit' ? 'selected' : ''; ?>>
                                    Inscrit
                                </option>
                                <option value="present" <?php echo $status === 'present' ? 'selected' : ''; ?>>
                                    Présent
                                </option>
                                <option value="absent" <?php echo $status === 'absent' ? 'selected' : ''; ?>>
                                    Absent
                                </option>
                                <option value="annule" <?php echo $status === 'annule' ? 'selected' : ''; ?>>
                                    Annulé
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="service">Service</label>
                            <select id="service" name="service">
                                <option value="">Tous</option>
                                <?php foreach ($services as $s): ?>
                                    <option value="<?php echo htmlspecialchars($s); ?>" 
                                            <?php echo $service === $s ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($s); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="search">Recherche</label>
                            <input type="text" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="Nom, prénom, email...">
                        </div>
                    </div>

                    <div class="filters-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="liste.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Liste des participants -->
            <div class="participants-list">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Formation</th>
                                <th>Service</th>
                                <th>Contact</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $participant): ?>
                                <tr>
                                    <td>
                                        <div class="participant-name">
                                            <?php echo htmlspecialchars($participant['prenom'] . ' ' . $participant['nom']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="formation-info">
                                            <?php if ($participant['category_name']): ?>
                                                <span class="category-badge" 
                                                      style="background-color: <?php echo htmlspecialchars($participant['category_color']); ?>">
                                                    <?php echo htmlspecialchars($participant['category_name']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <a href="../formations/voir.php?id=<?php echo $participant['formation_id']; ?>">
                                                <?php echo htmlspecialchars($participant['formation_titre']); ?>
                                            </a>
                                            <div class="formation-date">
                                                <?php echo date('d/m/Y', strtotime($participant['formation_date'])); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($participant['service']): ?>
                                            <div class="service-badge">
                                                <?php echo htmlspecialchars($participant['service']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="contact-info">
                                            <?php if ($participant['email']): ?>
                                                <a href="mailto:<?php echo htmlspecialchars($participant['email']); ?>" 
                                                   class="btn btn-icon" title="Email">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($participant['telephone']): ?>
                                                <a href="tel:<?php echo htmlspecialchars($participant['telephone']); ?>" 
                                                   class="btn btn-icon" title="Téléphone">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $status = getParticipantStatusLabel($participant['status']);
                                        echo '<span class="' . $status['class'] . '">' . $status['label'] . '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="modifier.php?id=<?php echo $participant['id']; ?>" 
                                               class="btn btn-primary btn-sm" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="supprimer">
                                                <input type="hidden" name="id" value="<?php echo $participant['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce participant ?')"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($participants)): ?>
                                <tr>
                                    <td colspan="6" class="no-results">Aucun participant trouvé</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php echo "&formation=$formation&status=$status&service=" . urlencode($service) . "&search=" . urlencode($search); ?>" 
                           class="page-item">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                        <a href="?page=<?php echo $page - 1; ?><?php echo "&formation=$formation&status=$status&service=" . urlencode($service) . "&search=" . urlencode($search); ?>" 
                           class="page-item">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    
                    if ($start > 1) {
                        echo '<span class="page-item">...</span>';
                    }
                    
                    for ($i = $start; $i <= $end; $i++) {
                        $active = $i === $page ? ' active' : '';
                        echo '<a href="?page=' . $i . '&formation=' . $formation . '&status=' . $status . '&service=' . urlencode($service) . '&search=' . urlencode($search) . '" class="page-item' . $active . '">' . $i . '</a>';
                    }
                    
                    if ($end < $total_pages) {
                        echo '<span class="page-item">...</span>';
                    }
                    ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo "&formation=$formation&status=$status&service=" . urlencode($service) . "&search=" . urlencode($search); ?>" 
                           class="page-item">
                            <i class="fas fa-angle-right"></i>
                        </a>
                        <a href="?page=<?php echo $total_pages; ?><?php echo "&formation=$formation&status=$status&service=" . urlencode($service) . "&search=" . urlencode($search); ?>" 
                           class="page-item">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
