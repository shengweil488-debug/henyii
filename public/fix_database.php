<?php
echo "<h2>数据库连接检查和修复</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .step { margin: 15px 0; padding: 10px; border-left: 4px solid #007bff; background: #f8f9fa; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn:hover { background: #0056b3; }
</style>";

echo "<div class='container'>";

// 检查MySQL服务状态
echo "<h3>步骤1: 检查MySQL服务状态</h3>";

// 尝试连接数据库
try {
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ MySQL服务正常运行，数据库连接成功！</div>";
    
    // 检查数据库表
    echo "<h3>步骤2: 检查数据库表</h3>";
    $tables = ['users', 'activities', 'students', 'participants', 'groups', 'logs'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='success'>✅ 表 '$table' 存在</div>";
            } else {
                echo "<div class='warning'>⚠️ 表 '$table' 不存在</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ 检查表 '$table' 时出错: " . $e->getMessage() . "</div>";
        }
    }
    
    // 检查用户数据
    echo "<h3>步骤3: 检查用户数据</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        echo "<div class='info'>📊 用户总数: $userCount</div>";
        
        if ($userCount > 0) {
            $stmt = $pdo->query("SELECT username, role FROM users LIMIT 5");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<div class='info'>👥 前5个用户:</div>";
            foreach ($users as $user) {
                echo "<div class='step'>- {$user['username']} ({$user['role']})</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ 检查用户数据时出错: " . $e->getMessage() . "</div>";
    }
    
    // 检查活动数据
    echo "<h3>步骤4: 检查活动数据</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM activities");
        $activityCount = $stmt->fetchColumn();
        echo "<div class='info'>📊 活动总数: $activityCount</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ 检查活动数据时出错: " . $e->getMessage() . "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ 数据库连接失败: " . $e->getMessage() . "</div>";
    
    echo "<h3>解决方案:</h3>";
    echo "<div class='step'>";
    echo "<strong>1. 启动MySQL服务:</strong><br>";
    echo "- 打开XAMPP控制面板<br>";
    echo "- 点击MySQL旁边的'Start'按钮<br>";
    echo "- 等待状态变为绿色<br>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<strong>2. 检查数据库是否存在:</strong><br>";
    echo "- 打开phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a><br>";
    echo "- 检查是否有名为'henyii'的数据库<br>";
    echo "- 如果没有，需要创建数据库<br>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<strong>3. 检查数据库配置:</strong><br>";
    echo "- 确认用户名: root<br>";
    echo "- 确认密码: (空密码)<br>";
    echo "- 确认主机: localhost<br>";
    echo "- 确认数据库名: henyii<br>";
    echo "</div>";
}

echo "<h3>快速链接:</h3>";
echo "<a href='index.php' class='btn'>返回首页</a>";
echo "<a href='login.php' class='btn'>登录页面</a>";
echo "<a href='dashboard_teacher.php' class='btn'>教师仪表板</a>";
echo "<a href='test_teacher_dashboard.php' class='btn'>测试页面</a>";

echo "</div>";
?> 