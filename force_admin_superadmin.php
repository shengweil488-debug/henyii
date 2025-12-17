<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "强制修正 admin 账号角色为 superadmin...\n";
    // 先清理所有空格和不可见字符
    $stmt = $pdo->prepare('UPDATE users SET role = TRIM(role) WHERE username = ?');
    $stmt->execute(['admin']);
    // 再强制设置为 superadmin
    $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE username = ?');
    $stmt->execute(['superadmin', 'admin']);
    echo "已将 admin 账号角色强制设置为 superadmin\n";
    
    // 查询确认
    $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nadmin 账号信息：\n";
    echo "ID: {$user['id']}\n";
    echo "用户名: {$user['username']}\n";
    echo "角色: '{$user['role']}'\n";
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
?> 