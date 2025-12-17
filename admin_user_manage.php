<?php
session_start();
require_once __DIR__ . '/../src/models/User.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$users = User::all();
$success = '';
$error = '';
// 处理新增账号
if (isset($_POST['add_user'])) {
    $username = trim($_POST['new_username']);
    $password = $_POST['new_password'];
    $name = trim($_POST['new_name'] ?? '');
    $role = $_POST['new_role'];
    $email = trim($_POST['new_email']);
    $language = $_POST['new_language'] ?? 'en';
    if (!$username || !$password) {
        $error = $langArr['username_password_required'] ?? '用户名和密码不能为空';
    } elseif (User::findByUsername($username)) {
        $error = $langArr['username_exists'] ?? '用户名已存在';
    } else {
        User::createWithEmail($username, $password, $role, $language, $email, $name);
        $success = $langArr['add_success'] ?? '账号添加成功';
        $users = User::all();
    }
}
// 处理编辑账号
if (isset($_POST['edit_user'])) {
    $uid = intval($_POST['edit_id']);
    $username = trim($_POST['edit_username']);
    $email = trim($_POST['edit_email']);
    $role = $_POST['edit_role'];
    $language = $_POST['edit_language'] ?? 'en';
    $password = $_POST['edit_password'];
    $name = trim($_POST['edit_name'] ?? '');
    if ($password) {
        User::updatePassword($uid, $password);
    }
    User::updateAllFields($uid, $username, $email, $role, $language, $name);
    $success = $langArr['update_success'] ?? '账号信息已更新';
    $users = User::all();
}
// 处理删除账号
if (isset($_POST['delete_user'])) {
    $uid = intval($_POST['delete_id']);
    if ($uid == $_SESSION['user']['id']) {
        $error = $langArr['cannot_delete_self'] ?? '不能删除自己';
    } else {
        User::deleteById($uid);
        $success = $langArr['delete_success'] ?? '账号已删除';
        $users = User::all();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['user_manage'] ?? '账号管理'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { background: linear-gradient(135deg, #e0e7ff 0%, #f4f8fb 100%); }
        .container {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(25,118,210,0.13);
            max-width: 1100px;
            margin: 40px auto;
            padding: 48px 32px 32px 32px;
        }
        h2 {
            text-align: center;
            color: #1976d2;
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 28px;
            letter-spacing: 2px;
        }
        .add-user-form {
            background: linear-gradient(90deg, #e3f0fc 0%, #f6fafd 100%);
            border-radius: 16px;
            padding: 22px 18px 10px 18px;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(25,118,210,0.06);
        }
        .add-user-form input, .add-user-form select {
            border-radius: 10px;
            font-size: 1rem;
        }
        .add-user-form .btn-success {
            font-weight: 700;
            letter-spacing: 1px;
            border-radius: 10px;
        }
        .table {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            margin-top: 0;
            box-shadow: 0 2px 12px rgba(25,118,210,0.07);
        }
        .table th {
            background: linear-gradient(90deg, #1976d2 60%, #42a5f5 100%);
            color: #fff;
            font-weight: 700;
            border-top: none;
            font-size: 1.05rem;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f6fafd;
        }
        .table-bordered th, .table-bordered td {
            border: 1.5px solid #e3f0fc;
        }
        .table tbody tr:hover {
            background: #e3f0fc !important;
            transition: background 0.2s;
        }
        .form-inline-edit input, .form-inline-edit select {
            width: 100%;
            min-width: 80px;
            border-radius: 8px;
        }
        .action-btns {
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        .action-btns .btn {
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 1px 4px rgba(25,118,210,0.06);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .action-btns .btn:hover {
            transform: translateY(-2px) scale(1.07);
            box-shadow: 0 4px 16px rgba(25,118,210,0.13);
        }
        .btn-primary { background: linear-gradient(90deg, #1976d2 60%, #42a5f5 100%); border: none; color: #fff; }
        .btn-danger { background: linear-gradient(90deg, #e53935 60%, #ff7675 100%); border: none; color: #fff; }
        .btn-success { background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%); border: none; color: #fff; }
        .btn:focus { outline: none; box-shadow: 0 0 0 2px #1976d230; }
        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 15px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        @media (max-width: 768px) {
            .container { padding: 10px 2px; }
            h2 { font-size: 1.3rem; }
            .add-user-form { padding: 12px 4px 4px 4px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fa fa-user-shield"></i> <?php echo $langArr['user_manage'] ?? '账号管理'; ?></h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <!-- 新增账号表单 -->
    <form class="add-user-form row g-2 align-items-end" method="post">
        <input type="hidden" name="add_user" value="1">
        <div class="col-md-2"><input type="text" name="new_username" class="form-control" placeholder="<?php echo $langArr['username'] ?? '用户名'; ?>" required></div>
        <div class="col-md-2"><input type="password" name="new_password" class="form-control" placeholder="<?php echo $langArr['password'] ?? '密码'; ?>" required></div>
        <div class="col-md-2"><input type="text" name="new_name" class="form-control" placeholder="<?php echo $langArr['name'] ?? '姓名'; ?>"></div>
        <div class="col-md-2"><input type="email" name="new_email" class="form-control" placeholder="<?php echo $langArr['email'] ?? '邮箱'; ?>"></div>
        <div class="col-md-2">
            <select name="new_role" class="form-select">
                <option value="student"><?php echo $langArr['student'] ?? '学生'; ?></option>
                <option value="teacher"><?php echo $langArr['teacher'] ?? '教师'; ?></option>
                <option value="admin"><?php echo $langArr['admin'] ?? '管理员'; ?></option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="new_language" class="form-select">
                <option value="en"><?php echo $langArr['en'] ?? 'English'; ?></option>
                <option value="ms"><?php echo $langArr['ms'] ?? 'Malay'; ?></option>
                <option value="zh"><?php echo $langArr['zh'] ?? '中文'; ?></option>
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-success w-100"><i class="fa fa-plus"></i> <?php echo $langArr['add_user'] ?? '新增账号'; ?></button></div>
    </form>
    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th><?php echo $langArr['username'] ?? '用户名'; ?></th>
                <th><?php echo $langArr['name'] ?? '姓名'; ?></th>
                <th><?php echo $langArr['email'] ?? '邮箱'; ?></th>
                <th><?php echo $langArr['role'] ?? '身份'; ?></th>
                <th><?php echo $langArr['language'] ?? '语言'; ?></th>
                <th><?php echo $langArr['action'] ?? '操作'; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <form class="form-inline-edit" method="post">
                    <input type="hidden" name="edit_user" value="1">
                    <input type="hidden" name="edit_id" value="<?php echo $u['id']; ?>">
                    <td><?php echo $u['id']; ?></td>
                    <td><input type="text" name="edit_username" value="<?php echo htmlspecialchars($u['username']); ?>" class="form-control" required></td>
                    <td><input type="text" name="edit_name" value="<?php echo htmlspecialchars($u['name'] ?? ''); ?>" class="form-control"></td>
                    <td><input type="email" name="edit_email" value="<?php echo htmlspecialchars($u['email']); ?>" class="form-control"></td>
                    <td>
                        <select name="edit_role" class="form-select">
                            <option value="student" <?php if($u['role']==='student')echo'selected';?>><?php echo $langArr['student'] ?? '学生'; ?></option>
                            <option value="teacher" <?php if($u['role']==='teacher')echo'selected';?>><?php echo $langArr['teacher'] ?? '教师'; ?></option>
                            <option value="admin" <?php if($u['role']==='admin')echo'selected';?>><?php echo $langArr['admin'] ?? '管理员'; ?></option>
                        </select>
                    </td>
                    <td>
                        <select name="edit_language" class="form-select">
                            <option value="en" <?php if(($u['language']??'')==='en')echo'selected';?>><?php echo $langArr['en'] ?? 'English'; ?></option>
                            <option value="ms" <?php if(($u['language']??'')==='ms')echo'selected';?>><?php echo $langArr['ms'] ?? 'Malay'; ?></option>
                            <option value="zh" <?php if(($u['language']??'')==='zh')echo'selected';?>><?php echo $langArr['zh'] ?? '中文'; ?></option>
                        </select>
                    </td>
                    <td class="action-btns">
                        <input type="password" name="edit_password" class="form-control mb-1" placeholder="<?php echo $langArr['reset_password'] ?? '重置密码'; ?>">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> <?php echo $langArr['save'] ?? '保存'; ?></button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete_user" value="1">
                    <input type="hidden" name="delete_id" value="<?php echo $u['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo $langArr['confirm_delete_user'] ?? '确定删除该账号？'; ?>')"><i class="fa fa-trash"></i></button>
                </form>
                    </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-link mt-3"><?php echo $langArr['home'] ?? '返回首页'; ?></a>
</div>
</body>
</html> 