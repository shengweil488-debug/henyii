<?php
echo "<h2>完整数据库导入工具</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
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
    .progress { width: 100%; background-color: #f0f0f0; border-radius: 5px; margin: 10px 0; }
    .progress-bar { height: 20px; background-color: #007bff; border-radius: 5px; text-align: center; line-height: 20px; color: white; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

echo "<div class='container'>";

// 检查是否有导入请求
if (isset($_GET['action']) && $_GET['action'] == 'import') {
    try {
        // 连接到MySQL（不指定数据库）
        $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>✅ MySQL连接成功</div>";
        
        // 读取SQL文件
        $sqlFile = '../complete_database.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            echo "<div class='info'>📄 SQL文件读取成功 (" . number_format(strlen($sql)) . " 字节)</div>";
            
            // 分割SQL语句
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            $successCount = 0;
            $errorCount = 0;
            $totalStatements = count($statements);
            
            echo "<div class='progress'>";
            echo "<div class='progress-bar' id='progressBar' style='width: 0%'>0%</div>";
            echo "</div>";
            
            foreach ($statements as $index => $statement) {
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement);
                        $successCount++;
                        
                        // 更新进度条
                        $progress = round(($index + 1) / $totalStatements * 100);
                        echo "<script>document.getElementById('progressBar').style.width = '$progress%'; document.getElementById('progressBar').textContent = '$progress%';</script>";
                        echo "<script>document.getElementById('progressBar').innerHTML = '$progress%';</script>";
                        
                    } catch (PDOException $e) {
                        $errorCount++;
                        echo "<div class='error'>❌ SQL执行错误: " . $e->getMessage() . "</div>";
                    }
                }
            }
            
            echo "<div class='success'>✅ 数据库导入完成！</div>";
            echo "<div class='info'>成功执行: $successCount 条语句</div>";
            if ($errorCount > 0) {
                echo "<div class='warning'>警告: $errorCount 条语句执行失败</div>";
            }
            
            // 验证导入结果
            echo "<h3>验证导入结果:</h3>";
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $tables = ['users', 'activities', 'students', 'participants', 'groups', 'logs', 'activity_files', 'group_members'];
                echo "<table>";
                echo "<tr><th>表名</th><th>记录数</th><th>状态</th></tr>";
                
                foreach ($tables as $table) {
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                        $count = $stmt->fetchColumn();
                        $status = $count > 0 ? "✅ 有数据" : "⚠️ 空表";
                        echo "<tr><td>$table</td><td>$count</td><td>$status</td></tr>";
                    } catch (Exception $e) {
                        echo "<tr><td>$table</td><td>错误</td><td>❌ 表不存在</td></tr>";
                    }
                }
                echo "</table>";
                
                // 显示用户账号信息
                echo "<h3>默认账号信息:</h3>";
                echo "<div class='info'>";
                echo "<strong>超级管理员:</strong><br>";
                echo "用户名: admin<br>";
                echo "密码: password<br><br>";
                echo "<strong>校长:</strong><br>";
                echo "用户名: principal<br>";
                echo "密码: password<br><br>";
                echo "<strong>教师账号:</strong><br>";
                echo "用户名: teacher1, teacher2, teacher3, teacher4, teacher5<br>";
                echo "密码: password<br>";
                echo "</div>";
                
                // 显示活动信息
                echo "<h3>示例活动:</h3>";
                $stmt = $pdo->query("SELECT title, level, achievement FROM activities ORDER BY id LIMIT 5");
                $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<table>";
                echo "<tr><th>活动名称</th><th>级别</th><th>成就</th></tr>";
                foreach ($activities as $activity) {
                    echo "<tr><td>{$activity['title']}</td><td>{$activity['level']}</td><td>{$activity['achievement']}</td></tr>";
                }
                echo "</table>";
                
            } catch (PDOException $e) {
                echo "<div class='error'>❌ 验证失败: " . $e->getMessage() . "</div>";
            }
            
        } else {
            echo "<div class='error'>❌ SQL文件不存在: $sqlFile</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ 数据库连接失败: " . $e->getMessage() . "</div>";
        echo "<div class='info'>请确保MySQL服务已启动</div>";
    }
} else {
    // 显示导入选项
    echo "<h3>完整数据库导入</h3>";
    
    // 检查SQL文件是否存在
    $sqlFile = '../complete_database.sql';
    if (file_exists($sqlFile)) {
        echo "<div class='success'>✅ SQL文件存在: complete_database.sql</div>";
        echo "<div class='info'>文件大小: " . number_format(filesize($sqlFile)) . " 字节</div>";
        
        echo "<h3>导入内容:</h3>";
        echo "<div class='info'>";
        echo "✅ 8个数据表<br>";
        echo "✅ 7个用户账号<br>";
        echo "✅ 22个学生数据<br>";
        echo "✅ 10个活动数据<br>";
        echo "✅ 8个组数据<br>";
        echo "✅ 完整的参与者数据<br>";
        echo "✅ 完整的统计信息<br>";
        echo "✅ 系统日志数据<br>";
        echo "</div>";
        
        echo "<h3>注意事项:</h3>";
        echo "<div class='warning'>";
        echo "⚠️ 如果数据库已存在，现有数据将被覆盖！<br>";
        echo "⚠️ 请确保MySQL服务正在运行<br>";
        echo "⚠️ 请确保有足够的数据库权限<br>";
        echo "⚠️ 导入过程可能需要几分钟时间<br>";
        echo "</div>";
        
        echo "<h3>操作:</h3>";
        echo "<a href='?action=import' class='btn btn-danger' onclick='return confirm(\"确定要导入完整数据库吗？这将覆盖现有数据！\")'>🚀 开始导入完整数据库</a>";
        
    } else {
        echo "<div class='error'>❌ SQL文件不存在: complete_database.sql</div>";
        echo "<div class='info'>请确保 complete_database.sql 文件在项目根目录中</div>";
    }
    
    echo "<h3>手动导入方法:</h3>";
    echo "<div class='info'>";
    echo "1. 打开phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a><br>";
    echo "2. 创建新数据库 'henyii'<br>";
    echo "3. 选择数据库，点击 'SQL' 标签<br>";
    echo "4. 复制 complete_database.sql 文件内容并粘贴<br>";
    echo "5. 点击 '执行' 按钮<br>";
    echo "</div>";
}

echo "<h3>快速链接:</h3>";
echo "<a href='dashboard_teacher.php' class='btn btn-success'>教师仪表板</a>";
echo "<a href='login.php' class='btn'>登录页面</a>";
echo "<a href='fix_database.php' class='btn'>数据库检查</a>";
echo "<a href='fix_foreign_keys.php' class='btn'>外键修复</a>";

echo "</div>";
?> 