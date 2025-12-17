<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
class AuthController {
    public static function login($username, $password) {
        $user = \User::findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // 返回完整用户信息
        }
        return false;
    }
    public static function register($username, $password, $role, $language) {
        if (User::findByUsername($username)) {
            return false; // 用户已存在
        }
        return User::create($username, $password, $role, $language);
    }
    public static function logout() {
        unset($_SESSION['user']);
    }
    public static function forgotPassword($email) {
        // 查找用户
        $user = User::findByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => '邮箱未注册'];
        }
        // 生成token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1小时有效
        // 存储token到数据库
        User::setResetToken($user['id'], $token, $expires);
        // 这里应发送邮件，测试环境直接返回重置链接
        $resetLink = (isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] : '') . '/reset_password.php?token=' . $token;
        return [
            'success' => true,
            'message' => '重置链接已生成，请查收邮件（测试环境直接显示链接）',
            'reset_link' => $resetLink
        ];
    }
    public static function resetPassword($token, $newPassword) {
        $user = User::findByResetToken($token);
        if (!$user) {
            return ['success' => false, 'message' => '无效或已过期的重置链接'];
        }
        // 更新密码并清除token
        User::updatePassword($user['id'], $newPassword);
        User::clearResetToken($user['id']);
        return ['success' => true, 'message' => '密码重置成功，请重新登录'];
    }
} 