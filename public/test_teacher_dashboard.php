<?php
// 测试教师仪表板功能
session_start();

// 模拟教师登录
$_SESSION['user_id'] = 1; // 假设用户ID为1是教师
$_SESSION['lang'] = 'zh';

echo "<h2>教师仪表板测试</h2>";
echo "<p>正在测试教师仪表板功能...</p>";

// 检查文件是否存在
if (file_exists('dashboard_teacher.php')) {
    echo "<p>✅ dashboard_teacher.php 文件存在</p>";
} else {
    echo "<p>❌ dashboard_teacher.php 文件不存在</p>";
}

// 检查数据库连接
try {
    require_once '../config/database.php';
    echo "<p>✅ 数据库连接成功</p>";
    
    // 检查活动表
    $stmt = $pdo->query('SELECT COUNT(*) FROM activities');
    $activityCount = $stmt->fetchColumn();
    echo "<p>✅ 活动表存在，共有 {$activityCount} 个活动</p>";
    
    // 检查用户表
    $stmt = $pdo->query('SELECT COUNT(*) FROM users WHERE role = "teacher"');
    $teacherCount = $stmt->fetchColumn();
    echo "<p>✅ 用户表存在，共有 {$teacherCount} 个教师</p>";
    
} catch (Exception $e) {
    echo "<p>❌ 数据库连接失败: " . $e->getMessage() . "</p>";
}

// 检查语言包
$langFiles = ['zh.php', 'en.php', 'ms.php'];
foreach ($langFiles as $langFile) {
    if (file_exists("../lang/{$langFile}")) {
        echo "<p>✅ 语言包 {$langFile} 存在</p>";
    } else {
        echo "<p>❌ 语言包 {$langFile} 不存在</p>";
    }
}

echo "<h3>测试链接</h3>";
echo "<p><a href='dashboard_teacher.php' target='_blank'>打开教师仪表板</a></p>";
echo "<p><a href='login.php'>返回登录页面</a></p>";
?> 