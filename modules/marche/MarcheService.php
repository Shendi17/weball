<?php
/**
 * Service pour la gestion de marche
 */
class MarcheService {
    
    public static function getAll() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT * FROM marche ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public static function getById($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM marche WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public static function count() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM marche");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
