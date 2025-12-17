<?php
echo "<h2>Principal账号登录修复工具</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn:hover { background: #0056b3; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; }
    .btn-success { background: #28a745; }
    .btn-success:hover { background: #218838; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
</style>";

echo "<div class='container'>";

try {
    // 连接数据库
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ 数据库连接成功</div>";
    
    // 检查principal账号是否存在
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute(['principal']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<div class='info'>📋 Principal账号信息:</div>";
        echo "<table>";
        echo "<tr><th>字段</th><th>值</th></tr>";
        echo "<tr><td>ID</td><td>{$user['id']}</td></tr>";
        echo "<tr><td>用户名</td><td>{$user['username']}</td></tr>";
        echo "<tr><td>角色</td><td>{$user['role']}</td></tr>";
        echo "<tr><td>姓名</td><td>{$user['name']}</td></tr>";
        echo "<tr><td>邮箱</td><td>{$user['email']}</td></tr>";
        echo "<tr><td>语言</td><td>{$user['language']}</td></tr>";
        echo "<tr><td>密码哈希</td><td>" . substr($user['password'], 0, 20) . "...</td></tr>";
        echo "</table>";
        
        // 检查密码是否正确
        $currentPassword = $user['password'];
        $expectedPassword = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password的哈希
        
        if ($currentPassword === $expectedPassword) {
            echo "<div class='success'>✅ 密码哈希正确（对应密码：password）</div>";
        } else {
            echo "<div class='warning'>⚠️ 密码哈希不匹配</div>";
            echo "<div class='info'>当前密码哈希: " . substr($currentPassword, 0, 30) . "...</div>";
            echo "<div class='info'>期望密码哈希: " . substr($expectedPassword, 0, 30) . "...</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Principal账号不存在</div>";
    }
    
    // 显示所有用户账号
    echo "<h3>所有用户账号:</h3>";
    $stmt = $pdo->query("SELECT id, username, role, name, email FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>ID</th><th>用户名</th><th>角色</th><th>姓名</th><th>邮箱</th></tr>";
    foreach ($users as $u) {
        $highlight = ($u['username'] === 'principal') ? 'style="background-color: #fff3cd;"' : '';
        echo "<tr $highlight>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['username']}</td>";
        echo "<td>{$u['role']}</td>";
        echo "<td>{$u['name']}</td>";
        echo "<td>{$u['email']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 修复选项
    echo "<h3>修复选项:</h3>";
    
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'reset_password':
                // 重置principal密码为password
                $newPasswordHash = password_hash('password', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                $stmt->execute([$newPasswordHash, 'principal']);
                
                echo "<div class='success'>✅ Principal密码已重置为: password</div>";
                echo "<div class='info'>新密码哈希: " . substr($newPasswordHash, 0, 30) . "...</div>";
                break;
                
            case 'create_principal':
                // 创建新的principal账号
                $passwordHash = password_hash('password', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role, language, email, name) VALUES (?, ?, 'admin', 'zh', 'principal@henyii.com', '校长')");
                $stmt->execute(['principal', $passwordHash]);
                
                echo "<div class='success'>✅ 新的Principal账号已创建</div>";
                echo "<div class='info'>用户名: principal</div>";
                echo "<div class='info'>密码: password</div>";
                break;
                
            case 'update_admin123':
                // 更新principal密码为admin123
                $admin123Hash = password_hash('admin123', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                $stmt->execute([$admin123Hash, 'principal']);
                
                echo "<div class='success'>✅ Principal密码已更新为: admin123</div>";
                echo "<div class='info'>新密码哈希: " . substr($admin123Hash, 0, 30) . "...</div>";
                break;
        }
        
        // 刷新页面显示最新状态
        echo "<script>setTimeout(function(){ window.location.href = 'fix_principal_login.php'; }, 2000);</script>";
        
    } else {
        echo "<div class='info'>选择修复操作:</div>";
        
        if ($user) {
            echo "<a href='?action=reset_password' class='btn btn-success'>🔄 重置密码为 'password'</a>";
            echo "<a href='?action=update_admin123' class='btn btn-success'>🔑 设置密码为 'admin123'</a>";
        } else {
            echo "<a href='?action=create_principal' class='btn btn-danger'>➕ 创建Principal账号</a>";
        }
    }
    
    // 测试登录功能
    echo "<h3>测试登录:</h3>";
    echo "<div class='info'>";
    echo "请尝试使用以下账号登录:<br>";
    echo "<strong>Principal账号:</strong><br>";
    echo "用户名: principal<br>";
    echo "密码: password (或 admin123，取决于你选择的修复方式)<br><br>";
    echo "<strong>其他测试账号:</strong><br>";
    echo "用户名: admin, 密码: password (超级管理员)<br>";
    echo "用户名: teacher1, 密码: password (教师)<br>";
    echo "</div>";
    
    // 检查登录页面
    echo "<h3>登录页面检查:</h3>";
    $loginFile = 'login.php';
    if (file_exists($loginFile)) {
        echo "<div class='success'>✅ 登录页面存在: $loginFile</div>";
        echo "<a href='$loginFile' class='btn btn-success'>🔗 打开登录页面</a>";
    } else {
        echo "<div class='error'>❌ 登录页面不存在: $loginFile</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ 数据库连接失败: " . $e->getMessage() . "</div>";
    echo "<div class='info'>请确保MySQL服务已启动</div>";
}

echo "<h3>快速链接:</h3>";
echo "<a href='dashboard_teacher.php' class='btn'>教师仪表板</a>";
echo "<a href='import_complete_database.php' class='btn'>数据库导入</a>";
echo "<a href='fix_database.php' class='btn'>数据库检查</a>";

echo "</div>";
?> 