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

// Données de démonstration pour les catégories
$categories = [
    'actualites' => [
        'name' => 'Actualités',
        'icon' => 'fas fa-newspaper',
        'color' => '#4299e1',
        'description' => 'Nouvelles et mises à jour'
    ],
    'evenements' => [
        'name' => 'Événements',
        'icon' => 'fas fa-calendar-alt',
        'color' => '#48bb78',
        'description' => 'Événements à venir et passés'
    ],
    'tutoriels' => [
        'name' => 'Tutoriels',
        'icon' => 'fas fa-graduation-cap',
        'color' => '#ed8936',
        'description' => 'Guides et instructions'
    ],
    'communiques' => [
        'name' => 'Communiqués',
        'icon' => 'fas fa-bullhorn',
        'color' => '#9f7aea',
        'description' => 'Annonces officielles'
    ]
];

// Données de démonstration pour les articles
$articles = [
    [
        'id' => 1,
        'titre' => 'Lancement de la nouvelle plateforme',
        'category' => 'actualites',
        'contenu' => 'Nous sommes ravis de vous annoncer le lancement de notre nouvelle plateforme collaborative...',
        'auteur' => 'Marie Dubois',
        'date_creation' => '2024-01-05',
        'statut' => 'publie',
        'tags' => ['Plateforme', 'Innovation', 'Digital'],
        'vues' => 156,
        'image' => 'platform-launch.jpg'
    ],
    [
        'id' => 2,
        'titre' => 'Séminaire sur l\'innovation - Mars 2024',
        'category' => 'evenements',
        'contenu' => 'Rejoignez-nous pour une journée exceptionnelle dédiée à l\'innovation et aux nouvelles technologies...',
        'auteur' => 'Jean Martin',
        'date_creation' => '2024-01-06',
        'statut' => 'publie',
        'tags' => ['Séminaire', 'Innovation', 'Technologie'],
        'vues' => 89,
        'image' => 'seminar.jpg'
    ],
    [
        'id' => 3,
        'titre' => 'Guide : Optimiser son espace de travail',
        'category' => 'tutoriels',
        'contenu' => 'Découvrez nos conseils pratiques pour organiser efficacement votre espace de travail...',
        'auteur' => 'Sophie Bernard',
        'date_creation' => '2024-01-04',
        'statut' => 'publie',
        'tags' => ['Guide', 'Productivité', 'Organisation'],
        'vues' => 234,
        'image' => 'workspace.jpg'
    ],
    [
        'id' => 4,
        'titre' => 'Résultats du premier trimestre 2024',
        'category' => 'communiques',
        'contenu' => 'Nous sommes heureux de partager avec vous les excellents résultats du premier trimestre...',
        'auteur' => 'Pierre Durand',
        'date_creation' => '2024-01-07',
        'statut' => 'brouillon',
        'tags' => ['Résultats', 'Finance', 'Croissance'],
        'vues' => 0,
        'image' => 'results.jpg'
    ]
];

$pageTitle = "Articles";
ob_start();
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Colonne de gauche : Catégories et Filtres -->
        <div class="col-md-3">
            <!-- Catégories -->
            <div class="card mb-4">
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
                            <i class="fas fa-layer-group me-2"></i>
                            Tous les articles
                            <span class="badge bg-secondary float-end">
                                <?php echo count($articles); ?>
                            </span>
                        </a>
                        <?php foreach ($categories as $key => $category): ?>
                            <a href="#" class="list-group-item list-group-item-action" data-category="<?php echo $key; ?>">
                                <i class="<?php echo $category['icon']; ?> me-2" style="color: <?php echo $category['color']; ?>"></i>
                                <?php echo htmlspecialchars($category['name']); ?>
                                <span class="badge bg-secondary float-end">
                                    <?php 
                                        echo count(array_filter($articles, function($article) use ($key) {
                                            return $article['category'] === $key;
                                        }));
                                    ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Filtres</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">Tous les statuts</option>
                            <option value="publie">Publié</option>
                            <option value="brouillon">Brouillon</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Auteur</label>
                        <select class="form-select" id="filterAuthor">
                            <option value="">Tous les auteurs</option>
                            <?php 
                                $authors = array_unique(array_column($articles, 'auteur'));
                                foreach ($authors as $author):
                            ?>
                                <option value="<?php echo $author; ?>">
                                    <?php echo htmlspecialchars($author); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de publication</label>
                        <input type="date" class="form-control" id="filterDate">
                    </div>
                    <button class="btn btn-primary w-100" id="applyFilters">
                        Appliquer les filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Colonne centrale : Liste des articles -->
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
                                <input type="text" class="form-control" id="searchArticle" 
                                       placeholder="Rechercher un article...">
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary" id="toggleView">
                                    <i class="fas fa-th-list"></i>
                                </button>
                                <?php if (isAdmin()): ?>
                                <a href="ajouter.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Nouvel article
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des articles -->
            <div class="articles-grid" id="articlesList">
                <?php foreach ($articles as $article): ?>
                    <div class="article-item" 
                         data-category="<?php echo $article['category']; ?>"
                         data-status="<?php echo $article['statut']; ?>"
                         data-author="<?php echo $article['auteur']; ?>"
                         data-date="<?php echo $article['date_creation']; ?>">
                        <div class="card h-100">
                            <?php if (!empty($article['image'])): ?>
                            <img src="../../assets/images/articles/<?php echo htmlspecialchars($article['image']); ?>" 
                                 class="card-img-top article-image" 
                                 alt="<?php echo htmlspecialchars($article['titre']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h3 class="h5 mb-0">
                                        <?php echo htmlspecialchars($article['titre']); ?>
                                    </h3>
                                    <span class="badge bg-<?php echo $article['statut'] === 'publie' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($article['statut']); ?>
                                    </span>
                                </div>

                                <p class="card-text text-muted mb-3">
                                    <?php echo substr(htmlspecialchars($article['contenu']), 0, 150) . '...'; ?>
                                </p>

                                <!-- Métadonnées -->
                                <div class="d-flex align-items-center text-muted small mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-user me-1"></i>
                                        <?php echo htmlspecialchars($article['auteur']); ?>
                                    </div>
                                    <div class="me-3">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d/m/Y', strtotime($article['date_creation'])); ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-eye me-1"></i>
                                        <?php echo $article['vues']; ?> vues
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div class="mb-3">
                                    <?php foreach ($article['tags'] as $tag): ?>
                                        <span class="badge bg-light text-dark me-1">
                                            <?php echo htmlspecialchars($tag); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="voir.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>
                                        Lire la suite
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <div class="btn-group">
                                        <a href="modifier.php?id=<?php echo $article['id']; ?>" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.article-image {
    height: 200px;
    object-fit: cover;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1rem;
}

.articles-grid.list-view {
    grid-template-columns: 1fr;
}

.articles-grid.list-view .article-image {
    height: 150px;
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
    .articles-grid {
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
// Filtrage des articles
const searchInput = document.getElementById('searchArticle');
const articleItems = document.querySelectorAll('.article-item');
const categoryLinks = document.querySelectorAll('.list-group-item');
const statusSelect = document.getElementById('filterStatus');
const authorSelect = document.getElementById('filterAuthor');
const dateInput = document.getElementById('filterDate');
const applyFiltersBtn = document.getElementById('applyFilters');
const toggleViewBtn = document.getElementById('toggleView');
const articlesGrid = document.getElementById('articlesList');

let activeCategory = '';

function filterArticles() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedStatus = statusSelect.value;
    const selectedAuthor = authorSelect.value;
    const selectedDate = dateInput.value;

    articleItems.forEach(item => {
        const matchSearch = item.textContent.toLowerCase().includes(searchTerm);
        const matchCategory = !activeCategory || item.dataset.category === activeCategory;
        const matchStatus = !selectedStatus || item.dataset.status === selectedStatus;
        const matchAuthor = !selectedAuthor || item.dataset.author === selectedAuthor;
        const matchDate = !selectedDate || item.dataset.date === selectedDate;

        item.style.display = 
            matchSearch && matchCategory && matchStatus && 
            matchAuthor && matchDate ? '' : 'none';
    });
}

// Event listeners
searchInput.addEventListener('input', filterArticles);
applyFiltersBtn.addEventListener('click', filterArticles);

categoryLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        categoryLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        activeCategory = link.dataset.category;
        filterArticles();
    });
});

// Changement de vue
toggleViewBtn.addEventListener('click', () => {
    articlesGrid.classList.toggle('list-view');
    const icon = toggleViewBtn.querySelector('i');
    icon.className = articlesGrid.classList.contains('list-view') ? 
        'fas fa-th' : 'fas fa-th-list';
});
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
