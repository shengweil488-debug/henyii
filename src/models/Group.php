<?php
require_once __DIR__ . '/../../config/database.php';
class Group {
    public static function all() {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM groups ORDER BY name');
        return $stmt->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM groups WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function create($name) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO groups (name) VALUES (?)');
        return $stmt->execute([$name]);
    }
    public static function update($id, $name) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE groups SET name=? WHERE id=?');
        return $stmt->execute([$name, $id]);
    }
    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM groups WHERE id=?');
        return $stmt->execute([$id]);
    }
    public static function members($group_id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT u.* FROM group_members gm LEFT JOIN users u ON gm.user_id = u.id WHERE gm.group_id = ?');
        $stmt->execute([$group_id]);
        return $stmt->fetchAll();
    }
    public static function addMember($group_id, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id) VALUES (?, ?)');
        return $stmt->execute([$group_id, $user_id]);
    }
    public static function removeMember($group_id, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM group_members WHERE group_id=? AND user_id=?');
        return $stmt->execute([$group_id, $user_id]);
    }
} 