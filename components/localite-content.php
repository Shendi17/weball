<?php
// Composant dynamique : localité (exemple)
$localites = [
    ['nom' => 'Paris', 'type' => 'Ville'],
    ['nom' => 'Lyon', 'type' => 'Ville'],
];
?>
<div class="localite-content">
    <h2>Localités</h2>
    <ul>
        <?php foreach($localites as $l): ?>
        <li><?= htmlspecialchars($l['nom']) ?> - <?= htmlspecialchars($l['type']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
