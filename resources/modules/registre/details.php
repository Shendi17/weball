<?php
session_start();
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/includes/functions.php';
require_once dirname(__DIR__, 3) . '/includes/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

// Vérifier si l'ID du document est fourni
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

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
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    'tags' => ['AG', '2024', 'Procès-verbal'],
    'file' => 'pv_ag_2024.pdf',
    'size' => '2.4 MB',
    'created_at' => '2024-01-15 14:30:00',
    'updated_at' => '2024-01-15 16:45:00',
    'history' => [
        [
            'action' => 'created',
            'date' => '2024-01-15 14:30:00',
            'user' => 'Marie Dubois',
            'details' => 'Création du document'
        ],
        [
            'action' => 'updated',
            'date' => '2024-01-15 15:20:00',
            'user' => 'Marie Dubois',
            'details' => 'Mise à jour du contenu'
        ],
        [
            'action' => 'approved',
            'date' => '2024-01-15 16:45:00',
            'user' => 'Jean Martin',
            'details' => 'Approbation du document'
        ]
    ],
    'related_documents' => [
        [
            'id' => 'doc-002',
            'title' => 'Ordre du jour AG 2024',
            'type' => 'legal',
            'date' => '2024-01-10'
        ],
        [
            'id' => 'doc-003',
            'title' => 'Rapport financier 2023',
            'type' => 'financial',
            'date' => '2024-01-10'
        ]
    ]
];

$pageTitle = $document['title'];
ob_start();
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Contenu principal -->
        <div class="col-md-8">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <a href="index.php" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour au registre
                    </a>
                    <h1 class="h2 mb-0"><?php echo htmlspecialchars($document['title']); ?></h1>
                    <div class="text-muted mt-2">
                        <i class="fas fa-calendar me-1"></i>
                        <?php echo date('d/m/Y', strtotime($document['date'])); ?> |
                        <i class="fas fa-user me-1"></i>
                        <?php echo htmlspecialchars($document['author']); ?>
                    </div>
                </div>
                <div class="btn-group">
                    <a href="download.php?id=<?php echo $document['id']; ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>
                        Télécharger
                    </a>
                    <?php if (isAdmin()): ?>
                    <button class="btn btn-outline-secondary dropdown-toggle" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="edit.php?id=<?php echo $document['id']; ?>">
                                <i class="fas fa-edit me-2"></i>
                                Modifier
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#historySection">
                                <i class="fas fa-history me-2"></i>
                                Voir l'historique
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" 
                               href="delete.php?id=<?php echo $document['id']; ?>"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                <i class="fas fa-trash me-2"></i>
                                Supprimer
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contenu du document -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-4">
                        <h2 class="h5 mb-3">Description</h2>
                        <p class="mb-0">
                            <?php echo htmlspecialchars($document['description']); ?>
                        </p>
                    </div>
                    <hr>
                    <div class="mb-4">
                        <h2 class="h5 mb-3">Contenu</h2>
                        <div class="content-preview">
                            <?php echo nl2br(htmlspecialchars($document['content'])); ?>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <h2 class="h5 mb-3">Tags</h2>
                        <div class="tags">
                            <?php foreach ($document['tags'] as $tag): ?>
                                <span class="badge bg-light text-dark me-2">
                                    <?php echo htmlspecialchars($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique -->
            <div class="card mb-4" id="historySection">
                <div class="card-header">
                    <h2 class="h5 mb-0">Historique</h2>
                </div>
                <div class="card-body p-0">
                    <div class="timeline">
                        <?php foreach ($document['history'] as $event): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="h6 mb-0">
                                            <?php echo ucfirst($event['action']); ?>
                                            par <?php echo htmlspecialchars($event['user']); ?>
                                        </h3>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($event['date'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-0 mt-1">
                                        <?php echo htmlspecialchars($event['details']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre latérale -->
        <div class="col-md-4">
            <!-- Informations -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">Informations</h2>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <strong class="d-block">Type</strong>
                            <span class="text-muted">
                                <i class="fas fa-file me-2"></i>
                                <?php echo ucfirst($document['type']); ?>
                            </span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block">Catégorie</strong>
                            <span class="text-muted">
                                <i class="fas fa-folder me-2"></i>
                                <?php echo ucfirst($document['category']); ?>
                            </span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block">Statut</strong>
                            <span class="badge bg-success">
                                <?php echo ucfirst($document['status']); ?>
                            </span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block">Taille du fichier</strong>
                            <span class="text-muted">
                                <i class="fas fa-file-alt me-2"></i>
                                <?php echo $document['size']; ?>
                            </span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block">Créé le</strong>
                            <span class="text-muted">
                                <i class="fas fa-calendar me-2"></i>
                                <?php echo date('d/m/Y H:i', strtotime($document['created_at'])); ?>
                            </span>
                        </li>
                        <li>
                            <strong class="d-block">Dernière modification</strong>
                            <span class="text-muted">
                                <i class="fas fa-clock me-2"></i>
                                <?php echo date('d/m/Y H:i', strtotime($document['updated_at'])); ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Documents liés -->
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Documents liés</h2>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($document['related_documents'] as $related): ?>
                            <a href="details.php?id=<?php echo $related['id']; ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y', strtotime($related['date'])); ?>
                                    </small>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-file me-1"></i>
                                    <?php echo ucfirst($related['type']); ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 1rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    padding-left: 2.5rem;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: 1.15rem;
    top: 0;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: var(--bs-primary);
    border: 2px solid #fff;
    transform: translateX(-50%);
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
}

.content-preview {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
