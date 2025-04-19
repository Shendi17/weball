<?php
// Composant dynamique : office (exemple)
offices = [
    ['nom' => 'Bureau Central', 'ville' => 'Paris'],
    ['nom' => 'Agence Sud', 'ville' => 'Marseille'],
];
?>
<div class="office-content">
    <h2>Offices</h2>
    <ul>
        <?php foreach($offices as $o): ?>
        <li><?= htmlspecialchars($o['nom']) ?> - <?= htmlspecialchars($o['ville']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
