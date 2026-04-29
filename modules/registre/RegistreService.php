<?php
// Service métier pour la gestion du registre (exemple)
class RegistreService {
    public static function getCategories() {
        // Ici, tu peux récupérer les catégories depuis la base ou une source de test
        return ['Catégorie A', 'Catégorie B', 'Catégorie C'];
    }
    public static function getDocuments() {
        // Ici, tu peux récupérer les documents depuis la base ou une source de test
        return [
            ['titre' => 'Document 1'],
            ['titre' => 'Document 2'],
            ['titre' => 'Document 3'],
        ];
    }
}
