<?php
/**
 * Service pour la gestion de entite
 */
class EntiteService {
    
    /**
     * Récupère tous les éléments
     */
    public static function getAll() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT * FROM entite ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère un élément par son ID
     */
    public static function getById($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM entite WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Crée un nouvel élément
     */
    public static function create($data) {
        $pdo = getPDO();
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO entite (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $pdo->lastInsertId();
    }
    
    /**
     * Met à jour un élément
     */
    public static function update($id, $data) {
        $pdo = getPDO();
        
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "$column = ?";
        }
        
        $sql = "UPDATE entite SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        $values = array_values($data);
        $values[] = $id;
        
        return $stmt->execute($values);
    }
    
    /**
     * Supprime un élément
     */
    public static function delete($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM entite WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Compte le nombre total d'éléments
     */
    public static function count() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM entite");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
