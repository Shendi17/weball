// Fonctions utilitaires communes
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Gestion du menu latéral
// (Amélioration pour compatibilité responsive et sidebar-toggle)
document.addEventListener('DOMContentLoaded', function() {
    // Toggle du menu latéral
    const sidebarToggle = document.getElementById('sidebar-toggle') || document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.getElementById('main-content') || document.querySelector('.main-content');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            if(window.innerWidth < 900) {
                sidebar.classList.toggle('open');
            }
            if(mainContent) mainContent.classList.toggle('expanded');
            // Sauvegarder l'état dans le localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        // Restaurer l'état du menu au chargement
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            if(mainContent) mainContent.classList.add('expanded');
        }
    }
    // Gestion des sous-menus
    const menuItems = document.querySelectorAll('.has-submenu');
    // ... (reste du code si besoin)
});
