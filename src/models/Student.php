<?php
require_once __DIR__ . '/../../config/database.php';
class Student {
    public static function all() {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM students ORDER BY class, name');
        return $stmt->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function findByNo($student_no) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM students WHERE student_no = ?');
        $stmt->execute([$student_no]);
        return $stmt->fetch();
    }
    public static function create($student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO students (student_no, name, chinese_name, class, gender, race, email, religion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion]);
    }
    public static function update($id, $student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE students SET student_no=?, name=?, chinese_name=?, class=?, gender=?, race=?, email=?, religion=? WHERE id=?');
        return $stmt->execute([$student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion, $id]);
    }
    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM students WHERE id=?');
        return $stmt->execute([$id]);
    }
    public static function getAllStudents() {
        return self::all();
    }
} 