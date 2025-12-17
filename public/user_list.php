<?php
session_start();
require_once '../vendor/autoload.php';
require_once '../src/models/User.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$users = $user->getAllUsers();

$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $langArr['user_manage'] ?? '用户管理'; ?> - <?php echo $langArr['activities'] ?? '活动管理系统'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        body.dark-mode h1, body.dark-mode h2, body.dark-mode .maintitle {
            color: #fff !important;
            font-size: 2.2rem !important;
            font-weight: 900 !important;
            text-shadow: 0 2px 12px #7f7fd5, 0 1px 2px #fff2;
            letter-spacing: 1.5px;
        }
        @media (max-width: 900px) {
            .content-card { border-radius: 14px; }
            .table th, .table td { font-size: 0.97rem; }
        }
        @media (max-width: 600px) {
            .content-card { border-radius: 8px; }
            .table th, .table td { font-size: 0.93rem; }
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
                    <i class="fas fa-users me-3"></i><?php echo $langArr['user_manage'] ?? '用户管理'; ?>
                </h2>
                <a href="user_add.php" class="btn btn-success btn-custom">
                    <i class="fas fa-plus me-2"></i><?php echo $langArr['add_user'] ?? '添加用户'; ?>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $langArr['username'] ?? '用户名'; ?></th>
                            <th><?php echo $langArr['name'] ?? '姓名'; ?></th>
                            <th><?php echo $langArr['email'] ?? '邮箱'; ?></th>
                            <th><?php echo $langArr['role'] ?? '角色'; ?></th>
                            <th><?php echo $langArr['created_at'] ?? '创建时间'; ?></th>
                            <th><?php echo $langArr['action'] ?? '操作'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($user['role']); ?></span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="user_edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="user_delete.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('<?php echo $langArr['confirm_delete_user'] ?? '确定要删除这个用户吗？'; ?>')">
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