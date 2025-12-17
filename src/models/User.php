<?php
require_once __DIR__ . '/../../config/database.php';
class User {
    public static function findByUsername($username) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    public static function create($username, $password, $role, $language) {
        global $pdo;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role, language) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$username, $hash, $role, $language]);
    }
    public static function all() {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM users ORDER BY id');
        return $stmt->fetchAll();
    }
    public static function createWithEmail($username, $password, $role, $language, $email, $name = '') {
        global $pdo;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role, language, email, name) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$username, $hash, $role, $language, $email, $name]);
    }
    public static function updateAllFields($id, $username, $email, $role, $language, $name = '') {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, role=?, language=?, name=? WHERE id=?');
        return $stmt->execute([$username, $email, $role, $language, $name, $id]);
    }
    public static function updatePassword($id, $newPassword) {
        global $pdo;
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password=? WHERE id=?');
        return $stmt->execute([$hash, $id]);
    }
    public static function updateProfile($id, $email, $language) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE users SET email=?, language=? WHERE id=?');
        return $stmt->execute([$email, $language, $id]);
    }
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function deleteById($id) {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }
    public static function getAllUsers() {
        return self::all();
    }
    public static function updateLastLogin($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
        return $stmt->execute([$id]);
    }
    public static function findByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    public static function setResetToken($id, $token, $expires) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE users SET reset_token=?, reset_token_expires=? WHERE id=?');
        return $stmt->execute([$token, $expires, $id]);
    }
    public static function findByResetToken($token) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token=? AND reset_token_expires > NOW()');
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    public static function clearResetToken($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE users SET reset_token=NULL, reset_token_expires=NULL WHERE id=?');
        return $stmt->execute([$id]);
    }
} 