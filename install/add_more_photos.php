<?php
/**
 * Ajout de photos pour les personnalités manquantes
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

echo "=== Ajout photos personnalités manquantes ===\n\n";

// Photos supplémentaires depuis Wikimedia Commons
$photos = [
    // Musiciens
    'Beyoncé' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/Beyonce_-_The_Formation_World_Tour%2C_at_Wembley_Stadium_in_London%2C_England.jpg/220px-Beyonce_-_The_Formation_World_Tour%2C_at_Wembley_Stadium_in_London%2C_England.jpg',
    'Eminem' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Eminem_-_Concert_for_Valor_in_Washington_DC_Nov._11%2C_2014_%282%29_%28Cropped%29.jpg/220px-Eminem_-_Concert_for_Valor_in_Washington_DC_Nov._11%2C_2014_%282%29_%28Cropped%29.jpg',
    
    // Acteurs
    'Gad Elmaleh' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Gad_Elmaleh_Cannes_2016.jpg/220px-Gad_Elmaleh_Cannes_2016.jpg',
    
    // Écrivains
    'Guillaume Musso' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Guillaume_Musso_2013.jpg/220px-Guillaume_Musso_2013.jpg',
    
    // Entrepreneurs
    'Xavier Niel' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1f/Xavier_Niel_2014.jpg/220px-Xavier_Niel_2014.jpg',
    'Jeff Bezos' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Jeff_Bezos_visits_LAAFB_SMC_%283908618%29_%28cropped%29.jpeg/220px-Jeff_Bezos_visits_LAAFB_SMC_%283908618%29_%28cropped%29.jpeg',
    
    // YouTubeurs
    'Squeezie' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Squeezie_2018.jpg/220px-Squeezie_2018.jpg',
    
    // Activistes
    'Malala Yousafzai' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Malala_Yousafzai_at_Girl_Summit_2014.jpg/220px-Malala_Yousafzai_at_Girl_Summit_2014.jpg',
    
    // Scientifiques
    'Temple Grandin' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Temple_Grandin_at_TED_2010.jpg/220px-Temple_Grandin_at_TED_2010.jpg',
    'Noam Chomsky' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Noam_Chomsky_portrait_2017.jpg/220px-Noam_Chomsky_portrait_2017.jpg',
    
    // Inventeurs
    'Alexander Graham Bell' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/10/Alexander_Graham_Bell.jpg/220px-Alexander_Graham_Bell.jpg',
    'Johannes Gutenberg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/Gutenberg.jpg/220px-Gutenberg.jpg',
    
    // Personnages historiques
    'Ramsès II' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Ramesses_II_in_the_Turin_Museum28.jpg/220px-Ramesses_II_in_the_Turin_Museum28.jpg',
    'Christophe Colomb' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Portrait_of_a_Man%2C_Said_to_be_Christopher_Columbus.jpg/220px-Portrait_of_a_Man%2C_Said_to_be_Christopher_Columbus.jpg',
    
    // Philosophes/Spirituels
    'Bouddha' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/Budda_Dordenma_statue.jpg/220px-Budda_Dordenma_statue.jpg',
    'Diogène de Sinope' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b1/Jean-L%C3%A9on_G%C3%A9r%C3%B4me_-_Diogenes_-_Walters_37131.jpg/220px-Jean-L%C3%A9on_G%C3%A9r%C3%B4me_-_Diogenes_-_Walters_37131.jpg',
    
    // Militaires
    'Sun Tzu' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0f/Enchoen27n3200.jpg/220px-Enchoen27n3200.jpg',
    'Jean Moulin' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Jean_Moulin_-_photo_d%27identit%C3%A9.jpg/220px-Jean_Moulin_-_photo_d%27identit%C3%A9.jpg',
    
    // Légendes
    'Harry Houdini' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/28/Harry_Houdini%2C_1899.jpg/220px-Harry_Houdini%2C_1899.jpg',
    
    // Tech
    'Linus Torvalds' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/LinuxCon_Europe_Linus_Torvalds_03_%28cropped%29.jpg/220px-LinuxCon_Europe_Linus_Torvalds_03_%28cropped%29.jpg',
    'Tim Berners-Lee' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Sir_Tim_Berners-Lee.jpg/220px-Sir_Tim_Berners-Lee.jpg'
];

$updated = 0;
$notFound = 0;

foreach ($photos as $nom => $url) {
    // Chercher par nom, prénom ou nom complet
    $stmt = $pdo->prepare("
        UPDATE personnalite 
        SET photo = ? 
        WHERE (nom LIKE ? OR prenom LIKE ? OR CONCAT(prenom, ' ', nom) LIKE ?)
        AND (photo IS NULL OR photo = '')
    ");
    
    $searchTerm = "%$nom%";
    if ($stmt->execute([$url, $searchTerm, $searchTerm, $searchTerm]) && $stmt->rowCount() > 0) {
        echo "✓ $nom\n";
        $updated++;
    } else {
        echo "⚠ Non trouvé ou déjà une photo: $nom\n";
        $notFound++;
    }
}

echo "\n=== RÉSUMÉ ===\n";
echo "✓ Photos ajoutées: $updated\n";
echo "⚠ Non trouvées ou déjà présentes: $notFound\n";
echo "\n=== Terminé ! ===\n";
