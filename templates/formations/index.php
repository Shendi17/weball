<?php
// Titre de la page
$pageTitle = 'Formations';
?>

<div class="formations-page">
    <div class="row">
        <!-- Colonne des filtres -->
        <div class="col-md-3">
            <div class="filters">
                <h2>Recherche et filtres</h2>
                
                <!-- Barre de recherche -->
                <div class="filter-group">
                    <input type="text" placeholder="Rechercher..." class="form-control">
                </div>

                <!-- Niveau -->
                <div class="filter-group">
                    <label>Niveau</label>
                    <select class="form-control">
                        <option value="">Tous</option>
                        <option value="debutant">Débutant</option>
                        <option value="intermediaire">Intermédiaire</option>
                        <option value="avance">Avancé</option>
                    </select>
                </div>

                <!-- Durée -->
                <div class="filter-group">
                    <label>Durée</label>
                    <select class="form-control">
                        <option value="">Toutes</option>
                        <option value="court">Court (< 2h)</option>
                        <option value="moyen">Moyen (2-5h)</option>
                        <option value="long">Long (> 5h)</option>
                    </select>
                </div>

                <!-- Catégorie -->
                <div class="filter-group">
                    <label>Catégorie</label>
                    <select class="form-control">
                        <option value="">Toutes</option>
                        <option value="web">Développement Web</option>
                        <option value="mobile">Développement Mobile</option>
                        <option value="database">Base de données</option>
                        <option value="security">Sécurité</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Colonne principale des formations -->
        <div class="col-md-9">
            <h1>Formations</h1>
            
            <div class="formations-grid">
                <!-- Formation PHP -->
                <div class="formation-card">
                    <h3>Développement PHP Avancé</h3>
                    <p>Formation approfondie sur le développement PHP moderne et ses frameworks</p>
                    <div class="badges">
                        <span class="badge badge-undefined">Non défini</span>
                        <span class="badge badge-undefined">Non définie</span>
                    </div>
                    <a href="#" class="btn-details">Détails</a>
                </div>

                <!-- Formation JavaScript -->
                <div class="formation-card">
                    <h3>JavaScript pour Développeurs</h3>
                    <p>Maîtrisez le développement front-end avec JavaScript et ses frameworks modernes</p>
                    <div class="badges">
                        <span class="badge badge-undefined">Non défini</span>
                        <span class="badge badge-undefined">Non définie</span>
                    </div>
                    <a href="#" class="btn-details">Détails</a>
                </div>

                <!-- Formation Full-Stack -->
                <div class="formation-card">
                    <h3>Développement Web Full-Stack</h3>
                    <p>Formation complète en développement web : PHP, JavaScript, HTML, CSS</p>
                    <div class="badges">
                        <span class="badge badge-undefined">Non défini</span>
                        <span class="badge badge-undefined">Non définie</span>
                    </div>
                    <a href="#" class="btn-details">Détails</a>
                </div>

                <!-- Formation SQL -->
                <div class="formation-card">
                    <h3>SQL et Base de Données</h3>
                    <p>Apprenez à développer et gérer vos bases de données efficacement</p>
                    <div class="badges">
                        <span class="badge badge-undefined">Non défini</span>
                        <span class="badge badge-undefined">Non définie</span>
                    </div>
                    <a href="#" class="btn-details">Détails</a>
                </div>

                <!-- Formation Sécurité -->
                <div class="formation-card">
                    <h3>Sécurité Web</h3>
                    <p>Protection et sécurisation des applications web pour les développeurs</p>
                    <div class="badges">
                        <span class="badge badge-undefined">Non défini</span>
                        <span class="badge badge-undefined">Non définie</span>
                    </div>
                    <a href="#" class="btn-details">Détails</a>
                </div>
            </div>
        </div>
    </div>
</div>
