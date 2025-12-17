<?php
session_start();
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/models/User.php';

echo "<h2>登录调试工具</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .debug { color: #6c757d; background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; font-family: monospace; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn:hover { background: #0056b3; }
    .btn-success { background: #28a745; }
    .btn-success:hover { background: #218838; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; }
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
    
    // 1. 检查数据库表结构
    echo "<h3>1. 数据库表结构检查:</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table>";
    echo "<tr><th>字段名</th><th>类型</th><th>NULL</th><th>KEY</th><th>DEFAULT</th><th>EXTRA</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. 检查所有用户数据
    echo "<h3>2. 用户数据检查:</h3>";
    $stmt = $pdo->query("SELECT id, username, role, name, email, password FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>ID</th><th>用户名</th><th>角色</th><th>姓名</th><th>邮箱</th><th>密码哈希</th><th>操作</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$user['name']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>" . substr($user['password'], 0, 30) . "...</td>";
        echo "<td>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='debug_username' value='{$user['username']}'>";
        echo "<button type='submit' name='debug_user'>调试</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 3. 处理调试请求
    if (isset($_POST['debug_user'])) {
        $username = $_POST['debug_username'];
        echo "<h3>3. 调试用户: $username</h3>";
        
        // 直接查询数据库
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dbUser) {
            echo "<div class='success'>✅ 用户存在于数据库中</div>";
            echo "<div class='debug'>";
            echo "用户ID: {$dbUser['id']}<br>";
            echo "用户名: {$dbUser['username']}<br>";
            echo "角色: {$dbUser['role']}<br>";
            echo "姓名: {$dbUser['name']}<br>";
            echo "邮箱: {$dbUser['email']}<br>";
            echo "语言: {$dbUser['language']}<br>";
            echo "密码哈希: {$dbUser['password']}<br>";
            echo "</div>";
            
            // 测试密码验证
            $testPasswords = ['password', 'admin123', '123456', 'admin'];
            echo "<h4>密码验证测试:</h4>";
            foreach ($testPasswords as $testPwd) {
                $isValid = password_verify($testPwd, $dbUser['password']);
                $status = $isValid ? "✅ 正确" : "❌ 错误";
                echo "<div class='info'>测试密码 '$testPwd': $status</div>";
            }
            
            // 测试User::findByUsername方法
            echo "<h4>User::findByUsername 方法测试:</h4>";
            $userModel = User::findByUsername($username);
            if ($userModel) {
                echo "<div class='success'>✅ User::findByUsername 返回用户数据</div>";
                echo "<div class='debug'>";
                echo "返回数据: " . print_r($userModel, true);
                echo "</div>";
            } else {
                echo "<div class='error'>❌ User::findByUsername 返回 false</div>";
            }
            
            // 测试AuthController::login方法
            echo "<h4>AuthController::login 方法测试:</h4>";
            foreach ($testPasswords as $testPwd) {
                $loginResult = AuthController::login($username, $testPwd);
                if ($loginResult) {
                    echo "<div class='success'>✅ 登录成功 (密码: $testPwd)</div>";
                    echo "<div class='debug'>";
                    echo "返回数据: " . print_r($loginResult, true);
                    echo "</div>";
                    break;
                } else {
                    echo "<div class='error'>❌ 登录失败 (密码: $testPwd)</div>";
                }
            }
            
        } else {
            echo "<div class='error'>❌ 用户不存在于数据库中</div>";
        }
    }
    
    // 4. 修复工具
    echo "<h3>4. 快速修复工具:</h3>";
    echo "<div class='info'>选择要修复的用户:</div>";
    
    foreach ($users as $user) {
        echo "<div style='margin: 10px 0;'>";
        echo "<strong>{$user['username']}</strong> ({$user['name']}) - ";
        echo "<a href='?action=reset_password&username={$user['username']}&password=password' class='btn btn-success'>重置为 password</a> ";
        echo "<a href='?action=reset_password&username={$user['username']}&password=admin123' class='btn btn-success'>重置为 admin123</a>";
        echo "</div>";
    }
    
    // 处理修复请求
    if (isset($_GET['action']) && $_GET['action'] === 'reset_password') {
        $username = $_GET['username'];
        $newPassword = $_GET['password'];
        
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$passwordHash, $username]);
        
        echo "<div class='success'>✅ 用户 $username 的密码已重置为: $newPassword</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'debug_login.php'; }, 2000);</script>";
    }
    
    // 5. 手动测试表单
    echo "<h3>5. 手动登录测试:</h3>";
    echo "<div class='test-form'>";
    echo "<form method='post'>";
    echo "<input type='text' name='test_username' placeholder='用户名' required>";
    echo "<input type='password' name='test_password' placeholder='密码' required>";
    echo "<button type='submit' name='manual_test'>测试登录</button>";
    echo "</form>";
    echo "</div>";
    
    if (isset($_POST['manual_test'])) {
        $username = $_POST['test_username'];
        $password = $_POST['test_password'];
        
        echo "<h4>手动测试结果:</h4>";
        echo "<div class='info'>测试: $username / $password</div>";
        
        // 步骤1: 检查用户是否存在
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dbUser) {
            echo "<div class='success'>✅ 步骤1: 用户存在</div>";
            
            // 步骤2: 检查密码验证
            $passwordValid = password_verify($password, $dbUser['password']);
            if ($passwordValid) {
                echo "<div class='success'>✅ 步骤2: 密码验证成功</div>";
                
                // 步骤3: 测试User::findByUsername
                $userModel = User::findByUsername($username);
                if ($userModel) {
                    echo "<div class='success'>✅ 步骤3: User::findByUsername 成功</div>";
                    
                    // 步骤4: 测试AuthController::login
                    $loginResult = AuthController::login($username, $password);
                    if ($loginResult) {
                        echo "<div class='success'>✅ 步骤4: AuthController::login 成功</div>";
                        echo "<div class='info'>登录成功！用户信息:</div>";
                        echo "<div class='debug'>";
                        echo "ID: {$loginResult['id']}<br>";
                        echo "用户名: {$loginResult['username']}<br>";
                        echo "角色: {$loginResult['role']}<br>";
                        echo "姓名: {$loginResult['name']}<br>";
                        echo "</div>";
                    } else {
                        echo "<div class='error'>❌ 步骤4: AuthController::login 失败</div>";
                    }
                } else {
                    echo "<div class='error'>❌ 步骤3: User::findByUsername 失败</div>";
                }
            } else {
                echo "<div class='error'>❌ 步骤2: 密码验证失败</div>";
                echo "<div class='debug'>数据库密码哈希: {$dbUser['password']}</div>";
            }
        } else {
            echo "<div class='error'>❌ 步骤1: 用户不存在</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ 数据库连接失败: " . $e->getMessage() . "</div>";
}

echo "<h3>快速链接:</h3>";
echo "<a href='login.php' class='btn btn-success'>🔗 登录页面</a>";
echo "<a href='fix_principal_login.php' class='btn'>🔧 Principal修复</a>";
echo "<a href='test_login.php' class='btn'>🧪 登录测试</a>";
echo "<a href='import_complete_database.php' class='btn'>📥 重新导入数据库</a>";

echo "</div>";
?> 