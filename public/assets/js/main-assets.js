// Sidebar moderne WebAll : toggle + persistance localStorage
(function() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const toggleFloatBtn = document.getElementById('sidebar-toggle-float');
    if (!sidebar) return;
    // Appliquer l'état au chargement (ouvert/fermé selon localStorage)
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    } else {
        sidebar.classList.remove('collapsed');
    }
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }
    if (toggleFloatBtn) {
        toggleFloatBtn.addEventListener('click', function() {
            sidebar.classList.remove('collapsed');
            localStorage.setItem('sidebarCollapsed', false);
        });
    }

    // --- Suppression du comportement : fermeture auto de la sidebar au clic sur un lien ---
    // (aucune action ici)

})();

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

// Gestion des sous-menus
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.has-submenu');
    // ... (reste du code si besoin)

    // --- Sidebar Hover Logic ---
    (function() {
        let hoverEnabled = true;

        // Gestion du hover uniquement si la sidebar est rétractée
        if (document.querySelector('.sidebar')) {
            document.querySelector('.sidebar').addEventListener('mouseenter', function() {
                if (document.querySelector('.sidebar').classList.contains('collapsed') && hoverEnabled) {
                    document.querySelector('.sidebar').classList.add('sidebar-hover');
                    if(document.querySelector('.main-content')) document.querySelector('.main-content').classList.add('sidebar-hovering');
                }
            });
            document.querySelector('.sidebar').addEventListener('mouseleave', function() {
                if (document.querySelector('.sidebar').classList.contains('collapsed') && hoverEnabled) {
                    document.querySelector('.sidebar').classList.remove('sidebar-hover');
                    if(document.querySelector('.main-content')) document.querySelector('.main-content').classList.remove('sidebar-hovering');
                }
            });
        }
        // Désactive le hover si la sidebar est ouverte (toggle)
        if (document.getElementById('sidebar-toggle') || document.querySelector('.sidebar-toggle')) {
            (document.getElementById('sidebar-toggle') || document.querySelector('.sidebar-toggle')).addEventListener('click', function() {
                const isCollapsed = document.querySelector('.sidebar').classList.contains('collapsed');
                hoverEnabled = isCollapsed;
                if (!isCollapsed) {
                    document.querySelector('.sidebar').classList.remove('sidebar-hover');
                    if(document.querySelector('.main-content')) document.querySelector('.main-content').classList.remove('sidebar-hovering');
                }
            });
        }
    })();
});
