<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/vendor/autoload.php';
require_once '../src/models/User.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: user_list.php'); exit; }
$user = User::findById($id);
if (!$user) { header('Location: user_list.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $language = $_POST['language'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $ok = User::updateAllFields($id, $username, $email, $role, $language, $name);
    if ($ok && $newPassword) {
        $ok = User::updatePassword($id, $newPassword);
    }
    if ($ok) {
        // 如果是当前登录用户，更新 session 里的语言
        if ($_SESSION['user_id'] == $id) {
            $_SESSION['lang'] = $language;
        }
        header('Location: user_list.php');
        exit;
    } else {
        $error = $langArr['update_failed'] ?? '更新失败！';
    }
}
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/lang/' . $lang . '.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['edit_user'] ?? '编辑账号'; ?></title>
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
        .main-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 8px 32px rgba(247,151,30,0.13);
            padding: 40px 32px 32px 32px;
            margin: 48px 0;
            max-width: 500px;
            width: 100%;
        }
        h2 {
            color: #f7971e;
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
            box-shadow: 0 2px 8px rgba(247,151,30,0.08);
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
            color: #f7971e;
            margin-bottom: 6px;
        }
        .form-control {
            border-radius: 18px;
            border: 1.5px solid #ffe0b2;
            font-size: 1.08rem;
            padding: 10px 16px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #f7971e;
            box-shadow: 0 0 0 2px #f7971e33;
        }
        /* 美化退出登录按钮 */
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
    </style>
</head>
<body>
<div class="main-card">
    <h2 class="mb-4"><?php echo $langArr['edit_user'] ?? '编辑账号'; ?></h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['username'] ?? '用户名'; ?></label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['email'] ?? '邮箱（可选）'; ?></label>
            <input type="email" name="email" class="form-control" id="emailInput" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['name'] ?? '姓名'; ?></label>
            <input type="text" name="name" class="form-control" id="nameInput" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['role'] ?? '角色'; ?></label>
            <select name="role" class="form-select">
                <option value="admin" <?php if($user['role']==='admin')echo'selected';?>><?php echo $langArr['admin'] ?? '管理员'; ?></option>
                <option value="teacher" <?php if($user['role']==='teacher')echo'selected';?>><?php echo $langArr['teacher'] ?? '教师'; ?></option>
                <option value="student" <?php if($user['role']==='student')echo'selected';?>><?php echo $langArr['student'] ?? '学生'; ?></option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['language'] ?? '语言'; ?></label>
            <select name="language" class="form-select">
                <option value="zh" <?php if($user['language']==='zh')echo'selected';?>><?php echo $langArr['zh'] ?? '中文'; ?></option>
                <option value="en" <?php if($user['language']==='en')echo'selected';?>><?php echo $langArr['en'] ?? 'English'; ?></option>
                <option value="ms" <?php if($user['language']==='ms')echo'selected';?>><?php echo $langArr['ms'] ?? 'Malay'; ?></option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['new_password'] ?? '新密码'; ?></label>
            <input type="password" name="new_password" class="form-control" autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-success"><?php echo $langArr['save'] ?? '保存'; ?></button>
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
            }
            if (!emailInput.value) {
                const rand = Math.floor(Math.random()*9000+1000);
                emailInput.value = 'teacher' + rand + '@school.com';
            }
            if (!nameInput.value) {
                const rand = Math.floor(Math.random()*9000+1000);
                nameInput.value = '教师' + rand;
            }
        }
    });
});
</script>
</body>
</html> 