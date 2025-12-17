# 找回密码功能设置指南

## 功能概述

我已经为你的系统添加了完整的找回密码功能，包括：

1. **美观的登录页面** - 现代化的卡片式设计
2. **找回密码表单** - 支持邮箱验证
3. **密码重置页面** - 安全的密码重置流程
4. **后端支持** - 完整的数据库和控制器支持

## 设置步骤

### 1. 更新数据库

首先需要更新你的 `users` 表，添加找回密码所需的字段：

```sql
-- 在MySQL中执行以下SQL语句
ALTER TABLE users 
ADD COLUMN email VARCHAR(255) NULL AFTER username,
ADD COLUMN reset_token VARCHAR(64) NULL AFTER email,
ADD COLUMN reset_token_expires DATETIME NULL AFTER reset_token;

-- 添加索引以提高性能
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_reset_token ON users(reset_token);
```

或者直接运行提供的SQL文件：
```bash
mysql -u root -p henyii < update_users_table.sql
```

### 2. 为现有用户添加邮箱

为测试目的，你可以为现有用户添加邮箱：

```sql
UPDATE users SET email = CONCAT(username, '@example.com') WHERE email IS NULL;
```

### 3. 测试功能

1. 访问 `http://localhost/henyii/public/login.php`
2. 点击 "Forgot your password?" 链接
3. 输入一个存在的邮箱地址
4. 系统会显示一个测试重置链接
5. 点击链接测试密码重置功能

## 文件说明

### 新增/修改的文件：

1. **`public/login.php`** - 美化的登录页面，包含找回密码功能
2. **`public/reset_password.php`** - 密码重置页面
3. **`src/controllers/AuthController.php`** - 添加了找回密码相关方法
4. **`src/models/User.php`** - 添加了数据库操作方法
5. **`update_users_table.sql`** - 数据库更新脚本
6. **`public/test_forgot_password.php`** - 测试页面（可选）

### 主要功能：

#### 登录页面特性：
- 现代化的渐变背景
- 卡片式设计
- 图标和动画效果
- 响应式设计
- 登录和找回密码表单切换

#### 找回密码流程：
1. 用户点击 "Forgot your password?"
2. 输入邮箱地址
3. 系统验证邮箱并生成重置令牌
4. 显示重置链接（测试环境）
5. 用户点击链接进入重置页面
6. 输入新密码并确认
7. 密码更新成功

#### 安全特性：
- 密码强度检测
- 令牌过期机制（1小时）
- 密码哈希存储
- 输入验证

## 生产环境配置

### 邮件发送

在生产环境中，你需要配置邮件发送功能。在 `AuthController::forgotPassword()` 方法中：

```php
// 替换示例代码为实际的邮件发送
$to = $user['email'];
$subject = 'Password Reset Request';
$message = "Click the following link to reset your password: $resetLink";
$headers = 'From: noreply@yourdomain.com';

mail($to, $subject, $message, $headers);
```

### 移除测试功能

在生产环境中，删除或注释掉以下代码：
- `public/test_forgot_password.php` 文件
- 登录页面中显示测试重置链接的代码

## 自定义样式

你可以通过修改CSS来自定义页面外观：

- 主色调：修改 `#667eea` 和 `#764ba2`
- 字体：修改 `font-family` 属性
- 圆角：修改 `border-radius` 值
- 阴影：修改 `box-shadow` 属性

## 故障排除

### 常见问题：

1. **数据库连接错误**
   - 检查 `config/database.php` 配置
   - 确保数据库和表存在

2. **邮箱字段为空**
   - 确保users表有email字段
   - 为用户添加邮箱地址

3. **重置链接无效**
   - 检查令牌是否过期
   - 验证数据库中的reset_token字段

4. **样式不显示**
   - 检查网络连接
   - 确保Font Awesome CDN可访问

### 调试方法：

1. 使用 `public/test_forgot_password.php` 测试功能
2. 检查浏览器控制台是否有错误
3. 查看PHP错误日志
4. 验证数据库字段是否正确添加

## 支持

如果遇到问题，请检查：
1. 数据库表结构是否正确
2. 文件权限是否正确
3. PHP错误日志
4. 网络连接状态

---

**注意：** 在生产环境中，请确保删除测试文件和测试代码，并配置适当的邮件发送功能。 