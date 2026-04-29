<?php /* Sidebar */ ?>
<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <span class="site-title">WebAll</span>
            <span class="site-desc">La qualité est essentielle</span>
            <button id="sidebar-toggle" class="sidebar-toggle" title="Afficher/Masquer la barre latérale">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="nav flex-column">
            <li class="nav-group">
                <span class="nav-group-title">CLASSEUR</span>
                <ul class="nav-group-list">
                    <li class="nav-item"><a href="/pages/personnalite.php" class="nav-link"><i class="fas fa-star"></i><span>Personnalité</span></a></li>
                    <li class="nav-item"><a href="/pages/autorite.php" class="nav-link"><i class="fas fa-shield-alt"></i><span>Autorité</span></a></li>
                    <li class="nav-item"><a href="/pages/annuaire.php" class="nav-link"><i class="fas fa-address-book"></i><span>ANNUAIRE</span></a></li>
                    <li class="nav-item"><a href="/pages/entite.php" class="nav-link"><i class="fas fa-building"></i><span>Entité</span></a></li>
                    <li class="nav-item"><a href="/pages/localite.php" class="nav-link"><i class="fas fa-map-marker-alt"></i><span>Localité</span></a></li>
                </ul>
            </li>
            <!-- Autres groupes/catégories ici (COLLECTION, REGISTRE, ...) -->
        </ul>
        <button id="sidebar-toggle-float" class="sidebar-toggle sidebar-toggle-float" title="Afficher la barre latérale">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</nav>
