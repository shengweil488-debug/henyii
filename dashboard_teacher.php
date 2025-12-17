<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once '../src/models/User.php';
require_once '../src/models/Activity.php';

// 检查用户是否已登录且是老师
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$currentUser = $user->findById($_SESSION['user_id']);

// 检查用户角色
if ($currentUser['role'] !== 'teacher') {
    header('Location: dashboard.php');
    exit();
}

// 设置语言
$lang = $_SESSION['lang'] ?? ($currentUser['language'] ?? 'zh');
$langArr = require __DIR__ . '/lang/' . $lang . '.php';

// 获取活动数据
$allActivities = Activity::all();

// 处理搜索和筛选
$search = $_GET['search'] ?? '';
$levelFilter = $_GET['level'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// 筛选活动
$filteredActivities = array_filter($allActivities, function($act) use ($search, $levelFilter, $statusFilter) {
    $matchesSearch = empty($search) || 
                    stripos($act['title'], $search) !== false || 
                    stripos($act['location'], $search) !== false;
    
    $matchesLevel = empty($levelFilter) || $act['level'] === $levelFilter;
    
    $matchesStatus = empty($statusFilter);
    if (!empty($statusFilter)) {
        $activityDate = new DateTime($act['date']);
        $now = new DateTime();
        if ($statusFilter === 'upcoming' && $activityDate > $now) {
            $matchesStatus = true;
        } elseif ($statusFilter === 'ongoing' && $activityDate == $now->format('Y-m-d')) {
            $matchesStatus = true;
        } elseif ($statusFilter === 'ended' && $activityDate < $now) {
            $matchesStatus = true;
        }
    }
    
    return $matchesSearch && $matchesLevel && $matchesStatus;
});

// 分页
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$totalActivities = count($filteredActivities);
$totalPages = ceil($totalActivities / $perPage);
$offset = ($page - 1) * $perPage;
$paginatedActivities = array_slice($filteredActivities, $offset, $perPage);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $langArr['teacher_dashboard'] ?? '教师仪表板'; ?> - 活动管理系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .teacher-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(44,62,80,0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .activity-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(44,62,80,0.15);
        }
        
        .activity-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .activity-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .activity-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .activity-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-upcoming {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-ongoing {
            background: #e8f5e8;
            color: #388e3c;
        }
        
        .status-ended {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .filters-section {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        
        .create-activity-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .create-activity-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        @media (max-width: 768px) {
            .activity-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- 顶部导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-chalkboard-teacher me-2"></i>
                <?php echo $langArr['teacher_dashboard'] ?? '教师仪表板'; ?>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($currentUser['username']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dashboard.php">
                            <i class="fas fa-home me-2"></i><?php echo $langArr['home'] ?? '首页'; ?>
                        </a></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i><?php echo $langArr['logout'] ?? '登出'; ?>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- 欢迎头部 -->
        <div class="teacher-header rounded">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">
                            <i class="fas fa-chalkboard-teacher me-3"></i>
                            <?php echo $langArr['welcome_teacher'] ?? '欢迎，老师！'; ?>
                        </h1>
                        <p class="mb-0 opacity-75">
                            <?php echo $langArr['teacher_dashboard_desc'] ?? '管理您的活动，查看所有活动信息'; ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="activity_create_handler.php" class="create-activity-btn">
                            <i class="fas fa-plus"></i>
                            <?php echo $langArr['create_activity'] ?? '创建活动'; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 统计卡片 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($allActivities); ?></div>
                <div class="stat-label">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?php echo $langArr['total_activities'] ?? '总活动数'; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $upcomingCount = 0;
                    $now = new DateTime();
                    foreach ($allActivities as $act) {
                        $activityDate = new DateTime($act['date']);
                        if ($activityDate > $now) $upcomingCount++;
                    }
                    echo $upcomingCount;
                    ?>
                </div>
                <div class="stat-label">
                    <i class="fas fa-clock me-2"></i>
                    <?php echo $langArr['upcoming_activities'] ?? '即将开始'; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $ongoingCount = 0;
                    foreach ($allActivities as $act) {
                        $activityDate = new DateTime($act['date']);
                        if ($activityDate->format('Y-m-d') === $now->format('Y-m-d')) $ongoingCount++;
                    }
                    echo $ongoingCount;
                    ?>
                </div>
                <div class="stat-label">
                    <i class="fas fa-play-circle me-2"></i>
                    <?php echo $langArr['ongoing_activities'] ?? '进行中'; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $endedCount = 0;
                    foreach ($allActivities as $act) {
                        $activityDate = new DateTime($act['date']);
                        if ($activityDate < $now) $endedCount++;
                    }
                    echo $endedCount;
                    ?>
                </div>
                <div class="stat-label">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $langArr['completed_activities'] ?? '已完成'; ?>
                </div>
            </div>
        </div>

        <!-- 筛选器 -->
        <div class="filters-section">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-search me-2"></i><?php echo $langArr['search'] ?? '搜索'; ?>
                    </label>
                    <input type="text" class="form-control" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="<?php echo $langArr['search_placeholder'] ?? '搜索活动标题或地点...'; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-layer-group me-2"></i><?php echo $langArr['level'] ?? '级别'; ?>
                    </label>
                    <select class="form-select" name="level">
                        <option value=""><?php echo $langArr['all_levels'] ?? '全部级别'; ?></option>
                        <option value="School" <?php echo $levelFilter === 'School' ? 'selected' : ''; ?>>School</option>
                        <option value="District" <?php echo $levelFilter === 'District' ? 'selected' : ''; ?>>District</option>
                        <option value="State" <?php echo $levelFilter === 'State' ? 'selected' : ''; ?>>State</option>
                        <option value="National" <?php echo $levelFilter === 'National' ? 'selected' : ''; ?>>National</option>
                        <option value="International" <?php echo $levelFilter === 'International' ? 'selected' : ''; ?>>International</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-filter me-2"></i><?php echo $langArr['status'] ?? '状态'; ?>
                    </label>
                    <select class="form-select" name="status">
                        <option value=""><?php echo $langArr['all_status'] ?? '全部状态'; ?></option>
                        <option value="upcoming" <?php echo $statusFilter === 'upcoming' ? 'selected' : ''; ?>>
                            <?php echo $langArr['upcoming'] ?? '即将开始'; ?>
                        </option>
                        <option value="ongoing" <?php echo $statusFilter === 'ongoing' ? 'selected' : ''; ?>>
                            <?php echo $langArr['ongoing'] ?? '进行中'; ?>
                        </option>
                        <option value="ended" <?php echo $statusFilter === 'ended' ? 'selected' : ''; ?>>
                            <?php echo $langArr['ended'] ?? '已结束'; ?>
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i><?php echo $langArr['search'] ?? '搜索'; ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- 活动列表 -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>
                        <i class="fas fa-list me-2"></i>
                        <?php echo $langArr['all_activities'] ?? '全部活动'; ?>
                        <span class="badge bg-primary ms-2"><?php echo $totalActivities; ?></span>
                    </h3>
                    <a href="activity_create_handler.php" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i><?php echo $langArr['create_activity'] ?? '创建活动'; ?>
                    </a>
                </div>

                <?php if (empty($paginatedActivities)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h4><?php echo $langArr['no_activities_found'] ?? '没有找到活动'; ?></h4>
                        <p><?php echo $langArr['no_activities_desc'] ?? '尝试调整搜索条件或创建新活动'; ?></p>
                        <a href="activity_create_handler.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i><?php echo $langArr['create_first_activity'] ?? '创建第一个活动'; ?>
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($paginatedActivities as $activity): ?>
                        <?php
                        $activityDate = new DateTime($activity['date']);
                        $now = new DateTime();
                        $status = '';
                        $statusClass = '';
                        
                        if ($activityDate > $now) {
                            $status = $langArr['upcoming'] ?? '即将开始';
                            $statusClass = 'status-upcoming';
                        } elseif ($activityDate->format('Y-m-d') === $now->format('Y-m-d')) {
                            $status = $langArr['ongoing'] ?? '进行中';
                            $statusClass = 'status-ongoing';
                        } else {
                            $status = $langArr['ended'] ?? '已结束';
                            $statusClass = 'status-ended';
                        }
                        ?>
                        
                        <div class="activity-card">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="activity-title">
                                        <a href="activity_detail.php?id=<?php echo $activity['id']; ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($activity['title']); ?>
                                        </a>
                                    </div>
                                    <div class="activity-meta">
                                        <div class="activity-meta-item">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo $activityDate->format('Y-m-d'); ?>
                                        </div>
                                        <div class="activity-meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($activity['location']); ?>
                                        </div>
                                        <div class="activity-meta-item">
                                            <i class="fas fa-layer-group"></i>
                                            <?php echo htmlspecialchars($activity['level'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="activity-meta-item">
                                            <i class="fas fa-user"></i>
                                            <?php echo htmlspecialchars($activity['organizer_name'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="activity-status <?php echo $statusClass; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                    <div class="mt-2">
                                        <a href="activity_detail.php?id=<?php echo $activity['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i><?php echo $langArr['view'] ?? '查看'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- 分页 -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination-container">
                            <nav>
                                <ul class="pagination">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 添加一些交互效果
        document.addEventListener('DOMContentLoaded', function() {
            // 卡片悬停效果
            const cards = document.querySelectorAll('.activity-card, .stat-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // 平滑滚动
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html> 