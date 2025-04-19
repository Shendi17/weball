<?php
// Composant dynamique : autorité (exemple)
$autorites = [
    ['nom' => 'Préfecture Paris', 'type' => 'Préfecture'],
    ['nom' => 'Police Lyon', 'type' => 'Police'],
];
?>
<div class="autorite-content">
    <h2>Autorités</h2>
    <ul>
        <?php foreach($autorites as $a): ?>
        <li><?= htmlspecialchars($a['nom']) ?> - <?= htmlspecialchars($a['type']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
