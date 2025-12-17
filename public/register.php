<?php
session_start();
require_once __DIR__ . '/../src/controllers/AuthController.php';
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'student'; // 强制学生
    $language = $_POST['language'];
    if (AuthController::register($username, $password, $role, $language)) {
        $success = $langArr['register_success'] ?? 'Registration successful!';
    } else {
        $error = $langArr['username_exists'] ?? 'Username already exists!';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['register']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Montserrat', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: background 0.5s, color 0.5s;
        }
        body.dark-mode {
            background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        .container {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(12px);
            border-radius: 36px;
            box-shadow: 0 12px 48px rgba(120,120,255,0.13);
            max-width: 430px;
            width: 100%;
            padding: 56px 40px 40px 40px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.32s, background 0.5s;
        }
        body.dark-mode .container {
            background: rgba(35,41,70,0.92);
            color: #f3f6fa;
            box-shadow: 0 8px 32px #7f7fd588, 0 0 24px 4px #7f7fd5, 0 0 0 2.5px #7f7fd544 inset, 0 1.5px 1.5px 0 #fff8 inset;
        }
        .logo {
            text-align: center;
            margin-bottom: 18px;
        }
        .logo .icon-bg {
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
        h2 {
            text-align: center;
            color: #4f46e5;
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        body.dark-mode h2 {
            color: #7f7fd5;
            text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
        }
        .form-group {
            margin-bottom: 0;
            width: 100%;
        }
        .form-label {
            color: #4f46e5;
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
            font-size: 15px;
        }
        body.dark-mode .form-label {
            color: #a1c4fd;
        }
        .input-group {
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-55%);
            color: #b0b3d6;
            font-size: 20px;
            transition: color 0.2s;
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
        body.dark-mode .form-control, body.dark-mode .form-select {
            background: #232946;
            color: #f3f6fa;
            border-color: #7f7fd5;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #7f7fd5;
            background: #fff;
            box-shadow: 0 0 0 2px #7f7fd533;
        }
        body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
            background: #232946;
        }
        .input-group:focus-within i {
            color: #7f7fd5;
        }
        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 26px;
            font-size: 1.15rem;
            font-weight: 800;
            cursor: pointer;
            transition: filter 0.2s, transform 0.2s, box-shadow 0.2s;
            margin-bottom: 10px;
        }
        .btn-success {
            background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
            color: white;
            box-shadow: 0 2px 12px rgba(120,120,255,0.10);
        }
        body.dark-mode .btn-success {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
            color: #fff;
        }
        .btn-success:hover {
            filter: brightness(1.10);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 8px 32px rgba(120,120,255,0.18);
        }
        .btn-link {
            background: none;
            color: #4f46e5;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 700;
            margin-left: 10px;
            transition: color 0.2s;
        }
        body.dark-mode .btn-link {
            color: #a1c4fd;
        }
        .btn-link:hover {
            color: #86a8e7;
            text-decoration: underline;
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
        .register-card form {
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
        @media (max-width: 480px) {
            .container {
                padding: 22px 4px 18px 4px;
                border-radius: 18px;
            }
            h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<button class="night-toggle" id="nightToggleBtn" title="夜间/白天模式"><i class="fas fa-moon"></i></button>
<div class="container">
    <div class="logo">
        <span class="icon-bg"><i class="fas fa-user-plus"></i></span>
    </div>
    <h2><?php echo $langArr['register']; ?></h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['username']; ?></label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['password']; ?></label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <!-- 移除角色选择 -->
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['language']; ?></label>
            <select name="language" class="form-select">
                <option value="en"><?php echo $langArr['en'] ?? 'English'; ?></option>
                <option value="ms"><?php echo $langArr['ms'] ?? 'Malay'; ?></option>
                <option value="zh"><?php echo $langArr['zh'] ?? '中文'; ?></option>
            </select>
        </div>
        <button type="submit" class="btn btn-success"><?php echo $langArr['register']; ?></button>
        <a href="login.php" class="btn btn-link"><?php echo $langArr['login']; ?></a>
    </form>
</div>
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
</script>
</body>
</html> 