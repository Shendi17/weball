<?php
if (!defined('BASE_PATH')) {
    require_once dirname(__DIR__) . '/config.php';
}
// Définition des sections et groupes pour le menu latéral
$sidebar_groups = [
    'CLASSEUR' => [
        ['icon' => 'fa-star', 'label' => 'Personnalité', 'slug' => 'personnalite'],
        ['icon' => 'fa-shield-alt', 'label' => 'Autorité', 'slug' => 'autorite'],
        ['icon' => 'fa-address-book', 'label' => 'ANNUAIRE', 'slug' => 'annuaire'],
        ['icon' => 'fa-building', 'label' => 'Entité', 'slug' => 'entite'],
        ['icon' => 'fa-map-marker-alt', 'label' => 'Localité', 'slug' => 'localite'],
    ],
    'COLLECTION' => [
        ['icon' => 'fa-music', 'label' => 'Instrument', 'slug' => 'instrument'],
        ['icon' => 'fa-briefcase', 'label' => 'Office', 'slug' => 'office'],
        ['icon' => 'fa-layer-group', 'label' => 'PLATEFORME', 'slug' => 'plateforme'],
        ['icon' => 'fa-university', 'label' => 'Banque', 'slug' => 'banque'],
        ['icon' => 'fa-network-wired', 'label' => 'Réseau', 'slug' => 'reseau'],
    ],
    'REGISTRE' => [
        ['icon' => 'fa-newspaper', 'label' => 'Publication', 'slug' => 'publication', 'badge' => 'Hot'],
        ['icon' => 'fa-archive', 'label' => 'Archive', 'slug' => 'archive', 'badge' => 'New'],
        ['icon' => 'fa-school', 'label' => 'ECOLE', 'slug' => 'ecole', 'badge' => 'New'],
        ['icon' => 'fa-photo-video', 'label' => 'Média', 'slug' => 'media'],
        ['icon' => 'fa-book-open', 'label' => 'Formation', 'slug' => 'formation'],
    ],
    'SELECTION' => [
        ['icon' => 'fa-bullhorn', 'label' => 'Annonce', 'slug' => 'annonce'],
        ['icon' => 'fa-th-list', 'label' => 'Catalogue', 'slug' => 'catalogue'],
        ['icon' => 'fa-store', 'label' => 'MARCHE', 'slug' => 'marche'],
        ['icon' => 'fa-shopping-bag', 'label' => 'Boutique', 'slug' => 'boutique'],
        ['icon' => 'fa-id-card', 'label' => 'Adhésion', 'slug' => 'adhesion', 'badge' => 'New', 'badge_color' => 'pink'],
    ],
    'GUIDE' => [
        ['icon' => 'fa-project-diagram', 'label' => 'Projet', 'slug' => 'projet'],
        ['icon' => 'fa-rocket', 'label' => 'Carrière', 'slug' => 'carriere'],
        ['icon' => 'fa-gavel', 'label' => 'Discipline', 'slug' => 'discipline'],
        ['icon' => 'fa-trophy', 'label' => 'Concours', 'slug' => 'concours'],
        ['icon' => 'fa-bullseye', 'label' => 'Campagne', 'slug' => 'campagne'],
    ],
];
$sidebar_top = [
    ['icon' => 'fa-cogs', 'label' => 'Cadran', 'slug' => 'cadran'],
    ['icon' => 'fa-book', 'label' => 'Journal', 'slug' => 'journal'],
    ['icon' => 'fa-desktop', 'label' => 'Ecran', 'slug' => 'ecran', 'badge' => 'New'],
];
function sidebar_is_active($slug) {
    $uri = $_SERVER['REQUEST_URI'];
    // On considère actif si l'URL contient /slug/ ou /slug.php ou /slug/index.php
    return (
        preg_match('#/' . preg_quote($slug, '#') . '(/|\.php|/index\.php)?#i', $uri)
    );
}
function sidebar_link($slug) {
    // Génère toujours /slug.php si le fichier existe, sinon /resources/modules/slug/index.php
    $slug = basename($slug); // Sécurité : on force le slug simple
    $rootFile = $_SERVER['DOCUMENT_ROOT'] . '/weball/' . $slug . '.php';
    if (file_exists($rootFile)) {
        return getFullUrl('/' . $slug . '.php');
    } else {
        return getFullUrl('/resources/modules/' . $slug . '/index.php');
    }
}
?>
<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <span class="site-title">WebAll</span>
            <span class="site-desc">La qualité est connectée</span>
        </div>
        <?php if (isset($_SESSION['username'])): ?>
            <div class="sidebar-user">
                <span>Connecté : <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="<?= getFullUrl('/logout.php') ?>" class="btn btn-sm btn-outline-danger mt-2">Déconnexion</a>
            </div>
        <?php endif; ?>
        <ul class="nav flex-column">
            <li class="nav-item cart-row">
                <a href="<?php echo getFullUrl('/panier'); ?>" class="nav-link"><i class="fas fa-shopping-cart"></i> <span>c0</span> <span class="cart-badge"></span></a>
            </li>
            <?php foreach ($sidebar_top as $item): ?>
                <li class="nav-item">
                    <a href="<?php echo sidebar_link($item['slug']); ?>" class="nav-link<?php echo sidebar_is_active($item['slug']) ? ' active' : ''; ?>">
                        <i class="fas <?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
                        <?php if (isset($item['badge'])): ?>
                            <span class="badge badge-sidebar bg-<?php echo $item['badge_color'] ?? ($item['badge']==='Hot'?'success':($item['badge']==='New'?'primary':'info')); ?> ms-2"><?php echo $item['badge']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php foreach ($sidebar_groups as $group => $items): ?>
                <li class="nav-item sidebar-group"><span><?php echo $group; ?></span></li>
                <?php foreach ($items as $item): ?>
                    <li class="nav-item">
                        <a href="<?php echo sidebar_link($item['slug']); ?>" class="nav-link<?php echo sidebar_is_active($item['slug']) ? ' active' : ''; ?>">
                            <i class="fas <?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
                            <?php if (isset($item['badge'])): ?>
                                <span class="badge badge-sidebar bg-<?php echo $item['badge_color'] ?? ($item['badge']==='Hot'?'success':($item['badge']==='New'?'primary':'info')); ?> ms-2"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
