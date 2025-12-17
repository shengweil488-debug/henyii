<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
$id = $_GET['id'] ?? null;
$activity_id = $_GET['activity_id'] ?? null;
if ($id && $activity_id) {
    ParticipantController::remove($id);
}
header('Location: participant_list.php?activity_id=' . $activity_id);
exit; 