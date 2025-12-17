<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ActivityController.php';
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
require_once __DIR__ . '/../config/database.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: dashboard.php'); exit; }
$activity = ActivityController::detail($id);
if (!$activity) { header('Location: dashboard.php'); exit; }
// Demographics
$demographics = [
    'Malay' => ['M' => $activity['stat_malay_m'] ?? 0, 'F' => $activity['stat_malay_f'] ?? 0],
    'Chinese' => ['M' => $activity['stat_chinese_m'] ?? 0, 'F' => $activity['stat_chinese_f'] ?? 0],
    'Indian' => ['M' => $activity['stat_indian_m'] ?? 0, 'F' => $activity['stat_indian_f'] ?? 0],
    'Other' => ['M' => $activity['stat_others_m'] ?? 0, 'F' => $activity['stat_others_f'] ?? 0],
];
// 目标、内容、后续
$objectives = json_decode($activity['objectives'] ?? '[]', true);
$content = json_decode($activity['content'] ?? '[]', true);
$followup = json_decode($activity['followup'] ?? '[]', true);
// 参与者
$participants = ParticipantController::list($id);
// 查询图片
$stmt = $pdo->prepare('SELECT * FROM activity_files WHERE activity_id = ?');
$stmt->execute([$id]);
$files = $stmt->fetchAll();
// 分类图片
$evidence = [];
$photos = [];
foreach ($files as $f) {
    if (stripos($f['file_type'], 'pdf') !== false) {
        $evidence[] = $f;
    } else {
        $photos[] = $f;
    }
}
// 获取老师姓名
$teacherName = '';
if (!empty($activity['teacher']) && is_numeric($activity['teacher'])) {
    $stmt = $pdo->prepare('SELECT name FROM users WHERE id = ?');
    $stmt->execute([$activity['teacher']]);
    $teacherName = $stmt->fetchColumn();
}
if (!$teacherName) {
    $teacherName = $activity['teacher'] ?? '';
}
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['activity_detail'] ?? 'Activity Details'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #56ccf2 0%, #2f80ed 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            transition: background 0.5s, color 0.5s;
        }
        body.dark-mode {
            background: linear-gradient(135deg, #181c2a 0%, #232946 100%);
            color: #e3eaf2;
        }
        body.dark-mode .card {
            background: rgba(24,28,42,0.92);
            box-shadow: 0 8px 32px #181c2a99, 0 0 24px 4px #7f7fd5, 0 0 0 2.5px #232946 inset, 0 1.5px 1.5px 0 #232946 inset;
        }
        body.dark-mode .card-header {
            background: linear-gradient(90deg, #232946 0%, #393a5a 100%);
            color: #a1c4fd !important;
            text-shadow: 0 2px 8px #232946, 0 1px 2px #7f7fd5;
        }
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
            color: #a1c4fd !important;
            text-shadow: 0 2px 8px #232946, 0 1px 2px #7f7fd5;
        }
        body.dark-mode .badge.bg-info {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
            color: #e3eaf2;
        }
        body.dark-mode .table {
            background: rgba(24,28,42,0.92);
            color: #e3eaf2;
        }
        body.dark-mode .table th {
            background: linear-gradient(90deg, #232946 0%, #393a5a 100%);
            color: #a1c4fd;
            border-bottom: 2px solid #393a5a;
            box-shadow: 0 2px 12px #232946cc inset, 0 1.5px 8px #7f7fd544 inset;
        }
        body.dark-mode .table td {
            background: rgba(35,41,70,0.72);
            color: #e3eaf2;
            border-bottom: 1.5px solid #232946;
        }
        body.dark-mode strong, body.dark-mode b, body.dark-mode th {
            color: #fff !important;
            font-weight: 800;
            text-shadow: 0 1px 4px #232946cc, 0 0.5px 0.5px #7f7fd5;
        }
        body.dark-mode td, body.dark-mode li, body.dark-mode p, body.dark-mode span, body.dark-mode label {
            color: #e3eaf2 !important;
            font-weight: 500;
            text-shadow: 0 1px 4px #181c2a99, 0 0.5px 0.5px #7f7fd5;
        }
        .card-body {
            padding: 28px 22px 18px 22px;
        }
        h4, h5, h6 {
            font-weight: 800;
            color: #2f80ed;
            text-shadow: 0 2px 8px #7f7fd544, 0 1px 2px #fff8;
        }
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
            color: #7f7fd5;
            text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
        }
        .badge.bg-info {
            background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
            color: #fff;
            font-weight: 700;
            border-radius: 12px;
            font-size: 1rem;
            box-shadow: 0 2px 8px #7f7fd544;
        }
        body.dark-mode .badge.bg-info {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
            color: #fff;
        }
        .table {
            background: rgba(227,240,252,0.18);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 8px;
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
        .photo-card {
            width: 160px;
            margin: 0 10px 10px 0;
            background: rgba(255,255,255,0.12);
            border-radius: 16px;
            box-shadow: 0 2px 8px #7f7fd544;
            overflow: hidden;
        }
        .photo-card img { width: 100%; height: 160px; object-fit: cover; }
        .photo-card:hover {
            box-shadow: 0 8px 32px #7f7fd544, 0 0 0 2.5px #7f7fd544 inset;
            filter: brightness(1.08);
        }
        body.dark-mode .photo-card {
            background: rgba(35,41,70,0.72);
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
        body.dark-mode .card-body, body.dark-mode .card-body * {
            color: #e3eaf2 !important;
            font-weight: 500;
            text-shadow: 0 1px 4px #181c2a99, 0 0.5px 0.5px #7f7fd5;
        }
        body.dark-mode .card-body strong, body.dark-mode .card-body b, body.dark-mode .card-body label {
            color: #fff !important;
            font-weight: 800;
            text-shadow: 0 1px 4px #232946cc, 0 0.5px 0.5px #7f7fd5;
        }
        body.dark-mode .card-body span, body.dark-mode .card-body br {
            color: #e3eaf2 !important;
        }
    </style>
</head>
<body>
<button class="night-toggle" id="nightToggleBtn" title="夜间/白天模式"><i class="fas fa-moon"></i></button>
<div class="container mt-4 mb-4">
    <!-- 活动详情 -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['activity_detail'] ?? 'Activity Details'; ?>
            <span class="float-end badge bg-info"><?php echo $langArr[$activity['visibility']] ?? ucfirst($activity['visibility'] ?? 'public'); ?></span>
        </div>
        <div class="card-body">
            <h4><?php echo htmlspecialchars($activity['title']); ?></h4>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong><?php echo $langArr['event_type'] ?? 'Event Type'; ?>:</strong> <?php echo htmlspecialchars($activity['event_type']); ?><br>
                    <strong><?php echo $langArr['date'] ?? 'Date'; ?>:</strong> <?php echo htmlspecialchars($activity['date']); ?><br>
                    <strong><?php echo $langArr['location'] ?? 'Location'; ?>:</strong> <?php echo htmlspecialchars($activity['location']); ?><br>
                    <strong><?php echo $langArr['organizer'] ?? 'Organizer'; ?>:</strong> <?php echo htmlspecialchars($activity['organizer']); ?><br>
                </div>
                <div class="col-md-6">
                    <strong><?php echo $langArr['level'] ?? 'Level'; ?>:</strong> <?php echo htmlspecialchars($activity['level']); ?><br>
                    <strong><?php echo $langArr['achievement'] ?? 'Achievement'; ?>:</strong> <?php echo htmlspecialchars($activity['achievement']); ?><br>
                    <span class="info-label"><i class="fa fa-chalkboard-teacher"></i> <?php echo $langArr['teacher'] ?? 'Teacher'; ?>:</span> <?php echo htmlspecialchars($teacherName); ?><br>
                    <strong><?php echo $langArr['created_at'] ?? 'Created At'; ?>:</strong> <?php echo htmlspecialchars($activity['created_at']); ?><br>
                </div>
            </div>
        </div>
    </div>
    <!-- Demographics -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['demographics'] ?? 'Demographics'; ?></div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php echo $langArr['malay'] ?? 'Malay'; ?></th>
                        <th><?php echo $langArr['chinese'] ?? 'Chinese'; ?></th>
                        <th><?php echo $langArr['indian'] ?? 'Indian'; ?></th>
                        <th><?php echo $langArr['others'] ?? 'Other'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $langArr['num_students_involved'] ?? 'Number of Students Involved'; ?></td>
                        <td><?php echo ($demographics['Malay']['M']+$demographics['Malay']['F']); ?></td>
                        <td><?php echo ($demographics['Chinese']['M']+$demographics['Chinese']['F']); ?></td>
                        <td><?php echo ($demographics['Indian']['M']+$demographics['Indian']['F']); ?></td>
                        <td><?php echo ($demographics['Other']['M']+$demographics['Other']['F']); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $langArr['male'] ?? 'Male'; ?></td>
                        <td><?php echo $demographics['Malay']['M']; ?></td>
                        <td><?php echo $demographics['Chinese']['M']; ?></td>
                        <td><?php echo $demographics['Indian']['M']; ?></td>
                        <td><?php echo $demographics['Other']['M']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $langArr['female'] ?? 'Female'; ?></td>
                        <td><?php echo $demographics['Malay']['F']; ?></td>
                        <td><?php echo $demographics['Chinese']['F']; ?></td>
                        <td><?php echo $demographics['Indian']['F']; ?></td>
                        <td><?php echo $demographics['Other']['F']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Objectives -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['objectives'] ?? 'Objectives'; ?></div>
        <div class="card-body">
            <ol><?php foreach($objectives as $obj) echo '<li>'.htmlspecialchars($obj).'</li>'; ?></ol>
        </div>
    </div>
    <!-- Content -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['activity_content'] ?? 'Content (Activities conducted)'; ?></div>
        <div class="card-body">
            <ol><?php foreach($content as $c) echo '<li>'.htmlspecialchars($c).'</li>'; ?></ol>
        </div>
    </div>
    <!-- Follow-up Actions -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['followup_action'] ?? 'Improvements / Follow-up Actions'; ?></div>
        <div class="card-body">
            <ol><?php foreach($followup as $f) echo '<li>'.htmlspecialchars($f).'</li>'; ?></ol>
        </div>
    </div>
    <!-- Student Participants -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['student_participants'] ?? 'Student Participants'; ?></div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $langArr['name'] ?? 'Name'; ?></th>
                        <th><?php echo $langArr['chinese_name'] ?? 'Chinese Name'; ?></th>
                        <th><?php echo $langArr['class'] ?? 'Class'; ?></th>
                        <th><?php echo $langArr['student_id'] ?? 'Student ID'; ?></th>
                        <th><?php echo $langArr['gender'] ?? 'Gender'; ?></th>
                        <th><?php echo $langArr['race'] ?? 'Race'; ?></th>
                        <th><?php echo $langArr['religion'] ?? 'Religion'; ?></th>
                        <th><?php echo $langArr['achievement'] ?? 'Achievement'; ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; foreach ($participants as $p): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($p['student_name'] ?? $p['name'] ?? $p['username'] ?? ''); ?></td>
                        <td><?php
                            $chinese_name = $p['chinese_name'] ?? '';
                            if (!$chinese_name && !empty($p['student_id'])) {
                                $stmt_cn = $pdo->prepare('SELECT chinese_name FROM students WHERE id = ?');
                                $stmt_cn->execute([$p['student_id']]);
                                $chinese_name = $stmt_cn->fetchColumn() ?: '';
                            }
                            echo htmlspecialchars($chinese_name);
                        ?></td>
                        <td><?php echo htmlspecialchars($p['class'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($p['student_no'] ?? $p['participant_id'] ?? $p['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($p['gender'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($p['race'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($p['religion'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($p['achievement'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Evidence of Achievement -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['evidence_of_achievement'] ?? 'Evidence of Achievement'; ?></div>
        <div class="card-body d-flex flex-wrap">
            <?php foreach ($evidence as $f): ?>
                <div class="card photo-card">
                    <?php if (stripos($f['file_type'], 'pdf') !== false): ?>
                        <a href="../<?php echo $f['file_path']; ?>" target="_blank"><?php echo $langArr['view_pdf'] ?? 'View PDF'; ?></a>
                    <?php else: ?>
                        <img src="../<?php echo $f['file_path']; ?>" alt="<?php echo $langArr['evidence_of_achievement'] ?? 'Evidence of achievement'; ?>">
                    <?php endif; ?>
                    <div class="card-body p-2 text-center"><?php echo $langArr['evidence_of_achievement'] ?? 'Evidence of achievement'; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Activity Photos -->
    <div class="card mb-3">
        <div class="card-header"><?php echo $langArr['activity_photos'] ?? 'Activity Photos'; ?></div>
        <div class="card-body d-flex flex-wrap">
            <?php foreach ($photos as $f): ?>
                <div class="card photo-card">
                    <img src="../<?php echo $f['file_path']; ?>" alt="<?php echo $langArr['activity_photo'] ?? 'Activity photo'; ?>">
                    <div class="card-body p-2 text-center"><?php echo $langArr['activity_photo'] ?? 'Activity photo'; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- 底部按钮 -->
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">&larr; <?php echo $langArr['back'] ?? 'Back'; ?></a>
        <a href="activity_edit.php?id=<?php echo $id; ?>" class="btn btn-primary"><?php echo $langArr['edit_report'] ?? 'Edit Report'; ?></a>
        <a href="participant_export_pdf.php?activity_id=<?php echo $id; ?>" class="btn btn-danger"><?php echo $langArr['export_pdf'] ?? 'Export PDF'; ?></a>
        <a href="participant_export_excel.php?activity_id=<?php echo $id; ?>" class="btn btn-success"><?php echo $langArr['export_excel'] ?? 'Export Excel'; ?></a>
        <a href="#" class="btn btn-info" onclick="shareToFacebook();return false;"><i class="fab fa-facebook me-1"></i>分享到Facebook</a>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
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

function shareToFacebook() {
    var url = window.location.href;
    var title = <?php echo json_encode($activity['title'] ?? ''); ?>;
    var shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url) + '&quote=' + encodeURIComponent(title);
    window.open(shareUrl, '_blank', 'width=600,height=500');
}
</script>
</body>
</html> 