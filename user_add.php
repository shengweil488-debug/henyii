<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/vendor/autoload.php';
require_once '../src/models/User.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $language = $_POST['language'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    if (User::findByUsername($username)) {
        $error = $langArr['username_exists'] ?? '用户名已存在！';
    } else {
        if (User::createWithEmail($username, $password, $role, $language, $email, $name)) {
            header('Location: user_list.php');
            exit;
        } else {
            $error = $langArr['add_failed'] ?? '添加失败！';
        }
    }
}
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/lang/' . $lang . '.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['add_user'] ?? '添加账号'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body.dark-mode {
            background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        .main-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 8px 32px rgba(67,206,162,0.13);
            padding: 40px 32px 32px 32px;
            margin: 48px 0;
            max-width: 500px;
            width: 100%;
        }
        h2 {
            color: #4f46e5;
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }
        .btn {
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 8px 28px;
            margin: 0 4px 8px 0;
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
            transition: filter 0.2s, transform 0.2s;
        }
        .btn-success {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            border: none;
        }
        .btn-secondary {
            background: #bdbdbd;
            color: #fff;
            border: none;
        }
        .btn:hover {
            filter: brightness(1.08);
            transform: translateY(-2px) scale(1.03);
        }
        .form-label {
            font-weight: 600;
            color: #4f46e5;
            margin-bottom: 6px;
        }
        .form-control {
            border-radius: 18px;
            border: 1.5px solid #e3f0fc;
            font-size: 1.08rem;
            padding: 10px 16px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px #4f46e533;
        }
        .logout-btn a {
            background: #ff4d4f;
            color: #fff !important;
            border: none;
            border-radius: 30px;
            font-weight: bold;
            box-shadow: 0 2px 12px rgba(255,77,79,0.15);
            padding: 12px 28px;
            font-size: 1.1rem;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .logout-btn a:hover, .logout-btn a:focus {
            background: #d9363e;
            color: #fff !important;
            box-shadow: 0 4px 24px rgba(255,77,79,0.25);
            text-decoration: none;
        }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        @media (max-width: 600px) {
            .main-card { padding: 8px 0; border-radius: 12px; }
            h2 { font-size: 1.2rem; }
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
<div class="main-card">
    <h2 class="mb-4"><?php echo $langArr['add_user'] ?? '添加账号'; ?></h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['username'] ?? '用户名'; ?></label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['password'] ?? '密码'; ?></label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['email'] ?? '邮箱（可选）'; ?></label>
            <input type="email" name="email" class="form-control" id="emailInput" placeholder="<?php echo $langArr['email'] ?? '邮箱（可选）'; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['name'] ?? '姓名'; ?></label>
            <input type="text" name="name" class="form-control" id="nameInput">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['role'] ?? '角色'; ?></label>
            <select name="role" class="form-select">
                <option value="admin"><?php echo $langArr['admin'] ?? '管理员'; ?></option>
                <option value="teacher"><?php echo $langArr['teacher'] ?? '教师'; ?></option>
                <option value="student"><?php echo $langArr['student'] ?? '学生'; ?></option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['language'] ?? '语言'; ?></label>
            <select name="language" class="form-select">
                <option value="zh"><?php echo $langArr['zh'] ?? '中文'; ?></option>
                <option value="en"><?php echo $langArr['en'] ?? 'English'; ?></option>
                <option value="ms"><?php echo $langArr['ms'] ?? 'Malay'; ?></option>
            </select>
        </div>
        <button type="submit" class="btn btn-success"><?php echo $langArr['add'] ?? '添加'; ?></button>
        <a href="user_list.php" class="btn btn-secondary"><?php echo $langArr['back'] ?? '返回'; ?></a>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    const usernameInput = document.querySelector('input[name="username"]');
    const nameInput = document.getElementById('nameInput');
    const emailInput = document.getElementById('emailInput');
    roleSelect.addEventListener('change', function() {
        if (roleSelect.value === 'teacher') {
            if (!usernameInput.value) {
                const rand = Math.floor(Math.random()*9000+1000);
                usernameInput.value = 'teacher' + rand;
                emailInput.value = 'teacher' + rand + '@school.com';
                nameInput.value = '教师' + rand;
            }
        }
    });
});
</script>
</body>
</html> 