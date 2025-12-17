<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
require_once __DIR__ . '/../src/models/User.php';
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$activity_id = $_GET['activity_id'] ?? null;
if (!$activity_id) { header('Location: dashboard.php'); exit; }
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {
    $file = $_FILES['csv']['tmp_name'];
    if (($handle = fopen($file, 'r')) !== false) {
        $row = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if ($row === 0) { $row++; continue; } // 跳过表头
            $username = $data[0];
            $user = User::findByUsername($username);
            if ($user) {
                ParticipantController::add($activity_id, $user['id']);
            }
            $row++;
        }
        fclose($handle);
        $success = 'Import completed!';
    } else {
        $error = 'Failed to open file!';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['import_participants']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2><?php echo $langArr['import_participants']; ?></h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">CSV (username as first column)</label>
            <input type="file" name="csv" class="form-control" accept=".csv" required>
        </div>
        <button type="submit" class="btn btn-info"><?php echo $langArr['import_participants']; ?></button>
        <a href="participant_list.php?activity_id=<?php echo $activity_id; ?>" class="btn btn-secondary"><?php echo $langArr['cancel']; ?></a>
    </form>
    <div class="mt-3">
        <strong>CSV格式示例：</strong><br>
        username<br>
        alice<br>
        bob<br>
    </div>
</div>
</body>
</html> 