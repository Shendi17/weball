<?php
// Composant dynamique : banque (exemple)
$banques = [
    ['nom' => 'Banque Populaire', 'ville' => 'Paris'],
    ['nom' => 'Crédit Alpha', 'ville' => 'Lyon'],
];
?>
<div class="banque-content">
    <h2>Banques</h2>
    <ul>
        <?php foreach($banques as $b): ?>
        <li><?= htmlspecialchars($b['nom']) ?> - <?= htmlspecialchars($b['ville']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
