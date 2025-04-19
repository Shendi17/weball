<?php
// Composant modulaire : liste dynamique pour l'annuaire
if (!isset($users)) {
    require_once __DIR__ . '/../config.db.php';
    try {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT full_name AS nom, 'Utilisateur' AS type, '' AS localite, '' AS icon FROM users WHERE is_active = 1 ORDER BY full_name");
        $users = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Erreur lors de la récupération des utilisateurs : ' . htmlspecialchars($e->getMessage()) . '</div>';
        $users = [];
    }
}
?>
<div class="annuaire-list">
    <h2 class="annuaire-title">Annuaire</h2>
    <ul class="annuaire-entries">
        <?php foreach ($users as $entry): ?>
            <li class="annuaire-entry">
                <i class="fas <?= htmlspecialchars($entry['icon'] ?? 'fa-user') ?>"></i>
                <span class="entry-nom"><?= htmlspecialchars($entry['nom'] ?? '') ?></span>
                <?php if (!empty($entry['type'] ?? '')): ?>
                    <span class="entry-type">(<?= htmlspecialchars($entry['type']) ?>)</span>
                <?php endif; ?>
                <?php if (!empty($entry['localite'] ?? '')): ?>
                    <span class="entry-localite">- <?= htmlspecialchars($entry['localite']) ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
