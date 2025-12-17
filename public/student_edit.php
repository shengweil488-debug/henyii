<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/StudentController.php';

// Load language pack
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: student_list.php'); exit; }
$student = StudentController::detail($id);
if (!$student) { header('Location: student_list.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_no = $_POST['student_no'];
    $name = $_POST['name'];
    $chinese_name = $_POST['chinese_name'] ?? '';
    $class = $_POST['class'];
    $gender = $_POST['gender'];
    $race = $_POST['race'];
    $religion = $_POST['religion'];
    $email = $_POST['email'];
    if (StudentController::update($id, $student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion)) {
        header('Location: student_list.php');
        exit;
    } else {
        $error = $langArr['update_failed'] ?? 'Failed to update student!';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['edit'] ?? 'Edit'; ?> <?php echo $langArr['student'] ?? 'Student'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .edit-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 8px 32px rgba(67, 126, 234, 0.13);
            padding: 48px 36px 36px 36px;
            margin: 48px 0;
            max-width: 540px;
            width: 100%;
        }
        h2 {
            color: #5a5ad7;
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 32px;
            text-align: center;
            letter-spacing: 2px;
        }
        .form-label {
            font-weight: 600;
            color: #5a5ad7;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-control, .form-select {
            border-radius: 18px;
            border: 1.5px solid #e3f0fc;
            font-size: 1.08rem;
            padding: 12px 18px;
            transition: border-color 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #5a5ad7;
            box-shadow: 0 0 0 2px #5a5ad733;
        }
        .btn-main {
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.15rem;
            padding: 12px 38px;
            margin: 0 8px 0 0;
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            border: none;
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
            transition: filter 0.2s, transform 0.2s;
        }
        .btn-main:hover {
            filter: brightness(1.08);
            transform: translateY(-2px) scale(1.03);
        }
        .btn-cancel {
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.15rem;
            padding: 12px 38px;
            background: #bdbdbd;
            color: #fff;
            border: none;
            margin-left: 0;
        }
        .btn-cancel:hover {
            filter: brightness(1.08);
            transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 600px) {
            .edit-card { padding: 18px 2vw; border-radius: 14px; }
            h2 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<div class="edit-card">
    <h2><i class="fas fa-user-edit me-2"></i><?php echo $langArr['edit'] ?? 'Edit'; ?> <?php echo $langArr['student'] ?? 'Student'; ?></h2>
    <?php if ($error): ?><div class="alert alert-danger mb-3"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-id-card"></i> <?php echo $langArr['student_id'] ?? 'Student No'; ?></label>
            <input type="text" name="student_no" class="form-control" value="<?php echo htmlspecialchars($student['student_no']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-user"></i> <?php echo $langArr['name'] ?? 'Name'; ?></label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-language"></i> <?php echo $langArr['chinese_name'] ?? 'Chinese Name'; ?></label>
            <input type="text" name="chinese_name" class="form-control" value="<?php echo htmlspecialchars($student['chinese_name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-school"></i> <?php echo $langArr['class'] ?? 'Class'; ?></label>
            <input type="text" name="class" class="form-control" value="<?php echo htmlspecialchars($student['class']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-venus-mars"></i> <?php echo $langArr['gender'] ?? 'Gender'; ?></label>
            <select name="gender" class="form-select">
                <option value="Male" <?php if($student['gender']==='Male') echo 'selected'; ?>><?php echo $langArr['male'] ?? 'Male'; ?></option>
                <option value="Female" <?php if($student['gender']==='Female') echo 'selected'; ?>><?php echo $langArr['female'] ?? 'Female'; ?></option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-flag"></i> <?php echo $langArr['race'] ?? 'Race'; ?></label>
            <input type="text" name="race" class="form-control" value="<?php echo htmlspecialchars($student['race']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-praying-hands"></i> <?php echo $langArr['religion'] ?? 'Religion'; ?></label>
            <input type="text" name="religion" class="form-control" value="<?php echo htmlspecialchars($student['religion'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-envelope"></i> <?php echo $langArr['email'] ?? 'Email'; ?></label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>">
        </div>
        <div class="d-flex justify-content-center mt-4 gap-3 flex-wrap">
            <button type="submit" class="btn btn-main"><i class="fas fa-save me-2"></i><?php echo $langArr['save'] ?? 'Save'; ?></button>
            <a href="student_list.php" class="btn btn-cancel"><i class="fas fa-times me-2"></i><?php echo $langArr['cancel'] ?? 'Cancel'; ?></a>
        </div>
    </form>
</div>
</body>
</html> 