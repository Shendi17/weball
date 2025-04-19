<?php
// Composant dynamique : journal (exemple de liste d'entrées)
$journal = [
    ['date' => date('Y-m-d'), 'action' => 'Connexion', 'user' => 'Tony'],
    ['date' => date('Y-m-d', strtotime('-1 day')), 'action' => 'Ajout fiche', 'user' => 'Alice'],
];
?>
<div class="journal-content">
    <h2>Journal d'activité</h2>
    <ul>
        <?php foreach($journal as $entry): ?>
        <li><?= htmlspecialchars($entry['date']) ?> : <?= htmlspecialchars($entry['user']) ?> - <?= htmlspecialchars($entry['action']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
