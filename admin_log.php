<?php
session_start();
require_once __DIR__ . '/../src/models/Log.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$logs = Log::all(200);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title>操作日志</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { background: #f4f8fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); max-width: 900px; margin: 40px auto; padding: 40px 30px; }
        h2 { text-align: center; color: #1976d2; font-size: 2rem; font-weight: 700; margin-bottom: 18px; }
        table { width: 100%; margin-top: 20px; }
        th, td { text-align: center; vertical-align: middle; }
        .btn-link { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>操作日志</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>用户</th>
                <th>操作</th>
                <th>详情</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo $log['id']; ?></td>
                <td><?php echo htmlspecialchars($log['username'] ?? '未知'); ?></td>
                <td><?php echo htmlspecialchars($log['action']); ?></td>
                <td><?php echo htmlspecialchars($log['detail']); ?></td>
                <td><?php echo $log['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="admin_user_manage.php" class="btn btn-link">返回用户管理</a>
</div>
</body>
</html> 