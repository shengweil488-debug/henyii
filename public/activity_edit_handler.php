<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: dashboard.php'); exit; }
// 1. 获取表单数据
$title = $_POST['title'] ?? '';
$event_type = $_POST['event_type'] ?? '';
$location = $_POST['location'] ?? '';
$date = $_POST['date'] ?? '';
$organizer = isset($_POST['organizer']) ? implode(',', $_POST['organizer']) : '';
$level = $_POST['level'] ?? '';
$achievement = $_POST['achievement'] ?? '';
$teacher = $_POST['teacher'] ?? '';
$stat_malay_m = $_POST['stat_malay_m'] ?? 0;
$stat_chinese_m = $_POST['stat_chinese_m'] ?? 0;
$stat_indian_m = $_POST['stat_indian_m'] ?? 0;
$stat_others_m = $_POST['stat_others_m'] ?? 0;
$stat_malay_f = $_POST['stat_malay_f'] ?? 0;
$stat_chinese_f = $_POST['stat_chinese_f'] ?? 0;
$stat_indian_f = $_POST['stat_indian_f'] ?? 0;
$stat_others_f = $_POST['stat_others_f'] ?? 0;
$objectives = $_POST['objectives'] ?? [];
$content = $_POST['content'] ?? [];
$followup = $_POST['followup'] ?? [];
$visibility = $_POST['visibility'] ?? 'public';
// 2. 更新活动主表
$stmt = $pdo->prepare('UPDATE activities SET title=?, event_type=?, location=?, date=?, organizer=?, level=?, achievement=?, objectives=?, content=?, followup=?, visibility=?, teacher=?, stat_malay_m=?, stat_malay_f=?, stat_chinese_m=?, stat_chinese_f=?, stat_indian_m=?, stat_indian_f=?, stat_others_m=?, stat_others_f=? WHERE id=?');
$stmt->execute([
    $title, $event_type, $location, $date, $organizer, $level, $achievement,
    json_encode($objectives), json_encode($content), json_encode($followup), $visibility, $teacher,
    $stat_malay_m, $stat_malay_f, $stat_chinese_m, $stat_chinese_f, $stat_indian_m, $stat_indian_f, $stat_others_m, $stat_others_f,
    $id
]);
// 3. 先删除原有参与者，再插入新参与者
$pdo->prepare('DELETE FROM participants WHERE activity_id=?')->execute([$id]);
$names = $_POST['participant_name'] ?? [];
$ids = $_POST['participant_id'] ?? [];
$classes = $_POST['participant_class'] ?? [];
$genders = $_POST['participant_gender'] ?? [];
$races = $_POST['participant_race'] ?? [];
$religions = $_POST['participant_religion'] ?? [];
$achievements = $_POST['participant_achievement'] ?? [];
$chinese_names = $_POST['participant_chinese_name'] ?? [];
for ($i = 0; $i < count($names); $i++) {
    if (trim($names[$i]) === '' || trim($ids[$i]) === '') continue;
    $student_id = $ids[$i];
    $chinese_name = $chinese_names[$i] ?? '';
    // 检查 student_id 是否存在
    $stmt = $pdo->prepare('SELECT id FROM students WHERE student_no = ?');
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    if ($student) {
        // 更新中文名
        $stmt2 = $pdo->prepare('UPDATE students SET chinese_name=? WHERE id=?');
        $stmt2->execute([$chinese_name, $student['id']]);
        $real_student_id = $student['id'];
    } else {
        // 自动插入新学生（用学号、姓名、中文名，其他字段留空）
        $stmt = $pdo->prepare('INSERT INTO students (student_no, name, chinese_name) VALUES (?, ?, ?)');
        $stmt->execute([$student_id, $names[$i], $chinese_name]);
        $real_student_id = $pdo->lastInsertId();
    }
    $stmt = $pdo->prepare('INSERT INTO participants (activity_id, student_id, class, gender, race, achievement) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $id,
        $real_student_id,
        $classes[$i],
        $genders[$i],
        $races[$i],
        $achievements[$i]
    ]);
}
// 4. 处理文件上传（追加，不删除原有）
if (!empty($_FILES['activity_files']['name'][0])) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    foreach ($_FILES['activity_files']['tmp_name'] as $idx => $tmpName) {
        if ($_FILES['activity_files']['error'][$idx] === 0) {
            $name = basename($_FILES['activity_files']['name'][$idx]);
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $newName = uniqid('file_') . '.' . $ext;
            $target = $uploadDir . $newName;
            if (move_uploaded_file($tmpName, $target)) {
                $stmt = $pdo->prepare('INSERT INTO activity_files (activity_id, file_path, file_type) VALUES (?, ?, ?)');
                $stmt->execute([$id, 'uploads/' . $newName, $ext]);
            }
        }
    }
}
header('Location: dashboard.php');
exit; 