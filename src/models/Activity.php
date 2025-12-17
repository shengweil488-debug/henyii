<?php
require_once __DIR__ . '/../../config/database.php';
class Activity {
    public static function all() {
        global $pdo;
        $stmt = $pdo->query('SELECT a.*, u.username as organizer_name FROM activities a LEFT JOIN users u ON a.organizer_id = u.id ORDER BY a.date DESC');
        return $stmt->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM activities WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    // 新增：审批状态相关
    public static function approve($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE activities SET approval_status = ? WHERE id = ?');
        return $stmt->execute(['approved', $id]);
    }
    public static function reject($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE activities SET approval_status = ? WHERE id = ?');
        return $stmt->execute(['rejected', $id]);
    }
    public static function create($title, $date, $location, $organizer_id, $description) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO activities (title, date, location, organizer_id, description) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$title, $date, $location, $organizer_id, $description]);
    }
    public static function update($id, $title, $date, $location, $description) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE activities SET title=?, date=?, location=?, description=? WHERE id=?');
        return $stmt->execute([$title, $date, $location, $description, $id]);
    }
    public static function delete($id) {
        global $pdo;
        // 先删除该活动下所有参与者
        $stmt = $pdo->prepare('DELETE FROM participants WHERE activity_id=?');
        $stmt->execute([$id]);
        // 再删除该活动下所有文件
        $stmt = $pdo->prepare('DELETE FROM activity_files WHERE activity_id=?');
        $stmt->execute([$id]);
        // 最后删除活动
        $stmt = $pdo->prepare('DELETE FROM activities WHERE id=?');
        return $stmt->execute([$id]);
    }
    public static function getTotalActivities() {
        global $pdo;
        $stmt = $pdo->query('SELECT COUNT(*) FROM activities');
        return $stmt->fetchColumn();
    }
    public static function getRecentActivities($limit = 5, $level = '') {
        global $pdo;
        if (!empty($level)) {
            $stmt = $pdo->prepare('SELECT a.*, u.username as creator_name FROM activities a LEFT JOIN users u ON a.organizer_id = u.id WHERE a.level = ? ORDER BY a.created_at DESC LIMIT ?');
            $stmt->bindValue(1, $level, PDO::PARAM_STR);
            $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare('SELECT a.*, u.username as creator_name FROM activities a LEFT JOIN users u ON a.organizer_id = u.id ORDER BY a.created_at DESC LIMIT ?');
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAllActivitiesForExport($level = '') {
        global $pdo;
        if (!empty($level)) {
            $stmt = $pdo->prepare('SELECT a.*, u.username as creator_name FROM activities a LEFT JOIN users u ON a.organizer_id = u.id WHERE a.level = ? ORDER BY a.created_at DESC');
            $stmt->bindValue(1, $level, PDO::PARAM_STR);
        } else {
            $stmt = $pdo->prepare('SELECT a.*, u.username as creator_name FROM activities a LEFT JOIN users u ON a.organizer_id = u.id ORDER BY a.created_at DESC');
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
} 