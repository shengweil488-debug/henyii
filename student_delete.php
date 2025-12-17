<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/StudentController.php';
$id = $_GET['id'] ?? null;
if ($id) {
    StudentController::delete($id);
}
header('Location: student_list.php');
exit; 