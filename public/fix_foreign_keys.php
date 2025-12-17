<?php
echo "<h2>外键约束修复工具</h2>";
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
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

echo "<div class='container'>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ 数据库连接成功</div>";
    
    // 检查用户表
    echo "<h3>1. 检查用户表</h3>";
    $stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<div class='error'>❌ 用户表为空！需要先创建用户</div>";
        echo "<a href='import_database.php' class='btn btn-danger'>导入完整数据库</a>";
    } else {
        echo "<div class='success'>✅ 用户表有 " . count($users) . " 个用户</div>";
        echo "<table>";
        echo "<tr><th>ID</th><th>用户名</th><th>角色</th></tr>";
        foreach ($users as $user) {
            echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$user['role']}</td></tr>";
        }
        echo "</table>";
    }
    
    // 检查组表
    echo "<h3>2. 检查组表</h3>";
    $stmt = $pdo->query("SELECT id, name FROM groups ORDER BY id");
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($groups)) {
        echo "<div class='error'>❌ 组表为空！</div>";
    } else {
        echo "<div class='success'>✅ 组表有 " . count($groups) . " 个组</div>";
        echo "<table>";
        echo "<tr><th>ID</th><th>组名</th></tr>";
        foreach ($groups as $group) {
            echo "<tr><td>{$group['id']}</td><td>{$group['name']}</td></tr>";
        }
        echo "</table>";
    }
    
    // 检查组成员表
    echo "<h3>3. 检查组成员表</h3>";
    $stmt = $pdo->query("SELECT gm.*, u.username, g.name as group_name 
                        FROM group_members gm 
                        LEFT JOIN users u ON gm.user_id = u.id 
                        LEFT JOIN groups g ON gm.group_id = g.id 
                        ORDER BY gm.id");
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($members)) {
        echo "<div class='warning'>⚠️ 组成员表为空</div>";
    } else {
        echo "<div class='success'>✅ 组成员表有 " . count($members) . " 条记录</div>";
        echo "<table>";
        echo "<tr><th>ID</th><th>组ID</th><th>组名</th><th>用户ID</th><th>用户名</th></tr>";
        foreach ($members as $member) {
            $username = $member['username'] ?? '用户不存在';
            $groupName = $member['group_name'] ?? '组不存在';
            $rowClass = ($member['username'] && $member['group_name']) ? '' : 'style="background-color: #ffebee;"';
            echo "<tr $rowClass>";
            echo "<td>{$member['id']}</td>";
            echo "<td>{$member['group_id']}</td>";
            echo "<td>$groupName</td>";
            echo "<td>{$member['user_id']}</td>";
            echo "<td>$username</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 修复选项
    if (isset($_GET['action']) && $_GET['action'] == 'fix') {
        echo "<h3>4. 修复外键约束问题</h3>";
        
        // 删除无效的组成员记录
        $stmt = $pdo->prepare("DELETE FROM group_members WHERE user_id NOT IN (SELECT id FROM users)");
        $stmt->execute();
        $deletedUsers = $stmt->rowCount();
        
        $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id NOT IN (SELECT id FROM groups)");
        $stmt->execute();
        $deletedGroups = $stmt->rowCount();
        
        echo "<div class='success'>✅ 修复完成！</div>";
        if ($deletedUsers > 0) {
            echo "<div class='info'>删除了 $deletedUsers 条无效用户引用</div>";
        }
        if ($deletedGroups > 0) {
            echo "<div class='info'>删除了 $deletedGroups 条无效组引用</div>";
        }
        
        // 重新添加有效的组成员
        $validMembers = [
            [1, 1], // 管理员加入学术组
            [2, 2], // 校长加入体育组
        ];
        
        // 检查是否有教师用户
        $stmt = $pdo->query("SELECT id FROM users WHERE role = 'teacher' ORDER BY id LIMIT 3");
        $teachers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($teachers) >= 3) {
            $validMembers[] = [1, $teachers[0]]; // 第一个教师加入学术组
            $validMembers[] = [2, $teachers[1]]; // 第二个教师加入体育组
            $validMembers[] = [3, $teachers[2]]; // 第三个教师加入文化组
        }
        
        foreach ($validMembers as $member) {
            try {
                $stmt = $pdo->prepare("INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)");
                $stmt->execute($member);
            } catch (PDOException $e) {
                echo "<div class='warning'>⚠️ 添加组成员失败: " . $e->getMessage() . "</div>";
            }
        }
        
        echo "<div class='success'>✅ 重新添加了 " . count($validMembers) . " 条有效的组成员记录</div>";
        
        // 重新加载页面
        echo "<script>setTimeout(function(){ window.location.href='fix_foreign_keys.php'; }, 2000);</script>";
        
    } else {
        echo "<h3>4. 修复操作</h3>";
        echo "<div class='info'>点击下面的按钮来修复外键约束问题：</div>";
        echo "<a href='?action=fix' class='btn btn-danger' onclick='return confirm(\"确定要修复外键约束问题吗？这将删除无效的引用并重新添加有效的记录。\")'>🔧 修复外键约束</a>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ 数据库连接失败: " . $e->getMessage() . "</div>";
    echo "<div class='info'>请确保：</div>";
    echo "<ul>";
    echo "<li>XAMPP已启动</li>";
    echo "<li>MySQL服务正在运行</li>";
    echo "<li>数据库 'henyii' 存在</li>";
    echo "</ul>";
}

echo "<h3>快速链接:</h3>";
echo "<a href='import_database.php' class='btn'>导入数据库</a>";
echo "<a href='fix_database.php' class='btn'>数据库检查</a>";
echo "<a href='init_database.php' class='btn'>数据库初始化</a>";
echo "<a href='dashboard_teacher.php' class='btn'>教师仪表板</a>";

echo "</div>";
?> 