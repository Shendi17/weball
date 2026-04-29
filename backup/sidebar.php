<?php
// Backup de l'ancienne sidebar
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
    ],
];
?>
<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <button id="sidebar-toggle" class="sidebar-toggle" title="Afficher/Masquer la barre latérale">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="nav flex-column">
            <?php foreach ($sidebar_groups as $group => $items): ?>
                <li class="nav-group">
                    <span class="nav-group-title"><?= htmlspecialchars($group) ?></span>
                    <ul class="nav-group-list">
                        <?php foreach ($items as $item): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/<?= htmlspecialchars($item['slug']) ?>">
                                    <i class="fas <?= htmlspecialchars($item['icon']) ?>"></i>
                                    <span><?= htmlspecialchars($item['label']) ?></span>
                                    <?php if (!empty($item['badge'])): ?>
                                        <span class="badge"<?= !empty($item['badge_color']) ? ' style="background:' . htmlspecialchars($item['badge_color']) . ';"' : '' ?>><?= htmlspecialchars($item['badge']) ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
