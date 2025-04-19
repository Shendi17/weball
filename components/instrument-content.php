<?php
// Composant dynamique : instrument (exemple)
$instruments = [
    ['nom' => 'Guitare', 'famille' => 'Cordes'],
    ['nom' => 'Piano', 'famille' => 'Clavier'],
];
?>
<div class="instrument-content">
    <h2>Instruments</h2>
    <ul>
        <?php foreach($instruments as $i): ?>
        <li><?= htmlspecialchars($i['nom']) ?> - <?= htmlspecialchars($i['famille']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
