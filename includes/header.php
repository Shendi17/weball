<?php
// Déterminer le chemin racine
$root_path = dirname($_SERVER['SCRIPT_FILENAME']);
$root_path = str_replace('\\', '/', $root_path);
$root_path = str_replace('/includes', '', $root_path);

// S'assurer que BASE_PATH est défini
require_once dirname(__DIR__) . '/config.php';
$base_path = BASE_PATH;

// Inclure le head HTML
require_once 'head.php';
?>

<!-- Header modulaire avec barre de recherche globale et boutons d'action -->
<header class="main-header">
    <button id="sidebar-toggle" class="btn btn-link sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <form class="header-search" method="get" action="<?php echo getFullUrl('/recherche'); ?>">
        <input type="text" name="q" placeholder="Search everything..." autocomplete="off" />
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <div class="header-actions">
        <?php if (isset($_SESSION['user'])): ?>
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="<?php echo getFullUrl('/profile'); ?>">Profil</a></li>
                    <li><a class="dropdown-item" href="<?php echo getFullUrl('/settings'); ?>">Paramètres</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo getFullUrl('/logout'); ?>">Déconnexion</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a href="<?php echo getFullUrl('/login'); ?>" class="btn btn-outline">Se connecter</a>
            <a href="<?php echo getFullUrl('/register'); ?>" class="btn btn-primary">S'enregistrer</a>
        <?php endif; ?>
    </div>
</header>
