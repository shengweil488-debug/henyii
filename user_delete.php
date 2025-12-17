<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/models/User.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: user_list.php'); exit; }
if ($id == $_SESSION['user']['id']) { header('Location: user_list.php'); exit; }
User::deleteById($id);
header('Location: user_list.php');
exit; 