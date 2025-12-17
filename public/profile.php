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
$currentUser = $user->findById($_SESSION['user_id']);

$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $language = $lang; // 保持当前语言
    // 先更新email和language
    $user->updateProfile($_SESSION['user_id'], $email, $language);
    // 再更新name（如有变化）
    if ($name !== ($currentUser['name'] ?? '')) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE users SET name=? WHERE id=?');
        $stmt->execute([$name, $_SESSION['user_id']]);
    }
    // 更新密码（如有变化）
    if (!empty($newPassword)) {
        $user->updatePassword($_SESSION['user_id'], $newPassword);
    }
    $successMessage = $langArr['profile_update_success'] ?? '个人信息更新成功！';
    $currentUser = $user->findById($_SESSION['user_id']); // 重新获取用户信息
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $langArr['profile'] ?? '个人中心'; ?> - <?php echo $langArr['activities'] ?? '活动管理系统'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(120deg,#a1c4fd 60%,#c2e9fb 100%);
            min-height: 100vh;
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
            color: #232946;
            transition: background 0.5s, color 0.5s;
        }
        .main-container {
            padding: 2rem 0;
        }
        .content-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 4px 24px rgba(160,132,238,0.13), 0 1.5px 8px #7f7fd544;
            border: none;
            transition: box-shadow 0.32s, background 0.5s;
        }
        .content-card:hover {
            box-shadow: 0 8px 32px 0 #a1c4fd99, 0 24px 64px 0 #7f7fd544, 0 0 24px 4px #a1c4fd, 0 0 0 2.5px #a1c4fd88 inset, 0 1.5px 1.5px 0 #fff8 inset;
            transform: translateY(-8px) scale(1.025);
        }
        .form-control {
            border-radius: 20px;
            border: 1.5px solid #e3eaf2;
            padding: 0.75rem 1rem;
            box-shadow: 0 1.5px 8px #7f7fd522 inset;
            background: rgba(255,255,255,0.95);
            transition: box-shadow 0.22s, background 0.5s;
        }
        .form-control:focus {
            border-color: #7f7fd5;
            box-shadow: 0 0 0 2px #7f7fd5cc, 0 1.5px 8px #7f7fd522 inset;
            background: #f3f6fa;
        }
        .btn-custom {
            border-radius: 24px;
            padding: 0.8rem 2.2rem;
            font-weight: 600;
            border: none;
            background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
            color: #fff;
            box-shadow: 0 2px 12px #7f7fd522, 0 0 0 2px #a1c4fd22 inset;
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-custom:hover {
            background: linear-gradient(90deg, #86a8e7 0%, #7f7fd5 100%);
            color: #fff;
            box-shadow: 0 4px 24px #7f7fd544;
            transform: translateY(-2px) scale(1.04);
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        .profile-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7f7fd5 0%, #86a8e7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 40px;
            color: white;
            box-shadow: 0 2px 8px #7f7fd544, 0 0 0 8px #a1c4fd33 inset, 0 2px 8px #fff8 inset;
            text-shadow: 0 2px 8px #7f7fd544, 0 1px 2px #fff8;
            filter: drop-shadow(0 2px 8px #7f7fd544) drop-shadow(0 0 4px #fff8);
            transition: box-shadow 0.32s, filter 0.32s, transform 0.32s;
        }
        .profile-icon:hover {
            box-shadow: 0 8px 32px #a1c4fdcc, 0 0 0 16px #a1c4fd33 inset, 0 4px 16px #fff8 inset;
            filter: brightness(1.18) drop-shadow(0 4px 16px #a1c4fd99) drop-shadow(0 0 8px #fff8);
            transform: scale(1.08);
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
        /* 夜间模式 */
        body.dark-mode {
            background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        body.dark-mode .content-card {
            background: rgba(35,41,70,0.92) !important;
            color: #f3f6fa !important;
            box-shadow:
                0 2.5px 12px 0 #7f7fd544,
                0 8px 32px 0 #7f7fd588,
                0 0 8px 2px #7f7fd5,
                0 0 0 1.5px #7f7fd544 inset,
                0 0.5px 0.5px 0 #fff2 inset;
            border-radius: 22px;
            border: 1.5px solid #7f7fd544;
            background-image: linear-gradient(180deg,rgba(120,120,255,0.10) 0%,transparent 60%);
        }
        body.dark-mode .content-card:hover {
            box-shadow:
                0 8px 32px 0 #a1c4fdcc,
                0 24px 64px 0 #23294699,
                0 0 32px 8px #7f7fd5,
                0 0 0 2.5px #a1c4fd inset,
                0 1.5px 1.5px 0 #fff2 inset;
        }
        body.dark-mode .form-control {
            background: rgba(35,41,70,0.92) !important;
            color: #f3f6fa !important;
            border: 1.5px solid #7f7fd544;
            box-shadow: 0 1.5px 8px #7f7fd544 inset;
        }
        body.dark-mode .form-control:focus {
            background: #232946 !important;
            color: #fff !important;
            box-shadow: 0 0 0 2px #7f7fd5cc, 0 1.5px 8px #7f7fd544 inset;
        }
        body.dark-mode .btn-custom {
            color: #fff !important;
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
            box-shadow:
                0 2px 12px #7f7fd5cc,
                0 0 0 2px #7f7fd5 inset,
                0 0.5px 0.5px #fff2 inset;
        }
        body.dark-mode .btn-custom:hover {
            background: linear-gradient(90deg, #393a5a 0%, #a1c4fd 100%);
            box-shadow:
                0 6px 24px #7f7fd5cc,
                0 0 0 4px #a1c4fd inset,
                0 2px 8px #fff2 inset;
        }
        body.dark-mode .profile-icon {
            background: linear-gradient(135deg, #232946 0%, #7f7fd5 100%);
            color: #fff;
            box-shadow: 0 4px 16px #7f7fd544, 0 0 0 8px #a1c4fd33 inset, 0 2px 8px #fff2 inset;
            text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
        }
        body.dark-mode h2, body.dark-mode h5, body.dark-mode .display-4 {
            color: #fff !important;
            text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
        }
        @media (max-width: 900px) {
            .content-card { border-radius: 14px; }
        }
        @media (max-width: 600px) {
            .content-card { border-radius: 8px; }
        }
        .page-container {
            padding: 30px 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            margin-bottom: 30px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            font-weight: 400;
        }
        .profile-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 40px;
            align-items: start;
        }
        .avatar-section {
            text-align: center;
        }
        .avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 8px 25px rgba(31, 38, 135, 0.2);
            transition: all 0.3s ease;
        }
        .avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(31, 38, 135, 0.3);
        }
        .avatar-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .avatar-upload:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        .form-control {
            border-radius: 12px;
            border: 1.5px solid #e3f0fc;
            padding: 14px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            width: 100%;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
            outline: none;
        }
        .btn-save {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-cancel {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
            color: white;
            text-decoration: none;
        }
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.1rem;
        }
        .info-content {
            flex: 1;
        }
        .info-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        .info-value {
            color: #6c757d;
            font-size: 1rem;
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
        body.dark-mode .content-card {
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
            font-weight: 400;
        }
        body.dark-mode .form-label {
            color: #ffffff;
            font-weight: 600;
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
        body.dark-mode .info-item {
            background: rgba(26, 26, 46, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.dark-mode .info-label {
            color: #ffffff;
            font-weight: 600;
        }
        body.dark-mode .info-value {
            color: #e3f2fd;
            font-weight: 500;
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
        @media (max-width: 768px) {
            .profile-section {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .avatar {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="back-btn">
        <a href="dashboard.php" class="btn btn-outline-light">
            <i class="fas fa-arrow-left"></i> <?php echo $langArr['home'] ?? '返回首页'; ?>
        </a>
    </div>
    <div class="night-toggle" id="nightToggleBtn" title="夜间/白天模式" style="position:fixed;top:32px;right:40px;z-index:1000;">
        <i class="fas fa-moon"></i>
    </div>

    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="content-card p-4">
                    <div class="text-center mb-4">
                        <div class="profile-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="mb-0">
                            <i class="fas fa-user-cog me-3"></i><?php echo $langArr['profile'] ?? '个人中心'; ?>
                        </h2>
                        <p class="text-muted"><?php echo $langArr['profile_desc'] ?? '管理您的个人信息和账户设置'; ?></p>
                    </div>

                    <?php if (isset($successMessage)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $successMessage; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errorMessage)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $errorMessage; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i><?php echo $langArr['username'] ?? '用户名'; ?>
                                </label>
                                <input type="text" class="form-control" id="username" 
                                       value="<?php echo htmlspecialchars($currentUser['username']); ?>" readonly>
                                <div class="form-text"><?php echo $langArr['username_tip'] ?? '用户名不可修改'; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    <i class="fas fa-user-tag me-2"></i><?php echo $langArr['role'] ?? '角色'; ?>
                                </label>
                                <input type="text" class="form-control" id="role" 
                                       value="<?php echo htmlspecialchars($currentUser['role']); ?>" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-id-card me-2"></i><?php echo $langArr['name'] ?? '姓名'; ?>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i><?php echo $langArr['email'] ?? '邮箱'; ?>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-lock me-2"></i><?php echo $langArr['new_password'] ?? '新密码'; ?>
                            </label>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   placeholder="<?php echo $langArr['new_password_tip'] ?? '留空则不修改密码'; ?>">
                            <div class="form-text"><?php echo $langArr['new_password_desc'] ?? '如需修改密码，请输入新密码'; ?></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="created_at" class="form-label">
                                    <i class="fas fa-calendar me-2"></i><?php echo $langArr['created_at'] ?? '创建时间'; ?>
                                </label>
                                <input type="text" class="form-control" id="created_at" 
                                       value="<?php echo date('Y-m-d H:i:s', strtotime($currentUser['created_at'])); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_login" class="form-label">
                                    <i class="fas fa-clock me-2"></i><?php echo $langArr['last_login'] ?? '最后登录'; ?>
                                </label>
                                <input type="text" class="form-control" id="last_login" 
                                       value="<?php echo (isset($currentUser['last_login']) && $currentUser['last_login']) ? date('Y-m-d H:i:s', strtotime($currentUser['last_login'])) : '从未登录'; ?>" readonly>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-custom me-3">
                                <i class="fas fa-save me-2"></i><?php echo $langArr['save'] ?? '保存更改'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
        // 丝滑退出/跳转动画（无loading遮罩）
        function fadeOutAndGo(href) {
            document.body.style.transition = 'opacity 0.45s cubic-bezier(.4,2,.6,1)';
            document.body.style.opacity = 0;
            setTimeout(function() { window.location = href; }, 420);
        }
        document.querySelectorAll('.logout-btn a, .back-btn a').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                var href = btn.getAttribute('href');
                if(href && href !== '#') {
                    e.preventDefault();
                    fadeOutAndGo(href);
                }
            });
        });
    </script>
</body>
</html> 