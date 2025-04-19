<?php
// Composant dynamique : réseau (exemple)
$reseaux = [
    ['nom' => 'Réseau Pro', 'type' => 'Professionnel'],
    ['nom' => 'Communauté Alpha', 'type' => 'Social'],
];
?>
<div class="reseau-content">
    <h2>Réseaux</h2>
    <ul>
        <?php foreach($reseaux as $r): ?>
        <li><?= htmlspecialchars($r['nom']) ?> - <?= htmlspecialchars($r['type']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
