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

// Liste des outils internes
$tools = [
    [
        'id' => 'weather',
        'name' => 'Météo en temps réel',
        'description' => 'Consultez la météo en temps réel pour n\'importe quelle ville',
        'icon' => 'fas fa-cloud-sun',
        'color' => '#4299e1',
        'url' => 'weather.php',
        'category' => 'utilitaires',
        'status' => 'active'
    ],
    [
        'id' => 'currency',
        'name' => 'Convertisseur de devises',
        'description' => 'Convertissez facilement des montants entre différentes devises',
        'icon' => 'fas fa-exchange-alt',
        'color' => '#48bb78',
        'url' => 'currency.php',
        'category' => 'utilitaires',
        'status' => 'active'
    ],
    [
        'id' => 'qrcode',
        'name' => 'Générateur de QR Code',
        'description' => 'Créez des QR codes personnalisés pour vos liens et textes',
        'icon' => 'fas fa-qrcode',
        'color' => '#805ad5',
        'url' => 'qrcode.php',
        'category' => 'utilitaires',
        'status' => 'active'
    ],
    [
        'id' => 'calculator',
        'name' => 'Calculatrice avancée',
        'description' => 'Effectuez des calculs complexes avec cette calculatrice scientifique',
        'icon' => 'fas fa-calculator',
        'color' => '#ed8936',
        'url' => 'calculator.php',
        'category' => 'utilitaires',
        'status' => 'active'
    ],
    [
        'id' => 'translate',
        'name' => 'Traduction instantanée',
        'description' => 'Traduisez du texte dans plus de 100 langues',
        'icon' => 'fas fa-language',
        'color' => '#667eea',
        'url' => 'translate.php',
        'category' => 'communication',
        'status' => 'active'
    ],
    [
        'id' => 'notes',
        'name' => 'Notes rapides',
        'description' => 'Prenez des notes rapidement avec formatage Markdown',
        'icon' => 'fas fa-sticky-note',
        'color' => '#f6ad55',
        'url' => 'notes.php',
        'category' => 'productivite',
        'status' => 'active'
    ],
    [
        'id' => 'calendar',
        'name' => 'Calendrier',
        'description' => 'Gérez vos événements et rendez-vous',
        'icon' => 'fas fa-calendar-alt',
        'color' => '#4c51bf',
        'url' => 'calendar.php',
        'category' => 'productivite',
        'status' => 'active'
    ],
    [
        'id' => 'tasks',
        'name' => 'Gestionnaire de tâches',
        'description' => 'Organisez et suivez vos tâches quotidiennes',
        'icon' => 'fas fa-tasks',
        'color' => '#2d3748',
        'url' => 'tasks.php',
        'category' => 'productivite',
        'status' => 'active'
    ],
    [
        'id' => 'editor',
        'name' => 'Éditeur de texte',
        'description' => 'Éditeur de texte avec support Markdown',
        'icon' => 'fas fa-edit',
        'color' => '#718096',
        'url' => 'editor.php',
        'category' => 'productivite',
        'status' => 'active'
    ]
];

// Catégories des outils
$categories = [
    'utilitaires' => [
        'name' => 'Utilitaires',
        'icon' => 'fas fa-tools',
        'description' => 'Outils pratiques pour vos besoins quotidiens'
    ],
    'productivite' => [
        'name' => 'Productivité',
        'icon' => 'fas fa-clock',
        'description' => 'Améliorez votre efficacité au travail'
    ],
    'communication' => [
        'name' => 'Communication',
        'icon' => 'fas fa-comments',
        'description' => 'Outils pour communiquer efficacement'
    ]
];

$pageTitle = "Outils";
ob_start();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Outils</h1>
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

    <!-- Barre de recherche et filtres -->
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

    <!-- Catégories -->
    <?php foreach ($categories as $categoryKey => $category): ?>
        <div class="category-section mb-5" data-category="<?php echo $categoryKey; ?>">
            <div class="category-header mb-3">
                <h2>
                    <i class="<?php echo $category['icon']; ?> me-2"></i>
                    <?php echo htmlspecialchars($category['name']); ?>
                </h2>
                <p class="text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php 
                $categoryTools = array_filter($tools, function($tool) use ($categoryKey) {
                    return $tool['category'] === $categoryKey && $tool['status'] === 'active';
                });
                
                foreach ($categoryTools as $tool): 
                ?>
                    <div class="col">
                        <div class="card h-100 tool-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="tool-icon me-3" style="background-color: <?php echo $tool['color']; ?>">
                                        <i class="<?php echo $tool['icon']; ?>"></i>
                                    </div>
                                    <h5 class="card-title mb-0">
                                        <?php echo htmlspecialchars($tool['name']); ?>
                                    </h5>
                                </div>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($tool['description']); ?>
                                </p>
                                <a href="<?php echo $tool['url']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Ouvrir l'outil
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Filtrage des outils
const searchInput = document.getElementById('searchTool');
const categorySelect = document.getElementById('filterCategory');
const toolCards = document.querySelectorAll('.tool-card');
const categorySections = document.querySelectorAll('.category-section');
const toggleViewBtn = document.getElementById('toggleView');

function filterTools() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedCategory = categorySelect.value;

    categorySections.forEach(section => {
        const sectionCategory = section.dataset.category;
        const cards = section.querySelectorAll('.tool-card');
        let visibleCards = 0;

        cards.forEach(card => {
            const cardText = card.textContent.toLowerCase();
            const matchSearch = cardText.includes(searchTerm);
            const matchCategory = !selectedCategory || sectionCategory === selectedCategory;

            if (matchSearch && matchCategory) {
                card.closest('.col').style.display = '';
                visibleCards++;
            } else {
                card.closest('.col').style.display = 'none';
            }
        });

        // Afficher/masquer la section en fonction des cartes visibles
        section.style.display = visibleCards > 0 ? '' : 'none';
    });
}

// Changement de vue (grille/liste)
function toggleView() {
    const toolsContainer = document.querySelector('.container');
    toolsContainer.classList.toggle('list-view');
    
    const icon = toggleViewBtn.querySelector('i');
    if (toolsContainer.classList.contains('list-view')) {
        icon.className = 'fas fa-th-large';
    } else {
        icon.className = 'fas fa-list';
    }
    
    localStorage.setItem('toolsView', toolsContainer.classList.contains('list-view') ? 'list' : 'grid');
}

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
.tool-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tool-icon i {
    font-size: 1.5rem;
    color: white;
}

.tool-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    background: #fff;
    border-radius: 1rem;
    overflow: hidden;
}

.tool-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.category-header h2 {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.category-header p {
    font-size: 0.95rem;
}

/* Vue en liste */
.list-view .row {
    flex-direction: column;
}

.list-view .col {
    width: 100%;
    max-width: 100%;
}

.list-view .tool-card {
    margin-bottom: 1rem;
}

.list-view .card-body {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.list-view .tool-icon {
    margin: 0;
}

.list-view .card-title {
    margin: 0;
}

.list-view .card-text {
    flex: 1;
    margin: 0 1rem;
}

.list-view .btn {
    width: auto !important;
}

@media (max-width: 768px) {
    .list-view .card-body {
        flex-direction: column;
        text-align: center;
    }

    .list-view .card-text {
        margin: 1rem 0;
    }

    .list-view .btn {
        width: 100% !important;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 3) . '/includes/template.php';
?>
