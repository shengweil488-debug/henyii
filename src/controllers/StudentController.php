<?php
require_once __DIR__ . '/../models/Student.php';
class StudentController {
    public static function list() {
        return Student::all();
    }
    public static function detail($idOrNo) {
        // 兼容id或学号
        if (is_numeric($idOrNo) && strlen($idOrNo) < 8) {
            return Student::find($idOrNo);
        } else {
            return Student::findByNo($idOrNo);
        }
    }
    public static function create($student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion) {
        return Student::create($student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion);
    }
    public static function update($id, $student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion) {
        return Student::update($id, $student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion);
    }
    public static function delete($id) {
        return Student::delete($id);
    }
    public static function findByName($name) {
        $all = Student::all();
        foreach ($all as $s) {
            if ($s['name'] === $name) return $s;
        }
        return null;
    }
} 