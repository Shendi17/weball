<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Utilisation de la config centrale PDO
require_once dirname(__DIR__, 3) . '/config.db.php';

try {
    $pdo = getPDO();
    
    echo "Connexion établie avec succès !\n\n";
    
    // Articles de démonstration
    $articles = [
        [
            'titre' => 'Bienvenue sur notre nouveau site',
            'contenu' => '<p>Nous sommes ravis de vous accueillir sur notre nouvelle plateforme. Ce site a été conçu pour offrir une meilleure expérience utilisateur et faciliter l\'accès à l\'information.</p>
                         <h3>Nouvelles fonctionnalités</h3>
                         <ul>
                             <li>Interface moderne et responsive</li>
                             <li>Navigation simplifiée</li>
                             <li>Système de recherche amélioré</li>
                         </ul>',
            'categorie' => 'Actualités',
            'statut' => 'publie'
        ],
        [
            'titre' => 'Comment optimiser votre productivité',
            'contenu' => '<p>La productivité est essentielle dans notre monde professionnel moderne. Voici quelques conseils pour améliorer votre efficacité au quotidien.</p>
                         <h3>Les points clés</h3>
                         <ol>
                             <li>Planifiez votre journée la veille</li>
                             <li>Utilisez la technique Pomodoro</li>
                             <li>Faites des pauses régulières</li>
                             <li>Éliminez les distractions</li>
                         </ol>',
            'categorie' => 'Conseils',
            'statut' => 'publie'
        ],
        [
            'titre' => 'Les tendances technologiques de 2025',
            'contenu' => '<p>L\'année 2025 apporte son lot d\'innovations technologiques passionnantes. Découvrons ensemble les principales tendances qui façonnent notre avenir.</p>
                         <h3>Tendances majeures</h3>
                         <ul>
                             <li>Intelligence artificielle avancée</li>
                             <li>Réalité augmentée au quotidien</li>
                             <li>Informatique quantique</li>
                             <li>Technologies vertes</li>
                         </ul>',
            'categorie' => 'Technologies',
            'statut' => 'publie'
        ],
        [
            'titre' => 'Guide débutant : Apprendre la programmation',
            'contenu' => '<p>Vous souhaitez vous lancer dans la programmation ? Ce guide vous donnera les bases essentielles pour bien démarrer.</p>
                         <h3>Par où commencer ?</h3>
                         <ol>
                             <li>Choisir son premier langage</li>
                             <li>Comprendre les concepts de base</li>
                             <li>Pratiquer régulièrement</li>
                             <li>Participer à des projets</li>
                         </ol>
                         <p>La programmation est un voyage passionnant qui demande de la patience et de la persévérance.</p>',
            'categorie' => 'Tutoriels',
            'statut' => 'publie'
        ],
        [
            'titre' => 'Sécurité informatique : les bonnes pratiques',
            'contenu' => '<p>La sécurité informatique est plus importante que jamais. Voici un guide des bonnes pratiques à adopter pour protéger vos données.</p>
                         <h3>Mesures essentielles</h3>
                         <ul>
                             <li>Utiliser des mots de passe forts</li>
                             <li>Activer l\'authentification à deux facteurs</li>
                             <li>Maintenir ses logiciels à jour</li>
                             <li>Sauvegarder régulièrement ses données</li>
                         </ul>',
            'categorie' => 'Conseils',
            'statut' => 'brouillon'
        ]
    ];
    
    // Insertion des articles
    $stmt = $pdo->prepare("INSERT INTO articles (titre, contenu, categorie, statut, date_creation) 
                          VALUES (:titre, :contenu, :categorie, :statut, NOW())");
    
    foreach ($articles as $article) {
        $stmt->execute($article);
        echo "Article '{$article['titre']}' ajouté.\n";
    }
    
    echo "\nNombre total d'articles : " . $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn() . "\n";
    echo "Articles de démonstration ajoutés avec succès !";
    
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
