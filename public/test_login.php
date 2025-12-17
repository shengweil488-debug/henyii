<?php
session_start();
require_once __DIR__ . '/../src/controllers/AuthController.php';

echo "<h2>登录功能测试</h2>";
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
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .test-form { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .test-form input { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 3px; }
    .test-form button { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
</style>";

echo "<div class='container'>";

try {
    // 连接数据库
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ 数据库连接成功</div>";
    
    // 显示测试账号
    echo "<h3>测试账号:</h3>";
    $stmt = $pdo->query("SELECT username, role, name, password FROM users ORDER BY id LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>用户名</th><th>角色</th><th>姓名</th><th>密码</th><th>测试</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$user['name']}</td>";
        echo "<td>password</td>";
        echo "<td>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='test_username' value='{$user['username']}'>";
        echo "<input type='hidden' name='test_password' value='password'>";
        echo "<button type='submit' name='test_login'>测试登录</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 处理测试登录
    if (isset($_POST['test_login'])) {
        $username = $_POST['test_username'];
        $password = $_POST['test_password'];
        
        echo "<h3>测试结果:</h3>";
        echo "<div class='info'>正在测试: $username / $password</div>";
        
        $user = AuthController::login($username, $password);
        
        if ($user) {
            echo "<div class='success'>✅ 登录成功！</div>";
            echo "<div class='info'>";
            echo "用户ID: {$user['id']}<br>";
            echo "用户名: {$user['username']}<br>";
            echo "角色: {$user['role']}<br>";
            echo "姓名: {$user['name']}<br>";
            echo "语言: {$user['language']}<br>";
            echo "</div>";
            
            // 测试密码验证
            if (password_verify($password, $user['password'])) {
                echo "<div class='success'>✅ 密码验证成功</div>";
            } else {
                echo "<div class='error'>❌ 密码验证失败</div>";
            }
            
        } else {
            echo "<div class='error'>❌ 登录失败</div>";
            
            // 检查用户是否存在
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($dbUser) {
                echo "<div class='info'>用户存在，但密码不匹配</div>";
                echo "<div class='info'>数据库密码哈希: " . substr($dbUser['password'], 0, 30) . "...</div>";
                
                // 测试密码验证
                if (password_verify($password, $dbUser['password'])) {
                    echo "<div class='success'>✅ 密码验证成功（直接验证）</div>";
                } else {
                    echo "<div class='error'>❌ 密码验证失败（直接验证）</div>";
                }
            } else {
                echo "<div class='error'>用户不存在</div>";
            }
        }
    }
    
    // 手动测试表单
    echo "<h3>手动测试:</h3>";
    echo "<div class='test-form'>";
    echo "<form method='post'>";
    echo "<input type='text' name='manual_username' placeholder='用户名' required>";
    echo "<input type='password' name='manual_password' placeholder='密码' required>";
    echo "<button type='submit' name='manual_test'>测试登录</button>";
    echo "</form>";
    echo "</div>";
    
    if (isset($_POST['manual_test'])) {
        $username = $_POST['manual_username'];
        $password = $_POST['manual_password'];
        
        echo "<h3>手动测试结果:</h3>";
        echo "<div class='info'>测试: $username / $password</div>";
        
        $user = AuthController::login($username, $password);
        
        if ($user) {
            echo "<div class='success'>✅ 登录成功！</div>";
            echo "<div class='info'>";
            echo "用户ID: {$user['id']}<br>";
            echo "用户名: {$user['username']}<br>";
            echo "角色: {$user['role']}<br>";
            echo "姓名: {$user['name']}<br>";
            echo "</div>";
        } else {
            echo "<div class='error'>❌ 登录失败</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ 数据库连接失败: " . $e->getMessage() . "</div>";
}

echo "<h3>快速链接:</h3>";
echo "<a href='login.php' class='btn btn-success'>🔗 打开登录页面</a>";
echo "<a href='fix_principal_login.php' class='btn'>🔧 修复Principal账号</a>";
echo "<a href='dashboard_teacher.php' class='btn'>📊 教师仪表板</a>";

echo "</div>";
?> 