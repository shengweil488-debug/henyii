-- 恒毅活动报告系统数据库设置
-- Heng Ee Activity Report System Database Setup

-- 创建数据库
CREATE DATABASE IF NOT EXISTS henyii CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE henyii;

-- 删除已存在的表（如果存在）
DROP TABLE IF EXISTS activity_files;
DROP TABLE IF EXISTS participants;
DROP TABLE IF EXISTS group_members;
DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS activities;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS users;

-- 1. 用户表 (users)
CREATE TABLE users (
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
    reset_token_expires DATETIME,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_email (email)
);

-- 2. 活动表 (activities)
CREATE TABLE activities (
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
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_date (date),
    INDEX idx_level (level),
    INDEX idx_approval_status (approval_status),
    INDEX idx_created_at (created_at)
);

-- 3. 学生表 (students)
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_no VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    chinese_name VARCHAR(100),
    class VARCHAR(20),
    gender ENUM('M', 'F'),
    race VARCHAR(50),
    email VARCHAR(255),
    religion VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_no (student_no),
    INDEX idx_class (class),
    INDEX idx_name (name)
);

-- 4. 参与者表 (participants)
CREATE TABLE participants (
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
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
    INDEX idx_activity_id (activity_id),
    INDEX idx_student_id (student_id),
    INDEX idx_user_id (user_id)
);

-- 5. 组表 (groups)
CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- 6. 组成员表 (group_members)
CREATE TABLE group_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_user (group_id, user_id),
    INDEX idx_group_id (group_id),
    INDEX idx_user_id (user_id)
);

-- 7. 日志表 (logs)
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    detail TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- 8. 活动文件表 (activity_files)
CREATE TABLE activity_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_type VARCHAR(100),
    file_size INT,
    upload_path VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    INDEX idx_activity_id (activity_id),
    INDEX idx_file_type (file_type)
);

-- 插入初始数据

-- 1. 创建超级管理员账号
INSERT INTO users (username, password, role, language, email, name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', 'zh', 'admin@henyii.com', '系统管理员'),
('principal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'zh', 'principal@henyii.com', '校长');

-- 2. 创建教师账号
INSERT INTO users (username, password, role, language, email, name) VALUES
('teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'zh', 'teacher1@henyii.com', '张老师'),
('teacher2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'en', 'teacher2@henyii.com', '李老师'),
('teacher3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'ms', 'teacher3@henyii.com', '王老师');

-- 3. 创建示例学生数据
INSERT INTO students (student_no, name, chinese_name, class, gender, race, email, religion) VALUES
('2024001', 'Ahmad bin Ali', '阿末', '4A', 'M', 'Malay', 'ahmad@student.henyii.com', 'Islam'),
('2024002', 'Siti binti Hassan', '西蒂', '4A', 'F', 'Malay', 'siti@student.henyii.com', 'Islam'),
('2024003', 'Tan Wei Ming', '陈伟明', '4B', 'M', 'Chinese', 'weiming@student.henyii.com', 'Buddhism'),
('2024004', 'Lim Mei Ling', '林美玲', '4B', 'F', 'Chinese', 'meiling@student.henyii.com', 'Christianity'),
('2024005', 'Raj Kumar', '拉吉', '4C', 'M', 'Indian', 'raj@student.henyii.com', 'Hinduism'),
('2024006', 'Priya Devi', '普丽雅', '4C', 'F', 'Indian', 'priya@student.henyii.com', 'Hinduism'),
('2024007', 'John Smith', '约翰', '5A', 'M', 'Others', 'john@student.henyii.com', 'Christianity'),
('2024008', 'Sarah Johnson', '莎拉', '5A', 'F', 'Others', 'sarah@student.henyii.com', 'Christianity');

-- 4. 创建示例活动数据
INSERT INTO activities (title, date, location, organizer_id, description, event_type, organizer, level, achievement, objectives, content, followup, visibility) VALUES
('2024年学术比赛', '2024-03-15', '学校礼堂', 1, '年度学术比赛，涵盖多个学科', 'Competition', '学术部', 'School', 'Champion', '["提高学生学术水平", "培养竞争意识"]', '["数学竞赛", "科学实验", "英语演讲"]', '["加强训练", "改进教学方法"]', 'public'),
('体育节2024', '2024-04-20', '学校操场', 2, '年度体育节活动', 'Sports', '体育部', 'School', 'Runner-up', '["促进身体健康", "培养团队精神"]', '["田径比赛", "球类运动", "趣味游戏"]', '["增加体育设施", "定期训练"]', 'public'),
('文化节表演', '2024-05-10', '文化中心', 3, '多元文化活动展示', 'Cultural', '文化部', 'District', 'Third Place', '["展示多元文化", "促进文化交流"]', '["传统舞蹈", "民族音乐", "文化展览"]', '["加强文化教育", "组织更多活动"]', 'public'),
('科学展览会', '2024-06-05', '科学实验室', 1, '学生科学项目展示', 'Exhibition', '科学部', 'State', 'Champion', '["激发科学兴趣", "培养创新能力"]', '["项目展示", "实验演示", "专家讲座"]', '["增加实验设备", "加强科学教育"]', 'public'),
('英语演讲比赛', '2024-07-12', '多媒体教室', 2, '英语口语能力比赛', 'Competition', '英语部', 'National', 'Runner-up', '["提高英语水平", "培养表达能力"]', '["演讲比赛", "辩论赛", "才艺表演"]', '["加强英语培训", "增加练习机会"]', 'public');

-- 5. 创建参与者数据
INSERT INTO participants (activity_id, student_id, class, gender, race, achievement) VALUES
(1, 1, '4A', 'M', 'Malay', 'Champion'),
(1, 3, '4B', 'M', 'Chinese', 'Runner-up'),
(1, 5, '4C', 'M', 'Indian', 'Third Place'),
(2, 2, '4A', 'F', 'Malay', 'Champion'),
(2, 4, '4B', 'F', 'Chinese', 'Runner-up'),
(2, 6, '4C', 'F', 'Indian', 'Third Place'),
(3, 1, '4A', 'M', 'Malay', 'Participation'),
(3, 3, '4B', 'M', 'Chinese', 'Participation'),
(3, 5, '4C', 'M', 'Indian', 'Participation'),
(4, 2, '4A', 'F', 'Malay', 'Champion'),
(4, 4, '4B', 'F', 'Chinese', 'Runner-up'),
(4, 6, '4C', 'F', 'Indian', 'Third Place'),
(5, 7, '5A', 'M', 'Others', 'Runner-up'),
(5, 8, '5A', 'F', 'Others', 'Participation');

-- 6. 创建组数据
INSERT INTO groups (name) VALUES
('学术组'),
('体育组'),
('文化组'),
('科学组'),
('英语组');

-- 7. 添加组成员（确保用户ID存在）
INSERT INTO group_members (group_id, user_id) VALUES
(1, 1), -- 管理员加入学术组
(2, 2), -- 校长加入体育组
(1, 4), -- 张老师加入学术组
(2, 5), -- 李老师加入体育组
(3, 6); -- 王老师加入文化组

-- 8. 创建日志数据
INSERT INTO logs (user_id, action, detail) VALUES
(1, 'login', '管理员登录系统'),
(2, 'login', '校长登录系统'),
(4, 'create_activity', '创建活动：2024年学术比赛'),
(5, 'create_activity', '创建活动：体育节2024'),
(6, 'create_activity', '创建活动：文化节表演'),
(1, 'approve_activity', '批准活动：2024年学术比赛'),
(2, 'approve_activity', '批准活动：体育节2024');

-- 更新活动统计
UPDATE activities SET 
    stat_malay_m = 2, stat_malay_f = 1,
    stat_chinese_m = 1, stat_chinese_f = 1,
    stat_indian_m = 1, stat_indian_f = 1,
    stat_others_m = 1, stat_others_f = 1
WHERE id = 1;

UPDATE activities SET 
    stat_malay_f = 1,
    stat_chinese_f = 1,
    stat_indian_f = 1
WHERE id = 2;

UPDATE activities SET 
    stat_malay_m = 1, stat_malay_f = 0,
    stat_chinese_m = 1, stat_chinese_f = 0,
    stat_indian_m = 1, stat_indian_f = 0
WHERE id = 3;

UPDATE activities SET 
    stat_malay_f = 1,
    stat_chinese_f = 1,
    stat_indian_f = 1
WHERE id = 4;

UPDATE activities SET 
    stat_others_m = 1, stat_others_f = 1
WHERE id = 5;

-- 显示创建结果
SELECT 'Database setup completed successfully!' as status;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_activities FROM activities;
SELECT COUNT(*) as total_students FROM students;
SELECT COUNT(*) as total_participants FROM participants; 