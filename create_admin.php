<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/models/User.php';

// Load language pack
session_start();
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/lang/' . $lang . '.php';

// 强制创建principal账号
$principal = User::findByUsername('principal');
if ($principal) {
    User::updatePassword($principal['id'], 'admin123');
    User::updateAllFields($principal['id'], 'principal', $principal['email'] ?? '', 'admin', $principal['language'] ?? 'zh', $principal['name'] ?? '');
    echo "<h2>principal账号已重置</h2>";
    echo "<p>用户名: principal</p>";
    echo "<p>密码: admin123</p>";
    echo "<p>角色: admin</p>";
} else {
    User::create('principal', 'admin123', 'admin', 'zh');
    echo "<h2>principal账号已创建</h2>";
    echo "<p>用户名: principal</p>";
    echo "<p>密码: admin123</p>";
    echo "<p>角色: admin</p>";
}
echo "<p><a href='login.php'>返回登录页面</a></p>";
?> 