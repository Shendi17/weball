<?php
/**
 * Service pour la gestion de ecole
 */
class EcoleService {
    
    public static function getAll() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT * FROM ecole ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public static function getById($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM ecole WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public static function count() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM ecole");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
