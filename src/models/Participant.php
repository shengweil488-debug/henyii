<?php
require_once __DIR__ . '/../../config/database.php';
class Participant {
    public static function all($activity_id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT p.*, p.participant_class, p.participant_gender, p.participant_race, p.participant_religion, u.username, s.student_no, s.name as student_name, s.class, s.gender, s.race, s.religion FROM participants p 
            LEFT JOIN users u ON p.user_id = u.id 
            LEFT JOIN students s ON p.student_id = s.id 
            WHERE p.activity_id = ?');
        $stmt->execute([$activity_id]);
        return $stmt->fetchAll();
    }
    public static function add($activity_id, $user_id = null, $student_id = null) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO participants (activity_id, user_id, student_id) VALUES (?, ?, ?)');
        return $stmt->execute([$activity_id, $user_id, $student_id]);
    }
    public static function remove($id) {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM participants WHERE id = ?');
        return $stmt->execute([$id]);
    }
} 