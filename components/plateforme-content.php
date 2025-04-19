<?php
// Composant dynamique : plateforme (exemple)
$plateformes = [
    ['nom' => 'WebAll', 'type' => 'Intranet'],
    ['nom' => 'AlphaCloud', 'type' => 'Cloud'],
];
?>
<div class="plateforme-content">
    <h2>Plateformes</h2>
    <ul>
        <?php foreach($plateformes as $p): ?>
        <li><?= htmlspecialchars($p['nom']) ?> - <?= htmlspecialchars($p['type']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
