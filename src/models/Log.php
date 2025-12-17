<?php
require_once __DIR__ . '/../../config/database.php';
class Log {
    public static function add($userId, $action, $detail = '') {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO logs (user_id, action, detail, created_at) VALUES (?, ?, ?, NOW())');
        return $stmt->execute([$userId, $action, $detail]);
    }
    public static function all($limit = 100) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT l.*, u.username FROM logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.id DESC LIMIT ?');
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
} 