<?php
session_start();
require_once '../config.php';

$error = null;
$success = isset($_GET['success']) ? true : false;
$participant = null;

// Récupération de l'ID du participant
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: liste.php");
    exit;
}

try {
    // Récupération des informations du participant
    $stmt = $pdo->prepare("SELECT p.*, f.titre as formation_titre, f.date_debut as formation_date,
                                 c.name as category_name
                          FROM formations_participants p
                          LEFT JOIN formations_formations f ON p.formation_id = f.id
                          LEFT JOIN formations_categories c ON f.category_id = c.id
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    $participant = $stmt->fetch();
    
    if (!$participant) {
        header("Location: liste.php");
        exit;
    }
    
    // Récupération de la liste des formations pour le select
    $sql = "SELECT f.*, c.name as category_name,
                   (SELECT COUNT(*) FROM formations_participants WHERE formation_id = f.id) as nb_participants
            FROM formations_formations f
            LEFT JOIN formations_categories c ON f.category_id = c.id
            WHERE f.status IN ('planifiee', 'en_cours')
            ORDER BY f.date_debut ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $formations = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formation_id = intval($_POST['formation_id']);
    $nom = cleanInput($_POST['nom']);
    $prenom = cleanInput($_POST['prenom']);
    $email = cleanInput($_POST['email']);
    $telephone = cleanInput($_POST['telephone']);
    $service = cleanInput($_POST['service']);
    $notes = cleanInput($_POST['notes']);
    $status = cleanInput($_POST['status']);
    
    // Validation
    if (empty($formation_id)) {
        $error = "La formation est obligatoire.";
    } elseif (empty($nom) || empty($prenom)) {
        $error = "Le nom et le prénom sont obligatoires.";
    } else {
        try {
            // Vérification du nombre de participants si changement de formation
            if ($formation_id !== $participant['formation_id']) {
                $stmt = $pdo->prepare("SELECT f.*, 
                                            (SELECT COUNT(*) FROM formations_participants WHERE formation_id = f.id) as nb_participants
                                     FROM formations_formations f 
                                     WHERE f.id = ?");
                $stmt->execute([$formation_id]);
                $formation = $stmt->fetch();
                
                if (!$formation) {
                    $error = "Formation invalide.";
                } elseif ($formation['nb_participants'] >= $formation['max_participants']) {
                    $error = "Cette formation a atteint son nombre maximum de participants.";
                }
            }
            
            if (!$error) {
                $sql = "UPDATE formations_participants 
                        SET formation_id = ?, nom = ?, prenom = ?, email = ?, 
                            telephone = ?, service = ?, notes = ?, status = ?
                        WHERE id = ?";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $formation_id,
                    $nom,
                    $prenom,
                    $email,
                    $telephone,
                    $service,
                    $notes,
                    $status,
                    $id
                ]);
                
                header("Location: liste.php?success=1");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification du participant : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un participant - Formations - WebAllOne</title>
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
                <h1>Modifier un participant</h1>
                <a href="liste.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">Modifications enregistrées avec succès.</div>
            <?php endif; ?>

            <form method="POST" class="form-grid">
                <div class="form-section">
                    <h2>Formation</h2>
                    
                    <div class="form-group">
                        <label for="formation_id">Formation *</label>
                        <select id="formation_id" name="formation_id" required>
                            <option value="">Sélectionner une formation</option>
                            <?php foreach ($formations as $f): ?>
                                <?php 
                                $disabled = $f['nb_participants'] >= $f['max_participants'] && 
                                          $f['id'] !== $participant['formation_id'];
                                ?>
                                <option value="<?php echo $f['id']; ?>"
                                        <?php echo $f['id'] == $participant['formation_id'] ? 'selected' : ''; ?>
                                        <?php echo $disabled ? 'disabled' : ''; ?>>
                                    <?php echo htmlspecialchars($f['titre']); ?>
                                    <?php if ($f['category_name']): ?>
                                        (<?php echo htmlspecialchars($f['category_name']); ?>)
                                    <?php endif; ?>
                                    - <?php echo date('d/m/Y H:i', strtotime($f['date_debut'])); ?>
                                    (<?php echo $f['nb_participants']; ?>/<?php echo $f['max_participants']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Statut *</label>
                        <select id="status" name="status" required>
                            <option value="inscrit" <?php echo $participant['status'] === 'inscrit' ? 'selected' : ''; ?>>
                                Inscrit
                            </option>
                            <option value="present" <?php echo $participant['status'] === 'present' ? 'selected' : ''; ?>>
                                Présent
                            </option>
                            <option value="absent" <?php echo $participant['status'] === 'absent' ? 'selected' : ''; ?>>
                                Absent
                            </option>
                            <option value="annule" <?php echo $participant['status'] === 'annule' ? 'selected' : ''; ?>>
                                Annulé
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Informations personnelles</h2>
                    
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" required
                               value="<?php echo htmlspecialchars($participant['nom']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required
                               value="<?php echo htmlspecialchars($participant['prenom']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($participant['email']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone"
                               value="<?php echo htmlspecialchars($participant['telephone']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="service">Service</label>
                        <input type="text" id="service" name="service"
                               value="<?php echo htmlspecialchars($participant['service']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($participant['notes']); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
