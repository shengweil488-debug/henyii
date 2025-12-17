<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // 加载语言包
    session_start();
    $lang = $_SESSION['lang'] ?? 'zh';
    $langArr = require __DIR__ . '/../lang/' . $lang . '.php';
    echo $langArr['admin_info'] . "\n";
    echo $langArr['id'] . ": {$user['id']}\n";
    echo $langArr['username'] . ": {$user['username']}\n";
    echo $langArr['role'] . ": '{$user['role']}'\n";
} catch (Exception $e) {
    // 加载语言包
    session_start();
    $lang = $_SESSION['lang'] ?? 'zh';
    $langArr = require __DIR__ . '/../lang/' . $lang . '.php';
    echo $langArr['error'] . ': ' . $e->getMessage() . "\n";
}
?> 