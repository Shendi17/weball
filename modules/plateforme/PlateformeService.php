<?php
/**
 * Service pour la gestion de plateforme
 */
class PlateformeService {
    
    public static function getAll() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT * FROM plateforme ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public static function getById($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM plateforme WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public static function count() {
        $pdo = getPDO();
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM plateforme");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
