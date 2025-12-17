<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "将 admin 账号提升为 superadmin...\n";
    
    // 更新admin账号的角色为superadmin
    $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE username = ?');
    $stmt->execute(['superadmin', 'admin']);
    echo "已将 admin 账号角色设置为 superadmin\n";
    
    // 显示admin账号信息
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nadmin 账号信息：\n";
    echo "ID: {$user['id']}\n";
    echo "用户名: {$user['username']}\n";
    echo "角色: {$user['role']}\n";
    echo "邮箱: {$user['email']}\n";
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
?> 