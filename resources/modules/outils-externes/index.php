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

// Liste des outils externes
$tools = [
    [
        'id' => 'chatgpt',
        'name' => 'ChatGPT',
        'description' => 'Assistant IA développé par OpenAI pour la génération de texte et l\'aide à la programmation',
        'icon' => 'fas fa-robot',
        'color' => '#10a37f',
        'url' => 'https://chat.openai.com',
        'category' => 'ia',
        'tags' => ['ia', 'assistant', 'programmation']
    ],
    [
        'id' => 'github',
        'name' => 'GitHub',
        'description' => 'Plateforme de développement collaboratif et de gestion de versions',
        'icon' => 'fab fa-github',
        'color' => '#24292e',
        'url' => 'https://github.com',
        'category' => 'dev',
        'tags' => ['git', 'code', 'collaboration']
    ],
    [
        'id' => 'stackoverflow',
        'name' => 'Stack Overflow',
        'description' => 'Communauté de développeurs pour poser et répondre aux questions de programmation',
        'icon' => 'fab fa-stack-overflow',
        'color' => '#f48024',
        'url' => 'https://stackoverflow.com',
        'category' => 'dev',
        'tags' => ['questions', 'réponses', 'programmation']
    ],
    [
        'id' => 'codepen',
        'name' => 'CodePen',
        'description' => 'Éditeur de code en ligne pour tester et partager du HTML, CSS et JavaScript',
        'icon' => 'fab fa-codepen',
        'color' => '#47cf73',
        'url' => 'https://codepen.io',
        'category' => 'dev',
        'tags' => ['frontend', 'code', 'playground']
    ],
    [
        'id' => 'figma',
        'name' => 'Figma',
        'description' => 'Outil de conception d\'interface et de prototypage collaboratif',
        'icon' => 'fab fa-figma',
        'color' => '#f24e1e',
        'url' => 'https://www.figma.com',
        'category' => 'design',
        'tags' => ['design', 'ui', 'ux']
    ],
    [
        'id' => 'notion',
        'name' => 'Notion',
        'description' => 'Espace de travail tout-en-un pour la prise de notes et la gestion de projets',
        'icon' => 'fas fa-book',
        'color' => '#000000',
        'url' => 'https://www.notion.so',
        'category' => 'productivite',
        'tags' => ['notes', 'organisation', 'collaboration']
    ],
    [
        'id' => 'trello',
        'name' => 'Trello',
        'description' => 'Outil de gestion de projet basé sur la méthode Kanban',
        'icon' => 'fab fa-trello',
        'color' => '#0079bf',
        'url' => 'https://trello.com',
        'category' => 'productivite',
        'tags' => ['kanban', 'projet', 'organisation']
    ],
    [
        'id' => 'canva',
        'name' => 'Canva',
        'description' => 'Plateforme de conception graphique en ligne',
        'icon' => 'fas fa-palette',
        'color' => '#00c4cc',
        'url' => 'https://www.canva.com',
        'category' => 'design',
        'tags' => ['design', 'graphisme', 'présentation']
    ]
];

// Catégories d'outils
$categories = [
    'ia' => [
        'name' => 'Intelligence Artificielle',
        'icon' => 'fas fa-brain',
        'color' => '#10a37f'
    ],
    'dev' => [
        'name' => 'Développement',
        'icon' => 'fas fa-code',
        'color' => '#24292e'
    ],
    'design' => [
        'name' => 'Design',
        'icon' => 'fas fa-paint-brush',
        'color' => '#f24e1e'
    ],
    'productivite' => [
        'name' => 'Productivité',
        'icon' => 'fas fa-tasks',
        'color' => '#0079bf'
    ]
];

$pageTitle = "Outils externes";
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Outils externes</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="toggleView">
                <i class="fas fa-th-large"></i> Changer la vue
            </button>
            <?php if (isAdmin()): ?>
            <a href="settings.php" class="btn btn-outline-secondary">
                <i class="fas fa-cog"></i> Paramètres
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="searchTool" 
                       placeholder="Rechercher un outil...">
            </div>
        </div>
        <div class="col-md-6">
            <select class="form-select" id="filterCategory">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $key => $category): ?>
                    <option value="<?php echo $key; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Filtres par tags -->
    <div class="mb-4 tags-container">
        <?php
        $all_tags = [];
        foreach ($tools as $tool) {
            $all_tags = array_merge($all_tags, $tool['tags']);
        }
        $unique_tags = array_unique($all_tags);
        sort($unique_tags);
        ?>
        <div class="tags-scroll">
            <?php foreach ($unique_tags as $tag): ?>
                <button class="btn btn-outline-secondary btn-sm tag-filter" data-tag="<?php echo $tag; ?>">
                    #<?php echo htmlspecialchars($tag); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="cards-grid" id="toolsGrid">
        <?php foreach ($tools as $tool): ?>
            <div class="card tool-card" 
                 data-category="<?php echo $tool['category']; ?>"
                 data-tags="<?php echo implode(',', $tool['tags']); ?>">
                <div class="card-body">
                    <div class="tool-icon" style="background-color: <?php echo $tool['color']; ?>">
                        <i class="<?php echo $tool['icon']; ?>"></i>
                    </div>
                    <h5 class="card-title mt-3">
                        <?php echo htmlspecialchars($tool['name']); ?>
                    </h5>
                    <p class="card-text text-muted">
                        <?php echo htmlspecialchars($tool['description']); ?>
                    </p>
                    <div class="tool-tags mb-3">
                        <?php foreach ($tool['tags'] as $tag): ?>
                            <span class="badge bg-light text-dark">
                                #<?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3">
                        <a href="<?php echo $tool['url']; ?>" class="btn btn-primary w-100" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Ouvrir l'outil
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Filtrage des outils
const searchInput = document.getElementById('searchTool');
const categorySelect = document.getElementById('filterCategory');
const toolCards = document.querySelectorAll('.tool-card');
const toggleViewBtn = document.getElementById('toggleView');
const toolsGrid = document.getElementById('toolsGrid');
const tagButtons = document.querySelectorAll('.tag-filter');

let activeTag = null;

function filterTools() {
    const searchTerm = searchInput.value.toLowerCase();
    const categoryValue = categorySelect.value;

    toolCards.forEach(card => {
        const cardText = card.textContent.toLowerCase();
        const cardCategory = card.dataset.category;
        const cardTags = card.dataset.tags.split(',');
        
        const matchSearch = cardText.includes(searchTerm);
        const matchCategory = !categoryValue || cardCategory === categoryValue;
        const matchTag = !activeTag || cardTags.includes(activeTag);

        card.style.display = matchSearch && matchCategory && matchTag ? '' : 'none';
    });
}

// Changement de vue (grille/liste)
function toggleView() {
    toolsGrid.classList.toggle('list-view');
    const icon = toggleViewBtn.querySelector('i');
    if (toolsGrid.classList.contains('list-view')) {
        icon.className = 'fas fa-th-large';
    } else {
        icon.className = 'fas fa-list';
    }
    localStorage.setItem('toolsView', toolsGrid.classList.contains('list-view') ? 'list' : 'grid');
}

// Gestion des tags
tagButtons.forEach(button => {
    button.addEventListener('click', () => {
        const tag = button.dataset.tag;
        
        // Toggle active state
        if (activeTag === tag) {
            activeTag = null;
            button.classList.remove('active');
        } else {
            tagButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            activeTag = tag;
        }
        
        filterTools();
    });
});

// Event listeners
searchInput.addEventListener('input', debounce(filterTools, 300));
categorySelect.addEventListener('change', filterTools);
toggleViewBtn.addEventListener('click', toggleView);

// Restaurer la vue précédente
document.addEventListener('DOMContentLoaded', () => {
    const savedView = localStorage.getItem('toolsView');
    if (savedView === 'list') {
        toggleView();
    }
});
</script>

<style>
.tags-container {
    position: relative;
    overflow: hidden;
}

.tags-scroll {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding: 0.5rem 0;
    -webkit-overflow-scrolling: touch;
}

.tag-filter {
    white-space: nowrap;
}

.tag-filter.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.tool-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tool-tags .badge {
    font-size: 0.75rem;
    font-weight: normal;
}

/* Scrollbar styles */
.tags-scroll::-webkit-scrollbar {
    height: 4px;
}

.tags-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.tags-scroll::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

.tags-scroll::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
