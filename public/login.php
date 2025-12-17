<?php
session_start();
require_once __DIR__ . '/../src/controllers/AuthController.php';
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = AuthController::login($username, $password);
    // 已移除var_dump($user); exit;，恢复正常登录流程
    if ($user) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['lang'] = $user['language'] ?? 'zh';
        
        // 根据角色重定向到不同页面
        if ($user['role'] === 'teacher') {
            header('Location: dashboard_teacher.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $error = $langArr['login_failed'] ?? '用户名或密码错误';
    }
        } elseif ($_POST['action'] === 'forgot_password') {
            $email = $_POST['email'];
            $result = AuthController::forgotPassword($email);
            if ($result['success']) {
                $success = $result['message'];
                // 如果是测试环境，显示重置链接
                if (isset($result['reset_link'])) {
                    $success .= '<br><br><strong>Test Reset Link:</strong> <a href="' . $result['reset_link'] . '" target="_blank">' . $result['reset_link'] . '</a>';
                }
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $langArr['login']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', Arial, sans-serif;
            transition: background 0.5s, color 0.5s;
        }
        body.dark-mode {
            background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        .login-card {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(12px);
            border-radius: 36px;
            box-shadow: 0 12px 48px rgba(120,120,255,0.13);
            padding: 56px 40px 40px 40px;
            max-width: 410px;
            width: 100%;
            margin: 32px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.32s, background 0.5s;
        }
        body.dark-mode .login-card {
            background: rgba(35,41,70,0.92);
            color: #f3f6fa;
            box-shadow: 0 8px 32px #7f7fd588, 0 0 24px 4px #7f7fd5, 0 0 0 2.5px #7f7fd544 inset, 0 1.5px 1.5px 0 #fff8 inset;
        }
        .login-card .logo .icon-bg {
            background: linear-gradient(135deg, #7f7fd5 0%, #86a8e7 100%);
            color: #fff;
            border-radius: 50%;
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            margin: 0 auto 8px auto;
            box-shadow: 0 4px 16px rgba(120,120,255,0.10);
            transition: box-shadow 0.2s, filter 0.2s;
        }
        .login-card h1 {
            font-size: 2.3rem;
            font-weight: 800;
            color: #4f46e5;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        body.dark-mode .login-card h1 {
            color: #7f7fd5;
            text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
        }
        .login-card p {
            color: #666;
            font-size: 1.08rem;
            text-align: center;
            margin-bottom: 28px;
        }
        body.dark-mode .login-card p {
            color: #bfc8e6;
        }
        .form-label {
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 6px;
        }
        body.dark-mode .form-label {
            color: #a1c4fd;
        }
        .login-card form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .form-group {
            margin-bottom: 0;
            width: 100%;
        }
        .form-control {
            border-radius: 22px;
            border: 1.5px solid #e3f0fc;
            font-size: 1.08rem;
            padding: 14px 18px;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.5s;
            width: 100%;
            box-sizing: border-box;
        }
        body.dark-mode .form-control {
            background: #232946;
            color: #f3f6fa;
            border-color: #7f7fd5;
        }
        .form-control:focus {
            border-color: #7f7fd5;
            box-shadow: 0 0 0 2px #7f7fd533;
        }
        .btn-primary {
            width: 100%;
            margin: 0;
            margin-top: 2px;
            border-radius: 22px;
            font-size: 1.15rem;
            padding: 14px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary i {
            font-size: 1.1em;
        }
        body.dark-mode .btn-primary {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
            color: #fff;
        }
        .btn-primary:hover {
            filter: brightness(1.10);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 8px 32px rgba(120,120,255,0.18);
        }
        .toggle-form {
            text-align: center;
            margin-top: 18px;
        }
        .toggle-form a {
            color: #4f46e5;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 700;
            transition: color 0.2s;
        }
        .toggle-form a:hover {
            text-decoration: underline;
            color: #86a8e7;
        }
        body.dark-mode .toggle-form a {
            color: #a1c4fd;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        /* 夜间模式切换按钮 */
        .night-toggle {
            position: fixed;
            top: 32px;
            right: 40px;
            z-index: 1000;
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
        body.dark-mode .night-toggle {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
        }
        @media (max-width: 480px) {
            .login-card {
                padding: 22px 4px 18px 4px;
                border-radius: 18px;
            }
            .login-card h1 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<button class="night-toggle" id="nightToggleBtn" title="夜间/白天模式"><i class="fas fa-moon"></i></button>
    <div class="login-card">
        <div class="logo">
            <span class="icon-bg"><i class="fas fa-user-circle"></i></span>
        </div>
        <h1><?php echo $langArr['login']; ?></h1>
        <p><?php echo $langArr['welcome_back'] ?? 'Welcome back! Please sign in to your account.'; ?></p>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <form method="post" id="loginForm">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="username" class="form-label"><?php echo $langArr['username']; ?></label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label"><?php echo $langArr['password']; ?></label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i><?php echo $langArr['login']; ?>
            </button>
            <div class="toggle-form">
                <a href="register.php"><?php echo $langArr['no_account'] ?? '还没有账号？注册'; ?></a>
            </div>
            <div class="toggle-form">
                <a href="#" onclick="showForgotPassword();return false;"><?php echo $langArr['forgot_password'] ?? '忘记密码？'; ?></a>
            </div>
        </form>
        <form method="post" id="forgotForm" style="display:none;width:100%;">
            <input type="hidden" name="action" value="forgot_password">
            <div class="form-group">
                <label for="forgot_email" class="form-label"><?php echo $langArr['email'] ?? '邮箱'; ?></label>
                <input type="email" id="forgot_email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i><?php echo $langArr['send_reset_email'] ?? '发送重置邮件'; ?>
            </button>
            <div class="toggle-form">
                <a href="#" onclick="showLoginForm();return false;"><?php echo $langArr['back_to_login'] ?? '返回登录'; ?></a>
            </div>
        </form>
    </div>
</body>
<script>
function showForgotPassword() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('forgotForm').style.display = '';
}
function showLoginForm() {
    document.getElementById('loginForm').style.display = '';
    document.getElementById('forgotForm').style.display = 'none';
}
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
</script>
</html> 