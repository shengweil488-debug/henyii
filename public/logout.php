<?php
session_start();
require_once __DIR__ . '/../src/controllers/AuthController.php';
AuthController::logout();
header('Location: index.php');
exit; 