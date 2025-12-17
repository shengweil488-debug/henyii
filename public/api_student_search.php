<?php
require_once __DIR__ . '/../src/models/Student.php';
header('Content-Type: application/json');
$q = $_GET['q'] ?? '';
$result = [];
if ($q) {
    $students = Student::all();
    foreach ($students as $s) {
        if (
            stripos($s['student_no'], $q) !== false ||
            stripos($s['name'], $q) !== false ||
            stripos($s['class'], $q) !== false
        ) {
            $result[] = [
                'id' => $s['id'],
                'student_no' => $s['student_no'],
                'name' => $s['name'],
                'class' => $s['class'],
                'gender' => $s['gender'],
                'race' => $s['race'],
                'religion' => $s['religion'] ?? '',
                'chinese_name' => $s['chinese_name'] ?? '',
                'email' => $s['email']
            ];
        }
    }
}
echo json_encode($result); 