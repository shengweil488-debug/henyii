-- 更新users表，添加找回密码功能所需的字段
ALTER TABLE users 
ADD COLUMN email VARCHAR(255) NULL AFTER username,
ADD COLUMN reset_token VARCHAR(64) NULL AFTER email,
ADD COLUMN reset_token_expires DATETIME NULL AFTER reset_token,
ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1 AFTER role;

-- 为email字段添加索引以提高查询性能
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_reset_token ON users(reset_token);

-- 示例：为现有用户添加测试邮箱（可选）
-- UPDATE users SET email = CONCAT(username, '@example.com') WHERE email IS NULL; 

-- 操作日志表
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(64) NOT NULL,
    detail TEXT,
    created_at DATETIME NOT NULL,
    INDEX(user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;