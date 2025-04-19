<?php
// Composant dynamique : widgets d'accueil (exemple)
// Simulons des données dynamiques
$stats = [
    'utilisateurs' => 128,
    'marches' => 42,
    'personnalites' => 11,
];
$derniers = [
    ['type' => 'Marché', 'nom' => 'Marché de Printemps'],
    ['type' => 'Personnalité', 'nom' => 'Jean Dupont'],
    ['type' => 'Entité', 'nom' => 'Société Alpha'],
];
?>
<div class="home-widgets">
    <div class="widgets-row">
        <div class="widget">
            <div class="widget-label">Utilisateurs</div>
            <div class="widget-value"><?= $stats['utilisateurs'] ?></div>
        </div>
        <div class="widget">
            <div class="widget-label">Marchés</div>
            <div class="widget-value"><?= $stats['marches'] ?></div>
        </div>
        <div class="widget">
            <div class="widget-label">Personnalités</div>
            <div class="widget-value"><?= $stats['personnalites'] ?></div>
        </div>
    </div>
    <div class="widgets-row">
        <div class="widget-list">
            <div class="widget-label">Derniers ajouts</div>
            <ul>
                <?php foreach($derniers as $d): ?>
                    <li><b><?= htmlspecialchars($d['type']) ?></b> : <?= htmlspecialchars($d['nom']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
