<?php
session_start();
require_once '../../config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

// Vérifier si l'ID du document est fourni
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Types de documents
$types = [
    'legal' => [
        'name' => 'Documents légaux',
        'icon' => 'fas fa-balance-scale',
        'color' => '#4299e1'
    ],
    'financial' => [
        'name' => 'Documents financiers',
        'icon' => 'fas fa-coins',
        'color' => '#48bb78'
    ],
    'contract' => [
        'name' => 'Contrats',
        'icon' => 'fas fa-file-signature',
        'color' => '#805ad5'
    ]
];

// Catégories
$categories = [
    'assemblees' => [
        'name' => 'Assemblées',
        'icon' => 'fas fa-users',
        'description' => 'Documents relatifs aux assemblées'
    ],
    'rapports' => [
        'name' => 'Rapports',
        'icon' => 'fas fa-chart-line',
        'description' => 'Rapports et analyses'
    ],
    'contrats' => [
        'name' => 'Contrats',
        'icon' => 'fas fa-file-signature',
        'description' => 'Contrats et conventions'
    ],
    'reglements' => [
        'name' => 'Règlements',
        'icon' => 'fas fa-gavel',
        'description' => 'Règlements et statuts'
    ]
];

// Statuts
$statuses = [
    'draft' => [
        'name' => 'Brouillon',
        'color' => '#718096'
    ],
    'pending' => [
        'name' => 'En attente',
        'color' => '#ed8936'
    ],
    'approved' => [
        'name' => 'Approuvé',
        'color' => '#48bb78'
    ],
    'active' => [
        'name' => 'Actif',
        'color' => '#4299e1'
    ],
    'archived' => [
        'name' => 'Archivé',
        'color' => '#a0aec0'
    ]
];

// Récupérer le document (à remplacer par une requête base de données)
$document = [
    'id' => 'doc-001',
    'title' => 'Procès-verbal Assemblée Générale 2024',
    'type' => 'legal',
    'category' => 'assemblees',
    'date' => '2024-01-15',
    'status' => 'approved',
    'author' => 'Marie Dubois',
    'description' => 'Procès-verbal de l\'assemblée générale ordinaire du 15 janvier 2024',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'tags' => ['AG', '2024', 'Procès-verbal'],
    'file' => 'pv_ag_2024.pdf',
    'size' => '2.4 MB'
];

$pageTitle = "Modifier - " . $document['title'];
ob_start();
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Formulaire principal -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="details.php?id=<?php echo $document['id']; ?>" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour au document
                    </a>
                    <h1 class="h2 mb-0">Modifier le document</h1>
                </div>
                <button type="submit" form="editDocumentForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Enregistrer
                </button>
            </div>

            <form id="editDocumentForm" action="update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="document_id" value="<?php echo $document['id']; ?>">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Informations générales</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?php echo htmlspecialchars($document['title']); ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="type" required>
                                    <?php foreach ($types as $key => $type): ?>
                                        <option value="<?php echo $key; ?>" 
                                                <?php echo $key === $document['type'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catégorie</label>
                                <select class="form-select" name="category" required>
                                    <?php foreach ($categories as $key => $category): ?>
                                        <option value="<?php echo $key; ?>"
                                                <?php echo $key === $document['category'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" 
                                       value="<?php echo $document['date']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Statut</label>
                                <select class="form-select" name="status" required>
                                    <?php foreach ($statuses as $key => $status): ?>
                                        <option value="<?php echo $key; ?>"
                                                <?php echo $key === $document['status'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required><?php 
                                echo htmlspecialchars($document['description']); 
                            ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-control" name="tags" id="tags" 
                                   value="<?php echo implode(', ', $document['tags']); ?>"
                                   placeholder="Séparez les tags par des virgules">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Contenu</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Contenu du document</label>
                            <textarea class="form-control" name="content" rows="10" required><?php 
                                echo htmlspecialchars($document['content']); 
                            ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Fichier</h2>
                    </div>
                    <div class="card-body">
                        <div class="current-file mb-3">
                            <label class="form-label">Fichier actuel</label>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file me-2"></i>
                                <span><?php echo htmlspecialchars($document['file']); ?></span>
                                <span class="text-muted ms-2">(<?php echo $document['size']; ?>)</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remplacer le fichier</label>
                            <input type="file" class="form-control" name="new_file">
                            <div class="form-text">
                                Laissez vide pour conserver le fichier actuel
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Barre latérale -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Aide</h2>
                </div>
                <div class="card-body">
                    <h3 class="h6">Types de documents</h3>
                    <ul class="list-unstyled mb-4">
                        <?php foreach ($types as $type): ?>
                            <li class="mb-2">
                                <i class="<?php echo $type['icon']; ?> me-2" 
                                   style="color: <?php echo $type['color']; ?>"></i>
                                <?php echo htmlspecialchars($type['name']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <h3 class="h6">Catégories</h3>
                    <ul class="list-unstyled mb-4">
                        <?php foreach ($categories as $category): ?>
                            <li class="mb-2">
                                <i class="<?php echo $category['icon']; ?> me-2"></i>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <h3 class="h6">Statuts</h3>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($statuses as $key => $status): ?>
                            <li class="mb-2">
                                <span class="badge me-2" style="background-color: <?php echo $status['color']; ?>">
                                    <?php echo htmlspecialchars($status['name']); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
}
</style>

<script>
// Initialisation de tagify pour les tags
new Tagify(document.getElementById('tags'), {
    whitelist: [],
    maxTags: 10,
    dropdown: {
        maxItems: 20,
        classname: "tags-look",
        enabled: 0,
        closeOnSelect: false
    }
});
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
