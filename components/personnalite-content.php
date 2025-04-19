<?php
// Composant dynamique : personnalité (exemple de liste)
$personnalites = [
    ['nom' => 'Jean Dupont', 'role' => 'Entrepreneur'],
    ['nom' => 'Sophie Martin', 'role' => 'Artiste'],
];
?>
<div class="personnalite-content">
    <h2>Personnalités</h2>
    <ul>
        <?php foreach($personnalites as $p): ?>
        <li><?= htmlspecialchars($p['nom']) ?> - <?= htmlspecialchars($p['role']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
