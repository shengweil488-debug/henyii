-- =====================================================
-- 恒毅活动报告系统 - 完整数据库设置
-- Heng Ee Activity Report System - Complete Database Setup
-- 版本: v1.0.0
-- 日期: 2025-01-27
-- =====================================================

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

-- =====================================================
-- 1. 用户表 (users)
-- =====================================================
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

-- =====================================================
-- 2. 活动表 (activities)
-- =====================================================
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

-- =====================================================
-- 3. 学生表 (students)
-- =====================================================
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

-- =====================================================
-- 4. 参与者表 (participants)
-- =====================================================
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

-- =====================================================
-- 5. 组表 (groups)
-- =====================================================
CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- =====================================================
-- 6. 组成员表 (group_members)
-- =====================================================
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

-- =====================================================
-- 7. 日志表 (logs)
-- =====================================================
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

-- =====================================================
-- 8. 活动文件表 (activity_files)
-- =====================================================
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

-- =====================================================
-- 插入初始数据
-- =====================================================

-- 1. 创建用户账号 (密码都是: password)
INSERT INTO users (username, password, role, language, email, name) VALUES
-- 超级管理员
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', 'zh', 'admin@henyii.com', '系统管理员'),
-- 校长
('principal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'zh', 'principal@henyii.com', '校长'),
-- 教师账号
('teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'zh', 'teacher1@henyii.com', '张老师'),
('teacher2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'en', 'teacher2@henyii.com', '李老师'),
('teacher3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'ms', 'teacher3@henyii.com', '王老师'),
('teacher4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'zh', 'teacher4@henyii.com', '陈老师'),
('teacher5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'en', 'teacher5@henyii.com', '刘老师');

-- 2. 创建学生数据
INSERT INTO students (student_no, name, chinese_name, class, gender, race, email, religion) VALUES
-- 马来族学生
('2024001', 'Ahmad bin Ali', '阿末', '4A', 'M', 'Malay', 'ahmad@student.henyii.com', 'Islam'),
('2024002', 'Siti binti Hassan', '西蒂', '4A', 'F', 'Malay', 'siti@student.henyii.com', 'Islam'),
('2024003', 'Mohammad bin Ibrahim', '莫哈末', '4B', 'M', 'Malay', 'mohammad@student.henyii.com', 'Islam'),
('2024004', 'Fatimah binti Omar', '法蒂玛', '4B', 'F', 'Malay', 'fatimah@student.henyii.com', 'Islam'),
('2024005', 'Zulkifli bin Ahmad', '祖基菲里', '5A', 'M', 'Malay', 'zulkifli@student.henyii.com', 'Islam'),
('2024006', 'Aminah binti Yusof', '阿米娜', '5A', 'F', 'Malay', 'aminah@student.henyii.com', 'Islam'),

-- 华族学生
('2024007', 'Tan Wei Ming', '陈伟明', '4A', 'M', 'Chinese', 'weiming@student.henyii.com', 'Buddhism'),
('2024008', 'Lim Mei Ling', '林美玲', '4A', 'F', 'Chinese', 'meiling@student.henyii.com', 'Christianity'),
('2024009', 'Wong Chee Keong', '黄志强', '4B', 'M', 'Chinese', 'cheekeong@student.henyii.com', 'Buddhism'),
('2024010', 'Chan Siew Mei', '陈秀美', '4B', 'F', 'Chinese', 'siewmei@student.henyii.com', 'Christianity'),
('2024011', 'Lee Kian Seng', '李建生', '5A', 'M', 'Chinese', 'kianseng@student.henyii.com', 'Buddhism'),
('2024012', 'Ng Pei Lin', '黄佩琳', '5A', 'F', 'Chinese', 'peilin@student.henyii.com', 'Christianity'),

-- 印族学生
('2024013', 'Raj Kumar', '拉吉', '4A', 'M', 'Indian', 'raj@student.henyii.com', 'Hinduism'),
('2024014', 'Priya Devi', '普丽雅', '4A', 'F', 'Indian', 'priya@student.henyii.com', 'Hinduism'),
('2024015', 'Suresh Ramasamy', '苏雷什', '4B', 'M', 'Indian', 'suresh@student.henyii.com', 'Hinduism'),
('2024016', 'Kavitha Raj', '卡维塔', '4B', 'F', 'Indian', 'kavitha@student.henyii.com', 'Hinduism'),
('2024017', 'Arun Kumar', '阿伦', '5A', 'M', 'Indian', 'arun@student.henyii.com', 'Hinduism'),
('2024018', 'Deepa Singh', '迪帕', '5A', 'F', 'Indian', 'deepa@student.henyii.com', 'Hinduism'),

-- 其他族裔学生
('2024019', 'John Smith', '约翰', '4A', 'M', 'Others', 'john@student.henyii.com', 'Christianity'),
('2024020', 'Sarah Johnson', '莎拉', '4A', 'F', 'Others', 'sarah@student.henyii.com', 'Christianity'),
('2024021', 'David Wilson', '大卫', '4B', 'M', 'Others', 'david@student.henyii.com', 'Christianity'),
('2024022', 'Emily Brown', '艾米丽', '4B', 'F', 'Others', 'emily@student.henyii.com', 'Christianity');

-- 3. 创建组数据
INSERT INTO groups (name) VALUES
('学术组'),
('体育组'),
('文化组'),
('科学组'),
('英语组'),
('数学组'),
('艺术组'),
('技术组');

-- 4. 创建活动数据
INSERT INTO activities (title, date, location, organizer_id, description, event_type, organizer, level, achievement, objectives, content, followup, visibility, approval_status) VALUES
-- 校级活动
('2024年学术比赛', '2024-03-15', '学校礼堂', 1, '年度学术比赛，涵盖多个学科', 'Competition', '学术部', 'School', 'Champion', '["提高学生学术水平", "培养竞争意识", "促进学科交流"]', '["数学竞赛", "科学实验", "英语演讲", "华文作文"]', '["加强训练", "改进教学方法", "增加练习机会"]', 'public', 'approved'),
('体育节2024', '2024-04-20', '学校操场', 2, '年度体育节活动', 'Sports', '体育部', 'School', 'Runner-up', '["促进身体健康", "培养团队精神", "提高运动技能"]', '["田径比赛", "球类运动", "趣味游戏", "接力赛"]', '["增加体育设施", "定期训练", "组织更多体育活动"]', 'public', 'approved'),
('文化节表演', '2024-05-10', '文化中心', 3, '多元文化活动展示', 'Cultural', '文化部', 'School', 'Third Place', '["展示多元文化", "促进文化交流", "培养艺术兴趣"]', '["传统舞蹈", "民族音乐", "文化展览", "戏剧表演"]', '["加强文化教育", "组织更多活动", "邀请专业表演者"]', 'public', 'approved'),

-- 区级活动
('区际科学展览会', '2024-06-05', '科学实验室', 1, '学生科学项目展示', 'Exhibition', '科学部', 'District', 'Champion', '["激发科学兴趣", "培养创新能力", "展示科学成果"]', '["项目展示", "实验演示", "专家讲座", "互动体验"]', '["增加实验设备", "加强科学教育", "建立科学俱乐部"]', 'public', 'approved'),
('区际英语演讲比赛', '2024-07-12', '多媒体教室', 2, '英语口语能力比赛', 'Competition', '英语部', 'District', 'Runner-up', '["提高英语水平", "培养表达能力", "增强自信心"]', '["演讲比赛", "辩论赛", "才艺表演", "即兴演讲"]', '["加强英语培训", "增加练习机会", "组织英语角"]', 'public', 'approved'),

-- 州级活动
('州际数学奥林匹克', '2024-08-25', '州教育中心', 3, '数学能力竞赛', 'Competition', '数学部', 'State', 'Champion', '["提高数学能力", "培养逻辑思维", "选拔优秀学生"]', '["数学竞赛", "解题技巧", "团队合作", "个人挑战"]', '["加强数学培训", "建立数学俱乐部", "参加更多比赛"]', 'public', 'approved'),
('州际艺术节', '2024-09-15', '州艺术中心', 4, '艺术创作展示', 'Exhibition', '艺术部', 'State', 'Third Place', '["展示艺术才华", "培养创造力", "促进艺术交流"]', '["绘画比赛", "手工艺术", "摄影展", "音乐表演"]', '["加强艺术教育", "提供更多创作机会", "邀请艺术家指导"]', 'public', 'approved'),

-- 国家级活动
('全国科学创新大赛', '2024-10-20', '国家科学中心', 5, '全国科学创新项目比赛', 'Competition', '科学部', 'National', 'Runner-up', '["培养创新精神", "提高科学素养", "展示创新成果"]', '["创新项目展示", "科学实验", "专家评审", "现场答辩"]', '["加强创新教育", "建立创新实验室", "参加国际比赛"]', 'public', 'approved'),
('全国华文写作比赛', '2024-11-08', '国家文化中心', 6, '华文写作能力比赛', 'Competition', '华文部', 'National', 'Champion', '["提高华文水平", "培养写作能力", "传承中华文化"]', '["作文比赛", "诗歌创作", "散文写作", "文学评论"]', '["加强华文教育", "组织写作工作坊", "出版学生作品"]', 'public', 'approved'),

-- 国际级活动
('国际数学竞赛', '2024-12-15', '国际会议中心', 7, '国际数学奥林匹克竞赛', 'Competition', '数学部', 'International', 'Participation', '["参与国际竞争", "提高数学水平", "促进国际交流"]', '["数学竞赛", "国际交流", "文化体验", "友谊建立"]', '["加强国际交流", "提高竞赛水平", "建立国际伙伴关系"]', 'public', 'approved');

-- 5. 创建参与者数据
INSERT INTO participants (activity_id, student_id, class, gender, race, achievement) VALUES
-- 学术比赛参与者
(1, 1, '4A', 'M', 'Malay', 'Champion'),
(1, 7, '4A', 'M', 'Chinese', 'Runner-up'),
(1, 13, '4A', 'M', 'Indian', 'Third Place'),
(1, 19, '4A', 'M', 'Others', 'Participation'),

-- 体育节参与者
(2, 2, '4A', 'F', 'Malay', 'Champion'),
(2, 8, '4A', 'F', 'Chinese', 'Runner-up'),
(2, 14, '4A', 'F', 'Indian', 'Third Place'),
(2, 20, '4A', 'F', 'Others', 'Participation'),

-- 文化节参与者
(3, 3, '4B', 'M', 'Malay', 'Participation'),
(3, 9, '4B', 'M', 'Chinese', 'Participation'),
(3, 15, '4B', 'M', 'Indian', 'Participation'),
(3, 21, '4B', 'M', 'Others', 'Participation'),

-- 科学展览会参与者
(4, 4, '4B', 'F', 'Malay', 'Champion'),
(4, 10, '4B', 'F', 'Chinese', 'Runner-up'),
(4, 16, '4B', 'F', 'Indian', 'Third Place'),
(4, 22, '4B', 'F', 'Others', 'Participation'),

-- 英语演讲比赛参与者
(5, 5, '5A', 'M', 'Malay', 'Runner-up'),
(5, 11, '5A', 'M', 'Chinese', 'Participation'),
(5, 17, '5A', 'M', 'Indian', 'Participation'),
(5, 6, '5A', 'F', 'Malay', 'Participation'),

-- 数学奥林匹克参与者
(6, 12, '5A', 'F', 'Chinese', 'Champion'),
(6, 18, '5A', 'F', 'Indian', 'Runner-up'),

-- 艺术节参与者
(7, 1, '4A', 'M', 'Malay', 'Third Place'),
(7, 7, '4A', 'M', 'Chinese', 'Participation'),
(7, 13, '4A', 'M', 'Indian', 'Participation'),

-- 科学创新大赛参与者
(8, 2, '4A', 'F', 'Malay', 'Runner-up'),
(8, 8, '4A', 'F', 'Chinese', 'Participation'),
(8, 14, '4A', 'F', 'Indian', 'Participation'),

-- 华文写作比赛参与者
(9, 3, '4B', 'M', 'Malay', 'Champion'),
(9, 9, '4B', 'M', 'Chinese', 'Participation'),
(9, 15, '4B', 'M', 'Indian', 'Participation'),

-- 国际数学竞赛参与者
(10, 4, '4B', 'F', 'Malay', 'Participation'),
(10, 10, '4B', 'F', 'Chinese', 'Participation'),
(10, 16, '4B', 'F', 'Indian', 'Participation');

-- 6. 添加组成员
INSERT INTO group_members (group_id, user_id) VALUES
-- 管理员和校长
(1, 1), -- 管理员加入学术组
(2, 2), -- 校长加入体育组
(3, 1), -- 管理员加入文化组
(4, 1), -- 管理员加入科学组

-- 教师加入各组
(1, 3), -- 张老师加入学术组
(2, 4), -- 李老师加入体育组
(3, 5), -- 王老师加入文化组
(4, 6), -- 陈老师加入科学组
(5, 7), -- 刘老师加入英语组
(6, 3), -- 张老师加入数学组
(7, 5), -- 王老师加入艺术组
(8, 6); -- 陈老师加入技术组

-- 7. 创建日志数据
INSERT INTO logs (user_id, action, detail) VALUES
(1, 'login', '管理员登录系统'),
(2, 'login', '校长登录系统'),
(3, 'login', '张老师登录系统'),
(4, 'login', '李老师登录系统'),
(5, 'login', '王老师登录系统'),
(1, 'create_activity', '创建活动：2024年学术比赛'),
(2, 'create_activity', '创建活动：体育节2024'),
(3, 'create_activity', '创建活动：文化节表演'),
(4, 'create_activity', '创建活动：区际科学展览会'),
(5, 'create_activity', '创建活动：区际英语演讲比赛'),
(1, 'approve_activity', '批准活动：2024年学术比赛'),
(2, 'approve_activity', '批准活动：体育节2024'),
(1, 'approve_activity', '批准活动：文化节表演'),
(2, 'approve_activity', '批准活动：区际科学展览会'),
(1, 'approve_activity', '批准活动：区际英语演讲比赛'),
(3, 'add_participant', '添加参与者到学术比赛'),
(4, 'add_participant', '添加参与者到体育节'),
(5, 'add_participant', '添加参与者到文化节');

-- 8. 更新活动统计
UPDATE activities SET 
    stat_malay_m = 3, stat_malay_f = 3,
    stat_chinese_m = 3, stat_chinese_f = 3,
    stat_indian_m = 3, stat_indian_f = 3,
    stat_others_m = 2, stat_others_f = 2
WHERE id = 1;

UPDATE activities SET 
    stat_malay_f = 2,
    stat_chinese_f = 2,
    stat_indian_f = 2,
    stat_others_f = 2
WHERE id = 2;

UPDATE activities SET 
    stat_malay_m = 1, stat_malay_f = 0,
    stat_chinese_m = 1, stat_chinese_f = 0,
    stat_indian_m = 1, stat_indian_f = 0,
    stat_others_m = 1, stat_others_f = 0
WHERE id = 3;

UPDATE activities SET 
    stat_malay_f = 1,
    stat_chinese_f = 1,
    stat_indian_f = 1,
    stat_others_f = 1
WHERE id = 4;

UPDATE activities SET 
    stat_malay_m = 1, stat_malay_f = 1,
    stat_chinese_m = 1, stat_chinese_f = 0,
    stat_indian_m = 1, stat_indian_f = 0,
    stat_others_m = 0, stat_others_f = 0
WHERE id = 5;

UPDATE activities SET 
    stat_malay_m = 0, stat_malay_f = 0,
    stat_chinese_f = 1,
    stat_indian_f = 1,
    stat_others_m = 0, stat_others_f = 0
WHERE id = 6;

UPDATE activities SET 
    stat_malay_m = 1, stat_malay_f = 0,
    stat_chinese_m = 1, stat_chinese_f = 0,
    stat_indian_m = 1, stat_indian_f = 0,
    stat_others_m = 0, stat_others_f = 0
WHERE id = 7;

UPDATE activities SET 
    stat_malay_f = 1,
    stat_chinese_f = 1,
    stat_indian_f = 1,
    stat_others_m = 0, stat_others_f = 0
WHERE id = 8;

UPDATE activities SET 
    stat_malay_m = 1, stat_malay_f = 0,
    stat_chinese_m = 1, stat_chinese_f = 0,
    stat_indian_m = 1, stat_indian_f = 0,
    stat_others_m = 0, stat_others_f = 0
WHERE id = 9;

UPDATE activities SET 
    stat_malay_f = 1,
    stat_chinese_f = 1,
    stat_indian_f = 1,
    stat_others_m = 0, stat_others_f = 0
WHERE id = 10;

-- =====================================================
-- 显示创建结果
-- =====================================================
SELECT 'Database setup completed successfully!' as status;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_activities FROM activities;
SELECT COUNT(*) as total_students FROM students;
SELECT COUNT(*) as total_participants FROM participants;
SELECT COUNT(*) as total_groups FROM groups;
SELECT COUNT(*) as total_group_members FROM group_members;
SELECT COUNT(*) as total_logs FROM logs;

-- =====================================================
-- 显示默认账号信息
-- =====================================================
SELECT 'Default Account Information:' as info;
SELECT username, role, name FROM users ORDER BY id; 