<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once '../src/models/Student.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$student = new Student();
$students = $student->getAllStudents();

$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/lang/' . $lang . '.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $langArr['student_manage'] ?? '学生管理'; ?> - <?php echo $langArr['activities'] ?? '活动管理系统'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body.dark-mode {
            background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        body.dark-mode .main-card,
        body.dark-mode .content-card,
        body.dark-mode .card {
            background: rgba(35,41,70,0.92) !important;
            color: #f3f6fa !important;
            box-shadow:
                0 2.5px 12px 0 #7f7fd544,
                0 8px 32px 0 #7f7fd588,
                0 0 8px 2px #7f7fd5,
                0 0 0 1.5px #7f7fd544 inset,
                0 0.5px 0.5px 0 #fff2 inset;
            border-radius: 18px;
            border: 1.5px solid #7f7fd544;
            background-image: linear-gradient(180deg,rgba(120,120,255,0.10) 0%,transparent 60%);
        }
        body.dark-mode .main-card:hover,
        body.dark-mode .content-card:hover,
        body.dark-mode .card:hover {
            box-shadow:
                0 8px 32px 0 #a1c4fdcc,
                0 24px 64px 0 #23294699,
                0 0 32px 8px #7f7fd5,
                0 0 0 2.5px #a1c4fd inset,
                0 1.5px 1.5px 0 #fff2 inset;
        }
        body.dark-mode .table {
            background: #232946 !important;
            color: #f3f6fa !important;
            box-shadow: 0 2px 12px #7f7fd544, 0 0 0 1.5px #7f7fd5 inset;
            border-radius: 12px;
        }
        body.dark-mode th, body.dark-mode td {
            color: #f3f6fa !important;
        }
        body.dark-mode .table-hover tbody tr:hover {
            background: linear-gradient(90deg, #232946 0%, #393a5a 100%) !important;
            box-shadow: 0 2px 12px #7f7fd544;
        }
        body.dark-mode .btn, body.dark-mode .btn-feature {
            color: #fff !important;
            box-shadow: 0 2px 12px #7f7fd544, 0 0 0 2px #a1c4fd55 inset;
            border-radius: 22px;
        }
        .main-container {
            padding: 2rem 0;
        }
        .content-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 4px 24px rgba(160,132,238,0.08);
            border: 1px solid #e3eaf2;
        }
        .btn-custom {
            border-radius: 24px;
            padding: 0.8rem 2.2rem;
            font-weight: 600;
            border: none;
            background: linear-gradient(90deg, #6a82fb 0%, #a084ee 100%);
            color: #fff;
            box-shadow: 0 2px 12px rgba(160,132,238,0.10);
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-custom:hover {
            background: linear-gradient(90deg, #a084ee 0%, #6a82fb 100%);
            color: #fff;
            box-shadow: 0 4px 24px rgba(160,132,238,0.18);
            transform: translateY(-2px) scale(1.04);
        }
        .table-custom {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(160,132,238,0.06);
        }
        .table-custom th {
            background: linear-gradient(90deg, #6a82fb 0%, #a084ee 100%);
            color: #fff;
            border: none;
            font-weight: 600;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        /* 美化退出登录按钮 */
        .logout-btn a {
            background: linear-gradient(90deg, #6a82fb 0%, #a084ee 100%);
            color: #fff !important;
            border: none;
            border-radius: 28px;
            font-weight: 600;
            box-shadow: 0 2px 12px rgba(160,132,238,0.10);
            padding: 10px 28px;
            font-size: 1.08rem;
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logout-btn a:hover, .logout-btn a:focus {
            background: linear-gradient(90deg, #a084ee 0%, #6a82fb 100%);
            color: #fff !important;
            box-shadow: 0 4px 24px rgba(160,132,238,0.18);
            transform: translateY(-2px) scale(1.04);
            text-decoration: none;
        }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .btn-group-vertical-student {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
        }
        .main-btn-student {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 180px;
            padding: 14px 0;
            background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            border-radius: 32px;
            box-shadow: 0 2px 8px rgba(120,120,255,0.10);
            transition: filter 0.2s, transform 0.2s;
            text-decoration: none;
        }
        .main-btn-student:hover {
            filter: brightness(1.08);
            transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 900px) {
            .content-card { border-radius: 14px; }
            .table th, .table td { font-size: 0.97rem; }
        }
        @media (max-width: 600px) {
            .content-card { border-radius: 8px; }
            .table th, .table td { font-size: 0.93rem; }
        }
        .page-container {
            padding: 30px 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            margin-bottom: 30px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        .search-section {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(31, 38, 135, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .form-control {
            border-radius: 12px;
            border: 1.5px solid #e3f0fc;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }
        .btn-search {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .table {
            margin: 0;
            background: transparent;
        }
        .table th {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px 12px;
            font-size: 1rem;
        }
        .table td {
            padding: 15px 12px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            vertical-align: middle;
            font-size: 0.95rem;
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-edit {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            border: none;
            color: white;
            box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3);
        }
        .btn-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.4);
            color: white;
        }
        .btn-delete {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border: none;
            color: white;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
        }
        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
            color: white;
        }
        .pagination {
            justify-content: center;
            margin-top: 30px;
        }
        .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: none;
            color: #667eea;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .page-link:hover {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-1px);
        }
        .page-item.active .page-link {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
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
        body.dark-mode .page-container {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
        }
        body.dark-mode .content-card,
        body.dark-mode .search-section,
        body.dark-mode .table-container {
            background: rgba(26, 26, 46, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e3f2fd;
        }
        body.dark-mode .page-title {
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        body.dark-mode .page-subtitle {
            color: #e3f2fd;
        }
        body.dark-mode .form-label {
            color: #ffffff;
            font-weight: 600;
        }
        body.dark-mode .table td {
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: #e3f2fd;
            font-weight: 500;
        }
        body.dark-mode .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.1);
        }
        body.dark-mode .table tbody tr:hover td {
            color: #ffffff;
        }
        body.dark-mode .form-control {
            background: rgba(26, 26, 46, 0.8);
            border-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 500;
        }
        body.dark-mode .form-control:focus {
            background: rgba(26, 26, 46, 0.95);
            border-color: #667eea;
            color: #ffffff;
        }
        body.dark-mode .navbar {
            background: rgba(26, 26, 46, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.dark-mode .nav-link {
            color: #e3f2fd;
        }
        body.dark-mode .nav-link:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body>
    <button class="night-toggle" id="nightToggleBtn" title="夜间/白天模式" style="position:fixed;top:24px;right:32px;z-index:1000;">
        <i class="fas fa-moon"></i>
    </button>
    <script>
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
    if(localStorage.getItem('henyii_dark')==='1') setDarkMode(true);
    </script>
    <div class="back-btn">
        <a href="dashboard.php" class="btn btn-outline-light">
            <i class="fas fa-arrow-left"></i> <?php echo $langArr['home'] ?? '返回首页'; ?>
        </a>
    </div>

    <div class="container main-container">
        <div class="content-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-users me-3"></i><?php echo $langArr['student_manage'] ?? '学生管理'; ?>
                </h2>
                <div class="btn-group-vertical-student">
                    <a href="student_add.php" class="main-btn-student">
                        <i class="fas fa-plus me-2"></i><?php echo $langArr['add_student'] ?? '添加学生'; ?>
                    </a>
                    <a href="student_import.php" class="main-btn-student">
                        <i class="fas fa-upload me-2"></i><?php echo $langArr['import_students'] ?? '批量导入'; ?>
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $langArr['student_id'] ?? '学号'; ?></th>
                            <th><?php echo $langArr['name'] ?? '姓名'; ?></th>
                            <th><?php echo $langArr['chinese_name'] ?? '中文名'; ?></th>
                            <th><?php echo $langArr['gender'] ?? '性别'; ?></th>
                            <th><?php echo $langArr['class'] ?? '班级'; ?></th>
                            <th><?php echo $langArr['race'] ?? '种族'; ?></th>
                            <th><?php echo $langArr['religion'] ?? '宗教'; ?></th>
                            <th><?php echo $langArr['action'] ?? '操作'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_no']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['chinese_name'] ?? ''); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($student['gender']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($student['class']); ?></td>
                            <td><?php echo htmlspecialchars($student['race'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($student['religion'] ?? ''); ?></td>
                            <td>
                                <a href="student_edit.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="student_delete.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('<?php echo $langArr['confirm_delete_student'] ?? '确定要删除这个学生吗？'; ?>')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 