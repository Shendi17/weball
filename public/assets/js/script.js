// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du toggle sidebar
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }

    // Gestion du menu utilisateur
    const userMenuBtn = document.querySelector('.user-menu-btn');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        // Fermer le menu lors d'un clic à l'extérieur
        document.addEventListener('click', function(e) {
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }

    // User menu functionality
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.querySelector('.user-dropdown');
    let timeoutId;

    userMenu.addEventListener('mouseenter', function() {
        clearTimeout(timeoutId);
        dropdown.style.display = 'block';
    });

    userMenu.addEventListener('mouseleave', function() {
        timeoutId = setTimeout(() => {
            dropdown.style.display = 'none';
        }, 200); // 200ms delay before closing
    });
});
