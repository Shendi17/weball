/**
 * Fonction utilitaire pour limiter la fréquence d'exécution d'une fonction
 * @param {Function} func - La fonction à debouncer
 * @param {number} wait - Le délai d'attente en millisecondes
 * @returns {Function} - La fonction debouncée
 */
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
