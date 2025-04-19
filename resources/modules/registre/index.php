<?php
session_start();
require_once '../../config.php';
require_once '../../includes/functions.php';
require_once '../../includes/db.php';
require_once 'data/demo_documents.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
}

$pageTitle = "Registre";
ob_start();
?>

<div class="container mt-5">
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Colonne de gauche : Catégories et Filtres -->
            <div class="col-md-3">
                <!-- Catégories -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Catégories</h2>
                        <?php if (isAdmin()): ?>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action active" data-category="">
                                <i class="fas fa-folder me-2"></i>
                                Tous les documents
                                <span class="badge bg-secondary float-end">
                                    <?php echo count($demo_documents); ?>
                                </span>
                            </a>
                            <?php foreach ($document_categories as $key => $category): ?>
                                <a href="#" class="list-group-item list-group-item-action" data-category="<?php echo $key; ?>">
                                    <i class="<?php echo $category['icon']; ?> me-2"></i>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                    <span class="badge bg-secondary float-end">
                                        <?php 
                                            echo count(array_filter($demo_documents, function($doc) use ($key) {
                                                return $doc['category'] === $key;
                                            }));
                                        ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Filtres</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="filterType">
                                <option value="">Tous les types</option>
                                <?php foreach ($document_types as $key => $type): ?>
                                    <option value="<?php echo $key; ?>">
                                        <?php echo htmlspecialchars($type['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="filterStatus">
                                <option value="">Tous les statuts</option>
                                <?php foreach ($document_statuses as $key => $status): ?>
                                    <option value="<?php echo $key; ?>">
                                        <?php echo htmlspecialchars($status['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control" id="dateStart">
                                <input type="date" class="form-control" id="dateEnd">
                            </div>
                        </div>
                        <button class="btn btn-primary w-100" id="applyFilters">
                            Appliquer les filtres
                        </button>
                    </div>
                </div>
            </div>

            <!-- Colonne centrale : Liste des documents -->
            <div class="col-md-9">
                <!-- Barre d'actions -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchDocument" 
                                           placeholder="Rechercher un document...">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary" id="toggleView">
                                        <i class="fas fa-th-list"></i>
                                    </button>
                                    <?php if (isAdmin()): ?>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Nouveau document
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des documents -->
                <div class="documents-grid" id="documentsList">
                    <?php foreach ($demo_documents as $document): ?>
                        <div class="document-item" 
                             data-category="<?php echo $document['category']; ?>"
                             data-type="<?php echo $document['type']; ?>"
                             data-status="<?php echo $document['status']; ?>"
                             data-date="<?php echo $document['date']; ?>">
                            <div class="card h-100">
                                <div class="card-body">
                                    <!-- En-tête du document -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="document-icon me-3" 
                                             style="background-color: <?php echo $document_types[$document['type']]['color']; ?>">
                                            <i class="<?php echo $document_types[$document['type']]['icon']; ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h3 class="h5 mb-1">
                                                <a href="details.php?id=<?php echo $document['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($document['title']); ?>
                                                </a>
                                            </h3>
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($document['date'])); ?> |
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($document['author']); ?>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <span class="badge" 
                                                  style="background-color: <?php echo $document_statuses[$document['status']]['color']; ?>">
                                                <?php echo $document_statuses[$document['status']]['name']; ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="card-text mb-3">
                                        <?php echo htmlspecialchars($document['description']); ?>
                                    </p>

                                    <!-- Tags -->
                                    <div class="mb-3">
                                        <?php foreach ($document['tags'] as $tag): ?>
                                            <span class="badge bg-light text-dark me-1">
                                                <?php echo htmlspecialchars($tag); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted small">
                                            <i class="fas fa-file me-1"></i>
                                            <?php echo $document['size']; ?>
                                        </div>
                                        <div class="btn-group">
                                            <a href="download.php?id=<?php echo $document['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-2"></i>
                                                Télécharger
                                            </a>
                                            <?php if (isAdmin()): ?>
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
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
                                                    <a class="dropdown-item" href="details.php?id=<?php echo $document['id']; ?>#historySection">
                                                        <i class="fas fa-history me-2"></i>
                                                        Historique
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
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.document-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.document-icon i {
    font-size: 1.5rem;
    color: white;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1rem;
}

.documents-grid.list-view {
    grid-template-columns: 1fr;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
}

.list-group-item.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

@media (max-width: 768px) {
    .documents-grid {
        grid-template-columns: 1fr;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-group .btn {
        width: 100%;
        margin: 0 !important;
        border-radius: 0.375rem !important;
    }
}
</style>

<script>
// Filtrage des documents
const searchInput = document.getElementById('searchDocument');
const documentItems = document.querySelectorAll('.document-item');
const categoryLinks = document.querySelectorAll('.list-group-item');
const typeSelect = document.getElementById('filterType');
const statusSelect = document.getElementById('filterStatus');
const dateStartInput = document.getElementById('dateStart');
const dateEndInput = document.getElementById('dateEnd');
const applyFiltersBtn = document.getElementById('applyFilters');
const toggleViewBtn = document.getElementById('toggleView');
const documentsGrid = document.getElementById('documentsList');

let activeCategory = '';

function filterDocuments() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedType = typeSelect.value;
    const selectedStatus = statusSelect.value;
    const startDate = dateStartInput.value ? new Date(dateStartInput.value) : null;
    const endDate = dateEndInput.value ? new Date(dateEndInput.value) : null;

    documentItems.forEach(item => {
        const matchSearch = item.textContent.toLowerCase().includes(searchTerm);
        const matchCategory = !activeCategory || item.dataset.category === activeCategory;
        const matchType = !selectedType || item.dataset.type === selectedType;
        const matchStatus = !selectedStatus || item.dataset.status === selectedStatus;
        
        let matchDate = true;
        if (startDate || endDate) {
            const itemDate = new Date(item.dataset.date);
            if (startDate && itemDate < startDate) matchDate = false;
            if (endDate && itemDate > endDate) matchDate = false;
        }

        item.style.display = 
            matchSearch && matchCategory && matchType && 
            matchStatus && matchDate ? '' : 'none';
    });
}

// Event listeners
searchInput.addEventListener('input', filterDocuments);
applyFiltersBtn.addEventListener('click', filterDocuments);

categoryLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        categoryLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        activeCategory = link.dataset.category;
        filterDocuments();
    });
});

// Changement de vue
toggleViewBtn.addEventListener('click', () => {
    documentsGrid.classList.toggle('list-view');
    const icon = toggleViewBtn.querySelector('i');
    icon.className = documentsGrid.classList.contains('list-view') ? 
        'fas fa-th' : 'fas fa-th-list';
});
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
