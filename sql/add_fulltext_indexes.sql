-- Ajout des index FULLTEXT pour la recherche
ALTER TABLE articles ADD FULLTEXT INDEX ft_articles_search(titre, contenu);
ALTER TABLE formations_formations ADD FULLTEXT INDEX ft_formations_search(titre, description);
ALTER TABLE outils ADD FULLTEXT INDEX ft_outils_search(nom, description);
