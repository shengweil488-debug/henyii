<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ActivityController.php';
$user = $_SESSION['user'];
$id = $_GET['id'] ?? null;
if ($id) {
    $activity = ActivityController::detail($id);
    if ($activity && ($user['role'] === 'admin' || $user['id'] == $activity['organizer_id'])) {
        ActivityController::delete($id);
    }
}
header('Location: dashboard.php');
exit; 