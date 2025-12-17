<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ActivityController.php';
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$user = $_SESSION['user'];
$activities = ActivityController::list();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['activity_manage'] ?? '全部活动管理'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #56ccf2 0%, #2f80ed 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            transition: background 0.5s, color 0.5s;
        }
        body.dark-mode {
            background: linear-gradient(135deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        .main-card {
            background: rgba(86,204,242,0.18);
            border-radius: 28px;
            box-shadow: 0 8px 32px rgba(47,128,237,0.13), 0 1.5px 8px #7f7fd544;
            padding: 40px 32px 32px 32px;
            margin: 48px 0;
            max-width: 1100px;
            width: 100%;
            transition: box-shadow 0.32s, background 0.5s;
        }
        body.dark-mode .main-card {
            background: rgba(35,41,70,0.85);
            box-shadow: 0 8px 32px #7f7fd588, 0 0 24px 4px #7f7fd5, 0 0 0 2.5px #7f7fd544 inset, 0 1.5px 1.5px 0 #fff8 inset;
        }
        h2 {
            color: #2f80ed;
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
            text-shadow: 0 2px 8px #7f7fd544, 0 1px 2px #fff8;
        }
        body.dark-mode h2 {
            color: #7f7fd5;
            text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
        }
        .btn {
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 8px 28px;
            margin: 0 4px 8px 0;
            box-shadow: 0 2px 8px rgba(47,128,237,0.08), 0 1.5px 8px #7f7fd522 inset;
            transition: filter 0.2s, transform 0.2s, background 0.5s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-info {
            background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
            color: #fff;
            border: none;
        }
        .btn-success {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            border: none;
        }
        .btn-danger {
            background: linear-gradient(90deg, #e53935 0%, #e35d5b 100%);
            color: #fff;
            border: none;
        }
        .btn-secondary {
            background: #bdbdbd;
            color: #fff;
            border: none;
        }
        body.dark-mode .btn-info {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
            color: #fff;
        }
        body.dark-mode .btn-success {
            background: linear-gradient(90deg, #232946 0%, #43e97b 100%);
            color: #fff;
        }
        body.dark-mode .btn-danger {
            background: linear-gradient(90deg, #232946 0%, #e53935 100%);
            color: #fff;
        }
        body.dark-mode .btn-secondary {
            background: #393a5a;
            color: #fff;
        }
        .btn:hover {
            filter: brightness(1.08);
            transform: translateY(-2px) scale(1.03);
        }
        .table {
            background: rgba(227,240,252,0.18);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 18px;
            box-shadow: 0 2px 12px rgba(47,128,237,0.06);
            transition: background 0.5s;
        }
        body.dark-mode .table {
            background: rgba(35,41,70,0.85);
            color: #f3f6fa;
        }
        .table th {
            background: linear-gradient(90deg, rgba(127,127,213,0.18) 0%, rgba(86,204,242,0.18) 100%);
            color: #2f80ed;
            font-weight: 700;
            font-size: 1.08rem;
            border-bottom: 2px solid #b6d0f7;
        }
        body.dark-mode .table th {
            background: linear-gradient(90deg, rgba(57,58,90,0.92) 0%, rgba(35,41,70,0.92) 100%);
            color: #7f7fd5;
            border-bottom: 2px solid #393a5a;
            box-shadow: 0 2px 12px #7f7fd544 inset, 0 1.5px 8px #fff2 inset;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 12px 8px;
            background: rgba(86,204,242,0.10);
            backdrop-filter: blur(6px) saturate(120%);
            border-bottom: 1.5px solid rgba(127,127,213,0.10);
        }
        body.dark-mode .table td {
            background: rgba(35,41,70,0.72);
            color: #f3f6fa;
            border-bottom: 1.5px solid #232946;
        }
        /* 立体美化按钮 */
        .btn-info, .btn-success, .btn-danger {
            border: none;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 12px #7f7fd544, 0 1.5px 8px #fff8 inset;
            backdrop-filter: blur(8px) saturate(160%);
        }
        body.dark-mode .btn-info {
            background: linear-gradient(90deg, #7f7fd5 0%, #393a5a 100%);
            color: #fff;
            box-shadow: 0 4px 16px #7f7fd588, 0 0 8px 2px #7f7fd5, 0 0 0 1.5px #7f7fd544 inset, 0 1.5px 1.5px 0 #fff2 inset;
        }
        body.dark-mode .btn-success {
            background: linear-gradient(90deg, #43e97b 0%, #232946 100%);
            color: #fff;
            box-shadow: 0 4px 16px #43e97b88, 0 0 8px 2px #43e97b, 0 0 0 1.5px #43e97b44 inset, 0 1.5px 1.5px 0 #fff2 inset;
        }
        body.dark-mode .btn-danger {
            background: linear-gradient(90deg, #e53935 0%, #232946 100%);
            color: #fff;
            box-shadow: 0 4px 16px #e5393588, 0 0 8px 2px #e53935, 0 0 0 1.5px #e5393544 inset, 0 1.5px 1.5px 0 #fff2 inset;
        }
        .btn-info:before, .btn-success:before, .btn-danger:before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.18);
            border-radius: 100%;
            transform: translate(-50%, -50%);
            transition: width 0.5s cubic-bezier(.4,2,.6,1), height 0.5s cubic-bezier(.4,2,.6,1);
            z-index: 0;
        }
        .btn-info:hover:before, .btn-success:hover:before, .btn-danger:hover:before {
            width: 220%;
            height: 600%;
        }
        .btn-info > *, .btn-success > *, .btn-danger > * {
            position: relative;
            z-index: 1;
        }
        .btn-info:hover, .btn-success:hover, .btn-danger:hover {
            filter: brightness(1.13) drop-shadow(0 2px 12px #a1c4fd);
            transform: translateY(-4px) scale(1.08);
        }
        /* 审批按钮美化 */
        .approval-btn {
          border: none;
          border-radius: 22px;
          font-weight: 700;
          font-size: 1.08rem;
          padding: 8px 28px;
          margin: 0 4px 8px 0;
          box-shadow: 0 2px 16px 0 rgba(80,255,120,0.18), 0 1.5px 8px #fff8 inset;
          background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
          color: #fff;
          transition: all 0.22s cubic-bezier(.4,2,.6,1);
          position: relative;
          overflow: hidden;
          outline: none;
          display: inline-flex;
          align-items: center;
          gap: 8px;
          letter-spacing: 1px;
          filter: drop-shadow(0 0 8px #43e97b44);
        }
        .approval-btn.reject {
          background: linear-gradient(90deg, #e53935 0%, #e35d5b 100%);
          box-shadow: 0 2px 16px 0 rgba(255,80,80,0.18), 0 1.5px 8px #fff8 inset;
          filter: drop-shadow(0 0 8px #e5393544);
        }
        .approval-btn:disabled {
          background: linear-gradient(90deg, #bdbdbd 0%, #757575 100%);
          color: #eee;
          cursor: not-allowed;
          box-shadow: none;
          filter: none;
        }
        .approval-btn:hover:not(:disabled) {
          filter: brightness(1.12) drop-shadow(0 0 16px #43e97baa);
          transform: scale(1.08);
        }
        .approval-btn.reject:hover:not(:disabled) {
          filter: brightness(1.12) drop-shadow(0 0 16px #e53935aa);
          transform: scale(1.08);
        }
        /* 状态badge美化 */
        .approval-badge {
          display: inline-block;
          padding: 6px 18px;
          border-radius: 18px;
          font-weight: 700;
          font-size: 1.02rem;
          box-shadow: 0 2px 12px rgba(80,80,200,0.12);
          letter-spacing: 1px;
          margin: 0 2px;
          transition: background 0.3s, color 0.3s, box-shadow 0.3s;
          text-shadow: 0 1px 4px rgba(0,0,0,0.18);
          backdrop-filter: blur(4px);
          border: 1.5px solid rgba(255,255,255,0.12);
        }
        .approval-badge.pending {
          background: linear-gradient(90deg, #ffe066 0%, #ffd700 100%);
          color: #232946;
          box-shadow: 0 0 12px #ffe06688;
        }
        .approval-badge.approved {
          background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
          color: #fff;
          box-shadow: 0 0 12px #43e97b88;
        }
        .approval-badge.rejected {
          background: linear-gradient(90deg, #e53935 0%, #e35d5b 100%);
          color: #fff;
          box-shadow: 0 0 12px #e5393588;
        }
        /* 按钮组美化 */
        .approval-btn-group {
          display: flex;
          flex-wrap: wrap;
          gap: 8px;
          align-items: center;
          justify-content: flex-start;
        }
        @media (max-width: 900px) {
            .main-card { padding: 18px 2vw; }
            .table th, .table td { font-size: 0.98rem; }
        }
        @media (max-width: 600px) {
            .main-card { padding: 8px 0; border-radius: 12px; }
            h2 { font-size: 1.2rem; }
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
    </style>
</head>
<body>
<button class="night-toggle" id="nightToggleBtn" title="夜间/白天模式"><i class="fas fa-moon"></i></button>
<div class="main-card">
    <h2 class="mb-4"><?php echo $langArr['activity_manage'] ?? '全部活动管理'; ?></h2>
    <a href="dashboard.php" class="btn btn-secondary btn-sm mb-3"><i class="fa fa-arrow-left"></i> <?php echo $langArr['back'] ?? '返回'; ?></a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th><?php echo $langArr['title'] ?? '标题'; ?></th>
                <th><?php echo $langArr['date'] ?? '日期'; ?></th>
                <th><?php echo $langArr['location'] ?? '地点'; ?></th>
                <th><?php echo $langArr['organizer'] ?? '负责人'; ?></th>
                <th><?php echo $langArr['action'] ?? '操作'; ?></th>
                <th>审批状态</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($activities as $a): ?>
            <tr>
                <td><?php echo $a['id']; ?></td>
                <td><?php echo htmlspecialchars($a['title']); ?></td>
                <td><?php echo $a['date']; ?></td>
                <td><?php echo htmlspecialchars($a['location']); ?></td>
                <td><?php echo htmlspecialchars($a['organizer_name']); ?></td>
                <td>
                    <a href="activity_detail.php?id=<?php echo $a['id']; ?>" class="btn btn-info btn-sm">详情</a>
                    <?php if ($user['role'] === 'admin' || $user['role'] === 'teacher'): ?>
                        <a href="activity_edit.php?id=<?php echo $a['id']; ?>" class="btn btn-success btn-sm">编辑</a>
                        <a href="activity_delete.php?id=<?php echo $a['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo $langArr['confirm_delete_activity'] ?? '确定要删除该活动吗？'; ?>')">删除</a>
                        <?php if ($user['role'] === 'admin' && ($a['approval_status'] ?? 'pending') === 'pending'): ?>
                            <a href="activity_approve.php?id=<?php echo $a['id']; ?>" class="btn btn-success btn-sm">同意</a>
                            <a href="activity_reject.php?id=<?php echo $a['id']; ?>" class="btn btn-danger btn-sm">拒绝</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    $status = $a['approval_status'] ?? 'pending';
                    if ($status === 'approved') {
                        echo '<span class="approval-badge approved">已同意</span>';
                    } elseif ($status === 'rejected') {
                        echo '<span class="approval-badge rejected">已拒绝</span>';
                    } else {
                        echo '<span class="approval-badge pending">待审核</span>';
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
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