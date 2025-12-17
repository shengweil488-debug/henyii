<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 如果数据库连接失败，显示友好的错误信息
    die('数据库连接失败：请确保MySQL服务已启动。错误信息：' . $e->getMessage());
}
?> 