<?php
session_start();
require_once __DIR__ . '/../src/controllers/ActivityController.php';
$user = $_SESSION['user'];
if ($user['role'] !== 'admin') {
    header('Location: activity_manage.php');
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    ActivityController::approve($id);
}
header('Location: activity_manage.php');
exit; 