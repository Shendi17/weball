// Script pour forcer le mode sidebar rétracté pour debug
window.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.add('collapsed');
    }
});
