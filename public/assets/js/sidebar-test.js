/**
 * Script de gestion de la sidebar WebAll
 */

document.addEventListener('DOMContentLoaded', function() {
    // Gestion du toggle de la sidebar
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarToggleFloat = document.getElementById('sidebar-toggle-float');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
    
    if (sidebarToggleFloat && sidebar) {
        sidebarToggleFloat.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Marquer le lien actif dans la sidebar
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    
    navLinks.forEach(function(link) {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href.split('/').pop())) {
            link.classList.add('active');
        }
    });
});
