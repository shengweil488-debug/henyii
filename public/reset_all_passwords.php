<?php
echo "<h2>重置所有用户密码</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn:hover { background: #0056b3; }
    .btn-success { background: #28a745; }
    .btn-success:hover { background: #218838; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

echo "<div class='container'>";

if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    try {
        // 连接数据库
        $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>✅ 数据库连接成功</div>";
        
        // 获取所有用户
        $stmt = $pdo->query("SELECT id, username, role, name FROM users ORDER BY id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>重置密码结果:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>用户名</th><th>角色</th><th>姓名</th><th>新密码</th><th>状态</th></tr>";
        
        $successCount = 0;
        foreach ($users as $user) {
            // 根据角色设置不同的密码
            $newPassword = 'password'; // 默认密码
            
            // 为principal设置admin123
            if ($user['username'] === 'principal') {
                $newPassword = 'admin123';
            }
            
            // 生成新的密码哈希
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // 更新密码
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $updateStmt->execute([$passwordHash, $user['id']]);
            
            $status = $result ? "✅ 成功" : "❌ 失败";
            if ($result) $successCount++;
            
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>$newPassword</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<div class='success'>✅ 密码重置完成！成功重置 $successCount 个用户</div>";
        
        echo "<h3>新的登录信息:</h3>";
        echo "<div class='info'>";
        echo "<strong>超级管理员:</strong><br>";
        echo "用户名: admin<br>";
        echo "密码: password<br><br>";
        echo "<strong>校长:</strong><br>";
        echo "用户名: principal<br>";
        echo "密码: admin123<br><br>";
        echo "<strong>教师账号:</strong><br>";
        echo "用户名: teacher1, teacher2, teacher3, teacher4, teacher5<br>";
        echo "密码: password<br>";
        echo "</div>";
        
        echo "<h3>测试登录:</h3>";
        echo "<div class='info'>";
        echo "请尝试使用以下账号登录:<br>";
        echo "1. 打开 <a href='login.php' target='_blank'>登录页面</a><br>";
        echo "2. 使用上面的账号信息登录<br>";
        echo "3. 如果还是不行，请使用 <a href='debug_login.php' target='_blank'>调试工具</a> 进一步诊断<br>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ 数据库连接失败: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<h3>重置所有用户密码</h3>";
    echo "<div class='info'>";
    echo "这个工具将重置所有用户的密码:<br>";
    echo "• admin: password<br>";
    echo "• principal: admin123<br>";
    echo "• teacher1-5: password<br>";
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "⚠️ 注意：这将覆盖所有现有用户的密码！<br>";
    echo "⚠️ 请确保你已经备份了重要数据。<br>";
    echo "</div>";
    
    echo "<h3>操作:</h3>";
    echo "<a href='?action=reset' class='btn btn-danger' onclick='return confirm(\"确定要重置所有用户密码吗？\")'>🚀 重置所有密码</a>";
}

echo "<h3>快速链接:</h3>";
echo "<a href='login.php' class='btn btn-success'>🔗 登录页面</a>";
echo "<a href='debug_login.php' class='btn'>🔧 调试工具</a>";
echo "<a href='test_login.php' class='btn'>🧪 登录测试</a>";
echo "<a href='import_complete_database.php' class='btn'>📥 重新导入数据库</a>";

echo "</div>";
?> 