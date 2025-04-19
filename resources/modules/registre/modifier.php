<?php
session_start();
require_once 'config.php';

$error = null;
$success = false;
$entry = null;

// Récupération des catégories
try {
    $stmt = $pdo->query("SELECT * FROM register_categories ORDER BY ordre");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des catégories : " . $e->getMessage();
}

// Récupération de l'entrée à modifier
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM register_entries WHERE id = ?");
        $stmt->execute([$id]);
        $entry = $stmt->fetch();
        
        if (!$entry) {
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération de l'entrée : " . $e->getMessage();
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $title = cleanInput($_POST['title']);
    $description = cleanInput($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $status = cleanInput($_POST['status']);
    $priority = cleanInput($_POST['priority']);
    $date_event = cleanInput($_POST['date_event']);
    $date_deadline = !empty($_POST['date_deadline']) ? cleanInput($_POST['date_deadline']) : null;
    $assigned_to = cleanInput($_POST['assigned_to']);
    $tags = !empty($_POST['tags']) ? cleanInput($_POST['tags']) : null;
    
    // Validation
    if (empty($title) || empty($description) || empty($date_event)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!isValidDate($date_event)) {
        $error = "La date de l'événement n'est pas valide.";
    } elseif (!empty($date_deadline) && !isValidDate($date_deadline)) {
        $error = "La date limite n'est pas valide.";
    } else {
        try {
            // Gestion des fichiers joints
            $attachments = [];
            if (!empty($_FILES['attachments']['name'][0])) {
                foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = $_FILES['attachments']['name'][$key];
                        $fileSize = $_FILES['attachments']['size'][$key];
                        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        // Vérifications
                        if ($fileSize > MAX_FILE_SIZE) {
                            throw new Exception("Le fichier $fileName est trop volumineux.");
                        }
                        if (!in_array($fileType, ALLOWED_EXTENSIONS)) {
                            throw new Exception("Le type de fichier .$fileType n'est pas autorisé.");
                        }
                        
                        // Génération d'un nom unique
                        $newFileName = uniqid() . '_' . cleanFileName($fileName);
                        $targetPath = UPLOAD_DIR . $newFileName;
                        
                        if (move_uploaded_file($tmp_name, $targetPath)) {
                            $attachments[] = [
                                'name' => $fileName,
                                'path' => $newFileName,
                                'size' => $fileSize,
                                'type' => $fileType
                            ];
                        }
                    }
                }
            }
            
            // Récupération des pièces jointes existantes
            if (!empty($entry['attachments'])) {
                $existingAttachments = json_decode($entry['attachments'], true);
                if (is_array($existingAttachments)) {
                    $attachments = array_merge($existingAttachments, $attachments);
                }
            }
            
            // Mise à jour dans la base de données
            $sql = "UPDATE register_entries SET 
                        category_id = ?, title = ?, description = ?, 
                        status = ?, priority = ?, date_event = ?, 
                        date_deadline = ?, assigned_to = ?, tags = ?, 
                        attachments = ?
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $category_id,
                $title,
                $description,
                $status,
                $priority,
                $date_event,
                $date_deadline,
                $assigned_to,
                $tags,
                !empty($attachments) ? json_encode($attachments) : null,
                $id
            ]);
            
            header("Location: voir.php?id=$id&updated=1");
            exit;
            
        } catch (Exception $e) {
            $error = "Erreur lors de la modification de l'entrée : " . $e->getMessage();
        }
    }
}

$pageTitle = 'Modifier fiche Registre';
ob_start();
?>
<div class="container mt-5">
    <div class="module-header">
        <h1>Modifier l'entrée</h1>
        <a href="voir.php?id=<?php echo $entry['id']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($entry): ?>
        <form method="POST" enctype="multipart/form-data" class="form-register">
            <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
            
            <div class="form-grid">
                <div class="form-section">
                    <h2>Informations principales</h2>
                    
                    <div class="form-group">
                        <label for="title">Titre *</label>
                        <input type="text" id="title" name="title" required
                               value="<?php echo htmlspecialchars($entry['title']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="5" required><?php 
                            echo htmlspecialchars($entry['description']); 
                        ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Catégorie *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                        <?php echo ($entry['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Détails</h2>
                    
                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select id="status" name="status">
                            <option value="en_cours" <?php echo $entry['status'] === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                            <option value="termine" <?php echo $entry['status'] === 'termine' ? 'selected' : ''; ?>>Terminé</option>
                            <option value="en_attente" <?php echo $entry['status'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="annule" <?php echo $entry['status'] === 'annule' ? 'selected' : ''; ?>>Annulé</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priorité</label>
                        <select id="priority" name="priority">
                            <option value="normale" <?php echo $entry['priority'] === 'normale' ? 'selected' : ''; ?>>Normale</option>
                            <option value="basse" <?php echo $entry['priority'] === 'basse' ? 'selected' : ''; ?>>Basse</option>
                            <option value="haute" <?php echo $entry['priority'] === 'haute' ? 'selected' : ''; ?>>Haute</option>
                            <option value="urgente" <?php echo $entry['priority'] === 'urgente' ? 'selected' : ''; ?>>Urgente</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_event">Date de l'événement *</label>
                        <input type="datetime-local" id="date_event" name="date_event" required
                               value="<?php echo date('Y-m-d\TH:i', strtotime($entry['date_event'])); ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_deadline">Date limite</label>
                        <input type="datetime-local" id="date_deadline" name="date_deadline"
                               value="<?php echo $entry['date_deadline'] ? date('Y-m-d\TH:i', strtotime($entry['date_deadline'])) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="assigned_to">Assigné à</label>
                        <input type="text" id="assigned_to" name="assigned_to"
                               value="<?php echo htmlspecialchars($entry['assigned_to']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags (séparés par des virgules)</label>
                        <input type="text" id="tags" name="tags"
                               value="<?php echo htmlspecialchars($entry['tags']); ?>">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Pièces jointes</h2>
                    
                    <?php if (!empty($entry['attachments'])): ?>
                        <div class="current-attachments">
                            <h3>Fichiers actuels</h3>
                            <div class="attachments-list">
                                <?php foreach (getAttachmentsArray($entry['attachments']) as $attachment): ?>
                                    <div class="attachment-item">
                                        <i class="fas fa-file"></i>
                                        <span class="attachment-name"><?php echo htmlspecialchars($attachment['name']); ?></span>
                                        <span class="attachment-size">
                                            <?php echo number_format($attachment['size'] / 1024, 2); ?> Ko
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="attachments">Ajouter des fichiers</label>
                        <input type="file" id="attachments" name="attachments[]" multiple>
                        <small class="form-text">
                            Formats acceptés : <?php echo implode(', ', ALLOWED_EXTENSIONS); ?><br>
                            Taille maximale : <?php echo MAX_FILE_SIZE / (1024 * 1024); ?> Mo
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
