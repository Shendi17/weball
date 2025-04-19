<?php
$pageTitle = "Annuaire - WebAllOne";
$pageContent = '
<h1>Annuaire</h1>
<div class="directory-container">
    <div class="search-filters">
        <input type="text" placeholder="Rechercher dans l\'annuaire..." class="search-input">
        <div class="filters">
            <select class="filter-select">
                <option value="">Département</option>
                <option value="it">IT</option>
                <option value="rh">RH</option>
                <option value="marketing">Marketing</option>
            </select>
            <select class="filter-select">
                <option value="">Fonction</option>
                <option value="manager">Manager</option>
                <option value="developer">Développeur</option>
                <option value="designer">Designer</option>
            </select>
        </div>
    </div>
    
    <div class="directory-grid">
        <!-- Exemple de cartes de contact -->
        <div class="contact-card">
            <div class="contact-header">
                <i class="fas fa-user-circle"></i>
                <h3>Jean Dupont</h3>
                <span class="department">IT</span>
            </div>
            <div class="contact-info">
                <p><i class="fas fa-briefcase"></i> Développeur Senior</p>
                <p><i class="fas fa-envelope"></i> jean.dupont@example.com</p>
                <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
            </div>
        </div>
        
        <div class="contact-card">
            <div class="contact-header">
                <i class="fas fa-user-circle"></i>
                <h3>Marie Martin</h3>
                <span class="department">Marketing</span>
            </div>
            <div class="contact-info">
                <p><i class="fas fa-briefcase"></i> Chef de Projet</p>
                <p><i class="fas fa-envelope"></i> marie.martin@example.com</p>
                <p><i class="fas fa-phone"></i> +33 1 23 45 67 90</p>
            </div>
        </div>
    </div>
</div>
';

require_once __DIR__ . '/page_template.php';
?>
