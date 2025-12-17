<?php
echo "<h2>数据库初始化</h2>";
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
</style>";

echo "<div class='container'>";

// 首先尝试连接MySQL（不指定数据库）
try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ MySQL连接成功</div>";
    
    // 检查数据库是否存在
    $stmt = $pdo->query("SHOW DATABASES LIKE 'henyii'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='info'>📊 数据库 'henyii' 已存在</div>";
        
        // 连接到henyii数据库
        $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 检查表是否存在
        $tables = ['users', 'activities', 'students', 'participants', 'groups', 'logs'];
        $existingTables = [];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $existingTables[] = $table;
                echo "<div class='success'>✅ 表 '$table' 已存在</div>";
            } else {
                echo "<div class='warning'>⚠️ 表 '$table' 不存在</div>";
            }
        }
        
        // 如果所有表都存在，检查是否有数据
        if (count($existingTables) == count($tables)) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
            $userCount = $stmt->fetchColumn();
            if ($userCount == 0) {
                echo "<div class='warning'>⚠️ 数据库表存在但没有用户数据</div>";
                echo "<a href='create_admin.php' class='btn'>创建管理员账号</a>";
            } else {
                echo "<div class='success'>✅ 数据库已初始化完成</div>";
                echo "<a href='dashboard_teacher.php' class='btn'>访问教师仪表板</a>";
            }
        } else {
            echo "<div class='warning'>⚠️ 部分表缺失，需要创建</div>";
            echo "<a href='?action=create_tables' class='btn btn-danger'>创建缺失的表</a>";
        }
        
    } else {
        echo "<div class='warning'>⚠️ 数据库 'henyii' 不存在</div>";
        echo "<a href='?action=create_database' class='btn btn-danger'>创建数据库</a>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ MySQL连接失败: " . $e->getMessage() . "</div>";
    echo "<div class='info'>请确保：</div>";
    echo "<ul>";
    echo "<li>XAMPP已启动</li>";
    echo "<li>MySQL服务正在运行</li>";
    echo "<li>用户名和密码正确</li>";
    echo "</ul>";
}

// 处理创建数据库的请求
if (isset($_GET['action']) && $_GET['action'] == 'create_database') {
    try {
        $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS henyii CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<div class='success'>✅ 数据库 'henyii' 创建成功</div>";
        
        // 重新加载页面
        echo "<script>setTimeout(function(){ window.location.href='init_database.php'; }, 2000);</script>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ 创建数据库失败: " . $e->getMessage() . "</div>";
    }
}

// 处理创建表的请求
if (isset($_GET['action']) && $_GET['action'] == 'create_tables') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=henyii;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 创建用户表
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'teacher', 'superadmin') NOT NULL DEFAULT 'teacher',
            language VARCHAR(10) DEFAULT 'zh',
            email VARCHAR(255),
            name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            reset_token VARCHAR(64),
            reset_token_expires DATETIME
        )");
        
        // 创建活动表
        $pdo->exec("CREATE TABLE IF NOT EXISTS activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            date DATE NOT NULL,
            location VARCHAR(255),
            organizer_id INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            event_type VARCHAR(100),
            organizer VARCHAR(255),
            level VARCHAR(50),
            achievement VARCHAR(255),
            objectives TEXT,
            content TEXT,
            followup TEXT,
            visibility ENUM('public', 'private') DEFAULT 'public',
            stat_malay_m INT DEFAULT 0,
            stat_malay_f INT DEFAULT 0,
            stat_chinese_m INT DEFAULT 0,
            stat_chinese_f INT DEFAULT 0,
            stat_indian_m INT DEFAULT 0,
            stat_indian_f INT DEFAULT 0,
            stat_others_m INT DEFAULT 0,
            stat_others_f INT DEFAULT 0,
            teacher VARCHAR(100),
            approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
        )");
        
        // 创建学生表
        $pdo->exec("CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_no VARCHAR(50) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            chinese_name VARCHAR(100),
            class VARCHAR(20),
            gender ENUM('M', 'F'),
            race VARCHAR(50),
            email VARCHAR(255),
            religion VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 创建参与者表
        $pdo->exec("CREATE TABLE IF NOT EXISTS participants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            activity_id INT NOT NULL,
            user_id INT,
            student_id INT,
            class VARCHAR(20),
            gender ENUM('M', 'F'),
            race VARCHAR(50),
            achievement VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
        )");
        
        // 创建组表
        $pdo->exec("CREATE TABLE IF NOT EXISTS groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 创建日志表
        $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            detail TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )");
        
        echo "<div class='success'>✅ 所有表创建成功</div>";
        
        // 重新加载页面
        echo "<script>setTimeout(function(){ window.location.href='init_database.php'; }, 2000);</script>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ 创建表失败: " . $e->getMessage() . "</div>";
    }
}

echo "<h3>快速链接:</h3>";
echo "<a href='fix_database.php' class='btn'>数据库检查</a>";
echo "<a href='create_admin.php' class='btn'>创建管理员</a>";
echo "<a href='index.php' class='btn'>返回首页</a>";

echo "</div>";
?> 