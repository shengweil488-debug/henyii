<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/Activity.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$currentUser = $user->findById($_SESSION['user_id']);

// 设置语言
$lang = $_SESSION['lang'] ?? ($currentUser['language'] ?? 'en');
$langArr = require __DIR__ . '/lang/' . $lang . '.php';

// 获取统计数据
$activity = new Activity();
$totalActivities = $activity->getTotalActivities();

// 处理搜索过滤
$selectedLevel = $_GET['level'] ?? '';
$recentActivities = $activity->getRecentActivities(5, $selectedLevel);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $langArr['home'] ?? 'Dashboard'; ?> - 活动管理系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
            min-height: 100vh;
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
            color: #232946;
            transition: background 0.5s, color 0.5s;
        }
        body.dark-mode {
            background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            border-radius: 0 0 32px 32px;
            box-shadow: 0 4px 24px rgba(80,80,200,0.08);
            padding: 18px 40px 10px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.5s;
        }
        body.dark-mode .topbar {
            background: rgba(35,41,70,0.92);
        }
        .topbar .brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #7f7fd5;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .topbar .brand i {
            font-size: 2rem;
        }
        .topbar .actions {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .night-toggle {
            border: none;
            background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
            color: #fff;
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 8px 22px;
            box-shadow: 0 2px 8px rgba(120,120,255,0.10);
            transition: filter 0.2s, transform 0.2s, background 0.5s;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .night-toggle:hover {
            filter: brightness(1.10);
            transform: scale(1.04);
        }
        .dashboard-container {
            padding: 30px 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .dashboard-bg-decor {
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            pointer-events: none;
            z-index: 0;
        }
        .dashboard-bg-decor .circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(32px);
            opacity: 0.22;
            background: radial-gradient(circle at 60% 40%, #a1c4fd 0%, #7f7fd5 80%, transparent 100%);
        }
        .dashboard-bg-decor .circle1 {
            width: 420px; height: 420px; right: -120px; top: 10vh;
        }
        .dashboard-bg-decor .circle2 {
            width: 260px; height: 260px; left: -80px; bottom: 8vh;
            background: radial-gradient(circle at 40% 60%, #86a8e7 0%, #91eac9 80%, transparent 100%);
        }
        .dashboard-content-area {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            z-index: 1;
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            justify-items: center;
            width: 100%;
        }
        .skeleton-block {
            min-height: 120px;
            border-radius: 18px;
            margin-bottom: 32px;
        }
        .skeleton-table {
            min-height: 220px;
            border-radius: 18px;
            margin-bottom: 32px;
        }
        .fade-in {
            opacity: 0;
            transform: translateY(24px) scale(0.98);
            animation: fadeInUp 0.7s cubic-bezier(.4,2,.6,1) 0.1s forwards;
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: none; }
        }
        .skeleton {
            background: linear-gradient(90deg, #e3eaf2 25%, #f3f6fa 50%, #e3eaf2 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.2s infinite linear;
            border-radius: 8px;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .feature-card, .stats-card, .welcome-card, .table, .logout-btn {
            opacity: 0;
            transform: translateY(24px) scale(0.98);
            animation: fadeInUp 0.7s cubic-bezier(.4,2,.6,1) 0.1s forwards;
        }
        .feature-card { animation-delay: 0.18s; }
        .stats-card { animation-delay: 0.22s; }
        .welcome-card { animation-delay: 0.13s; }
        .table { animation-delay: 0.25s; }
        .logout-btn { animation-delay: 0.05s; }
        .feature-card, .stats-card, .welcome-card {
            transition: box-shadow 0.25s, transform 0.25s, background 0.5s;
        }
        .feature-card:hover, .stats-card:hover, .welcome-card:hover {
            box-shadow: 0 16px 48px rgba(80,80,200,0.18);
            transform: translateY(-6px) scale(1.03);
            background: rgba(255,255,255,0.97);
        }
        body.dark-mode .feature-card, body.dark-mode .stats-card, body.dark-mode .welcome-card {
            background: rgba(35,41,70,0.92);
            color: #f3f6fa;
        }
        .feature-icon {
            background: linear-gradient(135deg, #7f7fd5 0%, #86a8e7 50%, #91eac9 100%);
            color: #fff;
            border-radius: 50%;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 18px;
            box-shadow: 0 4px 16px rgba(120,120,255,0.10);
            transition: box-shadow 0.2s, filter 0.2s;
        }
        .feature-card:hover .feature-icon {
            box-shadow: 0 8px 32px rgba(120,120,255,0.18);
            filter: brightness(1.08);
        }
        .feature-card h5 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #3a3a6a;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .feature-card p {
            color: #7b7b9d;
            font-size: 1.02rem;
            margin-bottom: 18px;
        }
        .btn-feature {
            background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
            color: #fff;
            border: none;
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 10px 32px;
            margin-top: 8px;
            box-shadow: 0 2px 8px rgba(120,120,255,0.10);
            transition: filter 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-feature:hover {
            filter: brightness(1.10);
            transform: scale(1.04);
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        .stats-card:hover::before {
            left: 100%;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(31, 38, 135, 0.25);
        }
        .stats-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .stats-label {
            font-size: 1.1rem;
            color: #6c757d;
            font-weight: 500;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        .feature-card:hover::before {
            left: 100%;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(31, 38, 135, 0.25);
            text-decoration: none;
            color: inherit;
        }
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .feature-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        .feature-description {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .btn-feature {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .btn-feature:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        .recent-activities {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-item:hover {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            padding-left: 15px;
            padding-right: 15px;
            margin-left: -15px;
            margin-right: -15px;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            font-size: 1.1rem;
        }
        .activity-content {
            flex: 1;
            margin-left: 15px;
        }
        .activity-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        .activity-time {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 20px rgba(31, 38, 135, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-link {
            font-weight: 500;
            color: #2c3e50;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 16px;
        }
        .nav-link:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        .btn-logout {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
            color: white;
        }
        .night-mode-toggle {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.2);
            color: #667eea;
            padding: 8px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .night-mode-toggle:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: scale(1.05);
        }
        body.dark-mode {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            color: #e3f2fd;
        }
        body.dark-mode .dashboard-container {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
        }
        body.dark-mode .stats-card,
        body.dark-mode .feature-card,
        body.dark-mode .recent-activities {
            background: rgba(26, 26, 46, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e3f2fd;
        }
        /* 新增：夜间模式下表格字体高对比度和阴影 */
        body.dark-mode .table,
        body.dark-mode .table-hover,
        body.dark-mode .table-light,
        body.dark-mode .table th,
        body.dark-mode .table td {
            color: #f8fafd !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.28);
            background: transparent !important;
        }
        body.dark-mode .table-light th,
        body.dark-mode .table-light td {
            background: rgba(35,41,70,0.92) !important;
        }
        /* 夜间模式下badge对比度提升 */
        body.dark-mode .badge.bg-warning {
            color: #232946;
            background: #ffe066 !important;
            text-shadow: none;
        }
        body.dark-mode .badge.bg-secondary {
            color: #fff;
            background: #6c757d !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.18);
        }
        body.dark-mode .badge.bg-success {
            color: #fff;
            background: #43e97b !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.18);
        }
        body.dark-mode .stats-number {
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        body.dark-mode .stats-label {
            color: #e3f2fd;
            font-weight: 500;
        }
        body.dark-mode .feature-title {
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        body.dark-mode .feature-description {
            color: #e3f2fd;
            font-weight: 400;
        }
        body.dark-mode .activity-title {
            color: #ffffff;
            font-weight: 600;
        }
        body.dark-mode .activity-time {
            color: #e3f2fd;
            font-weight: 400;
        }
        body.dark-mode .navbar {
            background: rgba(26, 26, 46, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.dark-mode .nav-link {
            color: #ffffff;
            font-weight: 500;
        }
        body.dark-mode .nav-link:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        body.dark-mode .navbar-brand {
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        body.dark-mode .activity-item {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        body.dark-mode .activity-item:hover {
            background: rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body>
    <!-- 移除全局 loading 遮罩、骨架屏、淡出动画相关 HTML -->
    <div class="topbar">
        <div class="brand">
            <i class="fas fa-chart-bar"></i> <?php echo $langArr['system_name'] ?? '活动管理系统'; ?>
        </div>
        <div class="actions">
            <button class="night-toggle" id="nightToggleBtn" title="夜间/白天模式">
                <i class="fas fa-moon"></i>
            </button>
            <div class="logout-btn fade-in">
        <a href="logout.php" class="btn btn-outline-light">
            <i class="fas fa-sign-out-alt"></i> <?php echo $langArr['logout']; ?>
        </a>
            </div>
        </div>
    </div>

    <div class="container dashboard-container" id="dashboardContent" style="opacity:1 !important;display:block !important;visibility:visible !important;min-height:400px;">
        <div class="dashboard-bg-decor">
            <div class="circle circle1"></div>
            <div class="circle circle2"></div>
        </div>
        <div class="dashboard-content-area">
            <!-- 真正内容（原有内容） -->
            <div id="dashboardRealContent" style="opacity:1 !important;display:block !important;visibility:visible !important;">
                <div class="welcome-card p-4 mb-5 fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-3" style="color:#7f7fd5;font-weight:800;letter-spacing:2px;">
                        <i class="fas fa-tachometer-alt me-3"></i><?php echo $langArr['welcome']; ?>
                    </h1>
                    <p class="lead mb-0" style="color:#5a5a7a;">
                        <?php echo $langArr['welcome']; ?>，<?php echo htmlspecialchars($currentUser['name'] ?? $currentUser['username']); ?>！
                    </p>
                </div>
                <div class="col-md-4 text-end">
                            <div class="stats-card p-3 fade-in">
                                <h3 class="mb-1" id="activity-count" style="color:#7f7fd5;font-weight:700;">
                                    <span class="skeleton" id="activity-count-skeleton" style="display:inline-block;width:48px;height:32px;"></span>
                                </h3>
                        <p class="mb-0" style="color:#7b7b9d;"><?php echo $langArr['activities']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-grid mb-5">
            <?php if ($currentUser['role'] !== 'teacher'): ?>
            <!-- 学生管理 -->
                    <a href="student_list.php" class="feature-card card-link">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h5><?php echo $langArr['student_manage'] ?? '学生管理'; ?></h5>
                <p><?php echo $langArr['student_manage_desc'] ?? '管理学生信息，导入学生数据'; ?></p>
                        <span class="btn btn-feature">
                    <i class="fas fa-arrow-right me-2"></i><?php echo $langArr['enter_manage'] ?? '进入管理'; ?>
                        </span>
                </a>
            <!-- 管理全部账号 -->
                    <a href="user_list.php" class="feature-card card-link">
                <div class="feature-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h5><?php echo $langArr['user_manage'] ?? '管理全部账号'; ?></h5>
                <p><?php echo $langArr['user_manage_desc'] ?? '管理系统中的所有用户账号'; ?></p>
                        <span class="btn btn-feature">
                    <i class="fas fa-arrow-right me-2"></i><?php echo $langArr['enter_manage'] ?? '进入管理'; ?>
                        </span>
                </a>
            <?php endif; ?>
            <!-- 全部活动管理 -->
                    <a href="activity_manage.php" class="feature-card card-link">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h5><?php echo $langArr['activity_manage'] ?? '全部活动管理'; ?></h5>
                <p><?php echo $langArr['activity_manage_desc'] ?? '查看和管理所有活动'; ?></p>
                        <span class="btn btn-feature">
                    <i class="fas fa-arrow-right me-2"></i><?php echo $langArr['enter_manage'] ?? '进入管理'; ?>
                        </span>
                </a>
            <!-- 创建活动 -->
                    <a href="activity_create.php" class="feature-card card-link">
                <div class="feature-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h5><?php echo $langArr['create_activity']; ?></h5>
                <p><?php echo $langArr['create_activity_desc'] ?? '创建新的活动'; ?></p>
                        <span class="btn btn-feature">
                    <i class="fas fa-arrow-right me-2"></i><?php echo $langArr['create_activity']; ?>
                        </span>
                </a>
            <!-- 活动数据分析 -->
                    <a href="activity_analysis.php" class="feature-card card-link">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h5><?php echo $langArr['activity_analysis'] ?? '活动数据分析'; ?></h5>
                <p><?php echo $langArr['activity_analysis_desc'] ?? '查看活动统计和分析'; ?></p>
                        <span class="btn btn-feature">
                    <i class="fas fa-arrow-right me-2"></i><?php echo $langArr['view_analysis'] ?? '查看分析'; ?>
                        </span>
                </a>
            <!-- 个人中心 -->
                    <a href="profile.php" class="feature-card card-link">
                <div class="feature-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h5><?php echo $langArr['profile'] ?? '个人中心'; ?></h5>
                <p><?php echo $langArr['profile_desc'] ?? '管理个人信息和设置'; ?></p>
                        <span class="btn btn-feature">
                    <i class="fas fa-arrow-right me-2"></i><?php echo $langArr['profile'] ?? '个人中心'; ?>
                        </span>
                </a>
        </div>

        <!-- 最近活动 -->
        <?php if (!empty($recentActivities)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="stats-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-clock me-2"></i><?php echo $langArr['recent_activities'] ?? '最近活动'; ?>
                        </h4>
                        <div class="d-flex align-items-center">
                           
                            <form method="GET" class="d-flex align-items-center">
                                <label class="me-2" style="color: #7b7b9d; font-weight: 600;"><?php echo $langArr['filter_by_level'] ?? '按级别筛选:'; ?></label>
                                <select name="level" class="form-select form-select-sm me-2" style="width: auto; border-radius: 12px; border: 1px solid #e3eaf2;">
                                    <option value=""><?php echo $langArr['all_levels'] ?? '全部级别'; ?></option>
                                    <option value="School" <?php echo $selectedLevel === 'School' ? 'selected' : ''; ?>><?php echo $langArr['school_level']; ?></option>
                                    <option value="Zone/Area" <?php echo $selectedLevel === 'Zone/Area' ? 'selected' : ''; ?>><?php echo $langArr['zone_area_level']; ?></option>
                                    <option value="State" <?php echo $selectedLevel === 'State' ? 'selected' : ''; ?>><?php echo $langArr['state_level']; ?></option>
                                    <option value="National" <?php echo $selectedLevel === 'National' ? 'selected' : ''; ?>><?php echo $langArr['national_level']; ?></option>
                                    <option value="International" <?php echo $selectedLevel === 'International' ? 'selected' : ''; ?>><?php echo $langArr['international_level']; ?></option>
                                </select>
                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%); color: #fff; border-radius: 12px; border: none; padding: 6px 16px;">
                                    <i class="fas fa-search me-1"></i><?php echo $langArr['search'] ?? '搜索'; ?>
                                </button>
                                <?php if (!empty($selectedLevel)): ?>
                                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm ms-2" style="border-radius: 12px;">
                                    <i class="fas fa-times me-1"></i><?php echo $langArr['clear'] ?? '清除'; ?>
                                </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo $langArr['activity'] ?? '活动名称'; ?></th>
                                    <th><?php echo $langArr['organizer'] ?? '创建者'; ?></th>
                                    <th>Level</th>
                                    <th><?php echo $langArr['status'] ?? '状态'; ?></th>
                                    <th><?php echo $langArr['date'] ?? '创建时间'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivities as $activity): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['creator_name']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['level'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                        $activityDate = strtotime($activity['date']);
                                        $currentDate = time();
                                        if ($activityDate > $currentDate) {
                                            echo '<span class="badge bg-warning">' . ($langArr['upcoming'] ?? '即将开始') . '</span>';
                                        } elseif ($activityDate < $currentDate) {
                                            echo '<span class="badge bg-secondary">' . ($langArr['ended'] ?? '已结束') . '</span>';
                                        } else {
                                            echo '<span class="badge bg-success">' . ($langArr['ongoing'] ?? '进行中') . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($activity['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (empty($recentActivities)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-search" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="mt-3" style="color: #7b7b9d;"><?php echo $langArr['no_activities_found'] ?? '没有找到符合条件的活动'; ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // 夜间模式切换
    function setDarkMode(on) {
        if(on) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('henyii_dark', '1');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('henyii_dark', '0');
        }
    }
    document.getElementById('nightToggleBtn').onclick = function() {
        setDarkMode(!document.body.classList.contains('dark-mode'));
    };
    // 自动读取夜间模式
    if(localStorage.getItem('henyii_dark')==='1') setDarkMode(true);

    // 页面离开淡出动画，避免白屏卡顿
    // 移除所有内容淡出和隐藏逻辑
    // document.addEventListener('DOMContentLoaded', function() {
    //     var content = document.getElementById('dashboardContent');
    //     function fadeOutAndGo(href) {
    //         content.style.transition = 'opacity 0.45s cubic-bezier(.4,2,.6,1)';
    //         content.style.opacity = 0;
    //         setTimeout(function() { window.location = href; }, 420);
    //     }
    //     // 所有功能按钮
    //     document.querySelectorAll('.btn-feature').forEach(function(btn) {
    //         btn.addEventListener('click', function(e) {
    //             var href = btn.getAttribute('href');
    //             if(href && href !== '#') {
    //                 e.preventDefault();
    //                 fadeOutAndGo(href);
    //             }
    //         });
    //     });
    //     // 退出按钮
    //     document.querySelectorAll('.logout-btn a').forEach(function(btn) {
    //         btn.addEventListener('click', function(e) {
    //             var href = btn.getAttribute('href');
    //             if(href && href !== '#') {
    //                 e.preventDefault();
    //                 fadeOutAndGo(href);
    //             }
    //         });
    //     });
    // });
    </script>
</body>
</html> 