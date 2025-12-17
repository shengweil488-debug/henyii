<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/models/User.php';

// 加载语言包
session_start();
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';

echo "<h2>" . ($langArr['check_user_title'] ?? '检查用户账号') . "</h2>";

// 检查principal用户是否存在
$user = User::findByUsername('principal');
if ($user) {
    echo "<p>✅ " . ($langArr['user_exists'] ?? "用户 'principal' 存在") . "</p>";
    echo "<p>" . ($langArr['user_id'] ?? '用户ID') . ": " . $user['id'] . "</p>";
    echo "<p>" . ($langArr['role'] ?? '角色') . ": " . $user['role'] . "</p>";
    echo "<p>" . ($langArr['password_hash'] ?? '密码哈希') . ": " . substr($user['password'], 0, 20) . "...</p>";
    
    // 测试密码
    if (password_verify('admin123', $user['password'])) {
        echo "<p>✅ " . ($langArr['password_verify_success'] ?? "密码 'admin123' 验证成功") . "</p>";
    } else {
        echo "<p>❌ " . ($langArr['password_verify_failed'] ?? "密码 'admin123' 验证失败") . "</p>";
    }
} else {
    echo "<p>❌ " . ($langArr['user_not_exists'] ?? "用户 'principal' 不存在") . "</p>";
}

echo "<h3>" . ($langArr['all_users_list'] ?? '所有用户列表：') . "</h3>";
$allUsers = User::all();
foreach ($allUsers as $u) {
    echo "<p>" . ($langArr['username'] ?? '用户名') . ": " . $u['username'] . ", " . ($langArr['role'] ?? '角色') . ": " . $u['role'] . "</p>";
}
?> 