<?php
session_start();
require_once '../config.php';

$error = null;
$success = false;
$formation = null;

// Récupération de l'ID de la formation si passé en paramètre
$formation_id = isset($_GET['formation']) ? intval($_GET['formation']) : 0;

try {
    if ($formation_id > 0) {
        // Récupération des informations de la formation
        $stmt = $pdo->prepare("SELECT * FROM formations_formations WHERE id = ?");
        $stmt->execute([$formation_id]);
        $formation = $stmt->fetch();
        
        if (!$formation) {
            header("Location: liste.php");
            exit;
        }
        
        // Vérification du nombre de participants
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM formations_participants WHERE formation_id = ?");
        $stmt->execute([$formation_id]);
        $nb_participants = $stmt->fetchColumn();
        
        if ($nb_participants >= $formation['max_participants']) {
            $error = "Cette formation a atteint son nombre maximum de participants.";
        }
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
    
    // Validation
    if (empty($formation_id)) {
        $error = "La formation est obligatoire.";
    } elseif (empty($nom) || empty($prenom)) {
        $error = "Le nom et le prénom sont obligatoires.";
    } else {
        try {
            // Vérification du nombre de participants
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
            } else {
                $sql = "INSERT INTO formations_participants (
                            formation_id, nom, prenom, email, telephone, service, notes
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $formation_id,
                    $nom,
                    $prenom,
                    $email,
                    $telephone,
                    $service,
                    $notes
                ]);
                
                header("Location: ../formations/voir.php?id=$formation_id&success=1");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du participant : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau participant - Formations - WebAllOne</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once dirname(__DIR__, 4) . '/includes/template.php'; ?>
    
    <div class="main-content">
        <?php include '../../../includes/header.php'; ?>
        
        <div class="content">
            <div class="module-header">
                <h1>Nouveau participant</h1>
                <?php if ($formation): ?>
                    <a href="../formations/voir.php?id=<?php echo $formation['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la formation
                    </a>
                <?php else: ?>
                    <a href="../formations/liste.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="form-grid">
                <div class="form-section">
                    <h2>Formation</h2>
                    
                    <div class="form-group">
                        <label for="formation_id">Formation *</label>
                        <select id="formation_id" name="formation_id" required 
                                <?php echo $formation ? 'disabled' : ''; ?>>
                            <option value="">Sélectionner une formation</option>
                            <?php foreach ($formations as $f): ?>
                                <?php if ($f['nb_participants'] < $f['max_participants']): ?>
                                    <option value="<?php echo $f['id']; ?>"
                                            <?php echo $f['id'] == $formation_id ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($f['titre']); ?>
                                        <?php if ($f['category_name']): ?>
                                            (<?php echo htmlspecialchars($f['category_name']); ?>)
                                        <?php endif; ?>
                                        - <?php echo date('d/m/Y H:i', strtotime($f['date_debut'])); ?>
                                        (<?php echo $f['nb_participants']; ?>/<?php echo $f['max_participants']; ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($formation): ?>
                            <input type="hidden" name="formation_id" value="<?php echo $formation['id']; ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Informations personnelles</h2>
                    
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone">
                    </div>

                    <div class="form-group">
                        <label for="service">Service</label>
                        <input type="text" id="service" name="service">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4"></textarea>
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
