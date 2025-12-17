<?php
require_once __DIR__ . '/../config/database.php';

echo "<h2>当前系统中的管理员用户</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .admin { background-color: #ffebee; }
    .superadmin { background-color: #e8f5e8; }
</style>";

try {
    // 获取所有用户
    $stmt = $pdo->query('SELECT id, username, role, email, language, created_at FROM users ORDER BY id');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $adminCount = 0;
    $superadminCount = 0;
    
    echo "<table>";
    echo "<tr><th>ID</th><th>用户名</th><th>角色</th><th>邮箱</th><th>语言</th><th>创建时间</th></tr>";
    
    foreach ($users as $user) {
        $rowClass = '';
        if ($user['role'] === 'admin') {
            $adminCount++;
            $rowClass = 'admin';
        } elseif ($user['role'] === 'superadmin') {
            $superadminCount++;
            $rowClass = 'superadmin';
        }
        
        echo "<tr class='$rowClass'>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($user['role']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($user['language']) . "</td>";
        echo "<td>" . ($user['created_at'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h3>统计信息：</h3>";
    echo "<p><strong>管理员 (admin):</strong> $adminCount 个</p>";
    echo "<p><strong>超级管理员 (superadmin):</strong> $superadminCount 个</p>";
    echo "<p><strong>总管理员数量:</strong> " . ($adminCount + $superadminCount) . " 个</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>错误: " . $e->getMessage() . "</p>";
}
?> 