<?php
// Composant dynamique : entité (exemple)
$entites = [
    ['nom' => 'Société Alpha', 'ville' => 'Paris'],
    ['nom' => 'Association Beta', 'ville' => 'Lille'],
];
?>
<div class="entite-content">
    <h2>Entités</h2>
    <ul>
        <?php foreach($entites as $e): ?>
        <li><?= htmlspecialchars($e['nom']) ?> - <?= htmlspecialchars($e['ville']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
