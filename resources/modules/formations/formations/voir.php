<?php
session_start();
require_once '../../../config.php';

// Initialisation des variables
$error = null;
$success = isset($_GET['success']) ? true : false;
$formation = null;
$participants = [];
$documents = [];
$evaluations = [];
$tables_missing = [];
$moyennes = [
    'contenu' => 0,
    'formateur' => 0,
    'organisation' => 0
];

// Récupération de l'ID de la formation
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: liste.php");
    exit;
}

try {
    // Vérification de l'existence des tables nécessaires
    $tables_required = ['formations_formations', 'formations_participants', 'formations_documents', 'formations_evaluations'];
    $tables_missing = [];
    
    foreach ($tables_required as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $tables_missing[] = $table;
        }
    }

    // Récupération de la formation
    $sql = "SELECT f.*
            FROM formations_formations f
            WHERE f.id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $formation = $stmt->fetch();
    
    if (!$formation) {
        throw new Exception("Formation non trouvée.");
    }
    
    // Récupération des participants seulement si la table existe
    if (!in_array('formations_participants', $tables_missing)) {
        $sql = "SELECT * FROM formations_participants WHERE formation_id = ? ORDER BY nom, prenom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $participants = $stmt->fetchAll();
    }
    
    // Récupération des documents seulement si la table existe
    if (!in_array('formations_documents', $tables_missing)) {
        $sql = "SELECT * FROM formations_documents WHERE formation_id = ? ORDER BY type, titre";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $documents = $stmt->fetchAll();
    }
    
    // Récupération des évaluations
    if ($formation['statut'] === 'terminee') {
        if (!in_array('formations_evaluations', $tables_missing)) {
            $sql = "SELECT e.*, p.nom as participant_nom, p.prenom as participant_prenom
                    FROM formations_evaluations e
                    JOIN formations_participants p ON e.participant_id = p.id
                    WHERE e.formation_id = ?
                    ORDER BY e.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $evaluations = $stmt->fetchAll();
            
            // Calcul des moyennes
            if (!empty($evaluations)) {
                foreach ($evaluations as $evaluation) {
                    $moyennes['contenu'] += $evaluation['contenu_note'];
                    $moyennes['formateur'] += $evaluation['formateur_note'];
                    $moyennes['organisation'] += $evaluation['organisation_note'];
                }
                
                $nb_evaluations = count($evaluations);
                $moyennes['contenu'] = round($moyennes['contenu'] / $nb_evaluations, 1);
                $moyennes['formateur'] = round($moyennes['formateur'] / $nb_evaluations, 1);
                $moyennes['organisation'] = round($moyennes['organisation'] / $nb_evaluations, 1);
            }
        }
    }
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    try {
        switch ($action) {
            case 'demarrer':
                $stmt = $pdo->prepare("UPDATE formations_formations SET statut = 'en_cours' WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: voir.php?id=$id&success=1");
                exit;
                break;
                
            case 'terminer':
                $stmt = $pdo->prepare("UPDATE formations_formations SET statut = 'terminee' WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: voir.php?id=$id&success=1");
                exit;
                break;
                
            case 'annuler':
                $stmt = $pdo->prepare("UPDATE formations_formations SET statut = 'annulee' WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: voir.php?id=$id&success=1");
                exit;
                break;
                
            case 'supprimer':
                // Suppression des documents
                if (!in_array('formations_documents', $tables_missing)) {
                    $stmt = $pdo->prepare("SELECT filename FROM formations_documents WHERE formation_id = ?");
                    $stmt->execute([$id]);
                    $files = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    foreach ($files as $file) {
                        $filepath = __DIR__ . '/../documents/' . $file;
                        if (file_exists($filepath)) {
                            unlink($filepath);
                        }
                    }
                }
                
                // Suppression de la formation
                $stmt = $pdo->prepare("DELETE FROM formations_formations WHERE id = ?");
                $stmt->execute([$id]);
                
                header("Location: liste.php?success=1");
                exit;
                break;
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de l'exécution de l'action : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($formation['titre']) ? htmlspecialchars($formation['titre']) : 'Formation'; ?> - Formations - WebAllOne</title>
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
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">Opération effectuée avec succès.</div>
            <?php endif; ?>

            <?php if ($formation): ?>
            <div class="module-header">
                <h1><?php echo htmlspecialchars($formation['titre']); ?></h1>
                <div class="header-actions">
                    <?php if (isset($formation['statut'])): ?>
                        <?php if ($formation['statut'] === 'planifiee'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="demarrer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-play"></i> Démarrer
                                </button>
                            </form>
                        <?php elseif ($formation['statut'] === 'en_cours'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="terminer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Terminer
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if (in_array($formation['statut'], ['planifiee', 'en_cours'])): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="annuler">
                                <button type="submit" class="btn btn-warning" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler cette formation ?')">
                                    <i class="fas fa-ban"></i> Annuler
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <a href="modifier.php?id=<?php echo $formation['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="supprimer">
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                    
                    <a href="liste.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="formation-details">
                <div class="formation-grid">
                    <!-- En-tête -->
                    <div class="formation-header-section">
                        <?php if (isset($formation['statut'])): ?>
                        <div class="formation-status">
                            <?php 
                            $statusLabels = [
                                'planifiee' => ['label' => 'Planifiée', 'class' => 'status-planned'],
                                'en_cours' => ['label' => 'En cours', 'class' => 'status-ongoing'],
                                'terminee' => ['label' => 'Terminée', 'class' => 'status-completed'],
                                'annulee' => ['label' => 'Annulée', 'class' => 'status-cancelled']
                            ];
                            $status = isset($statusLabels[$formation['statut']]) ? $statusLabels[$formation['statut']] : ['label' => $formation['statut'], 'class' => 'status-default'];
                            echo '<span class="status-badge ' . htmlspecialchars($status['class']) . '">' . htmlspecialchars($status['label']) . '</span>';
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="formation-section">
                        <h2>Description</h2>
                        <div class="formation-description">
                            <?php echo nl2br(htmlspecialchars($formation['description'] ?? '')); ?>
                        </div>
                    </div>

                    <div class="formation-section">
                        <h2>Dates</h2>
                        <div class="formation-dates">
                            <p><strong>Début:</strong> <?php echo isset($formation['date_debut']) ? date('d/m/Y', strtotime($formation['date_debut'])) : 'Non définie'; ?></p>
                            <p><strong>Fin:</strong> <?php echo isset($formation['date_fin']) ? date('d/m/Y', strtotime($formation['date_fin'])) : 'Non définie'; ?></p>
                        </div>
                    </div>

                    <?php if (!empty($participants)): ?>
                    <div class="formation-section">
                        <div class="section-header">
                            <h2>Participants</h2>
                            <?php if (count($participants) < ($formation['max_participants'] ?? 10)): ?>
                            <a href="../participants/ajouter.php?formation=<?php echo $formation['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus"></i> Ajouter
                            </a>
                            <?php endif; ?>
                        </div>
                        <div class="participants-list">
                            <?php foreach ($participants as $participant): ?>
                            <div class="participant-item">
                                <span class="participant-name"><?php echo htmlspecialchars($participant['nom'] . ' ' . $participant['prenom']); ?></span>
                                <?php if (!empty($participant['service'])): ?>
                                <span class="participant-service"><?php echo htmlspecialchars($participant['service']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
