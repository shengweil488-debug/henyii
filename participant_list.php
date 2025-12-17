<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
require_once __DIR__ . '/../src/controllers/ActivityController.php';
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$user = $_SESSION['user'];
$activity_id = $_GET['activity_id'] ?? null;
if (!$activity_id) { header('Location: dashboard.php'); exit; }
$activity = ActivityController::detail($activity_id);
if (!$activity) { header('Location: dashboard.php'); exit; }
$participants = ParticipantController::list($activity_id);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['participants']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2><?php echo $langArr['participants']; ?> - <?php echo htmlspecialchars($activity['title']); ?></h2>
    <div class="mb-3">
        <a href="participant_add.php?activity_id=<?php echo $activity_id; ?>" class="btn btn-success"><?php echo $langArr['add_participant']; ?></a>
        <a href="participant_import.php?activity_id=<?php echo $activity_id; ?>" class="btn btn-info"><?php echo $langArr['import_participants']; ?></a>
        <a href="participant_export_excel.php?activity_id=<?php echo $activity_id; ?>" class="btn btn-primary"><?php echo $langArr['export_excel']; ?></a>
        <a href="participant_export_pdf.php?activity_id=<?php echo $activity_id; ?>" class="btn btn-secondary"><?php echo $langArr['export_pdf']; ?></a>
        <a href="activity_detail.php?id=<?php echo $activity_id; ?>" class="btn btn-link"><?php echo $langArr['cancel']; ?></a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student No</th>
                <th>Name</th>
                <th><?php echo $langArr['username']; ?></th>
                <th><?php echo $langArr['action']; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($participants as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['student_no']); ?></td>
                <td><?php echo htmlspecialchars($p['student_name']); ?></td>
                <td><?php echo htmlspecialchars($p['username']); ?></td>
                <td>
                    <a href="participant_remove.php?id=<?php echo $p['id']; ?>&activity_id=<?php echo $activity_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><?php echo $langArr['remove_participant']; ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html> 