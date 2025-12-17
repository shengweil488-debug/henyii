<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/vendor/autoload.php';
require_once '../src/models/Student.php';
require_once __DIR__ . '/../src/controllers/StudentController.php';

// Load language pack
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/lang/' . $lang . '.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_no = $_POST['student_id'];
    $name = $_POST['name'];
    $chinese_name = $_POST['chinese_name'] ?? '';
    $class = $_POST['class'];
    $gender = $_POST['gender'];
    $race = $_POST['race'];
    $religion = $_POST['religion'];
    $email = $_POST['email'];
    if (StudentController::create($student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion)) {
        header('Location: student_list.php');
        exit;
    } else {
        $error = $langArr['add_failed'] ?? 'Failed to add student!';
    }
}
?>
<!-- 引入 Bootstrap 和 FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="container my-5">
  <div class="card shadow rounded-4 p-4" style="max-width:500px;margin:auto;">
    <h2 class="fw-bold mb-4 text-center" style="color:#1976d2;letter-spacing:2px;">
        <?php echo $langArr['add_student'] ?? 'Add Student'; ?>
    </h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <form method="post" action="">
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['student_id'] ?? 'Student No'; ?></label>
        <input type="text" name="student_id" class="form-control rounded-pill" required>
      </div>
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['name'] ?? 'Name'; ?></label>
        <input type="text" name="name" class="form-control rounded-pill" required>
      </div>
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['chinese_name'] ?? 'Chinese Name'; ?></label>
        <input type="text" name="chinese_name" class="form-control rounded-pill">
      </div>
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['class'] ?? 'Class'; ?></label>
        <input type="text" name="class" class="form-control rounded-pill">
      </div>
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['gender'] ?? 'Gender'; ?></label>
        <select name="gender" class="form-select rounded-pill">
          <option value="男"><?php echo $langArr['male'] ?? 'Male'; ?></option>
          <option value="女"><?php echo $langArr['female'] ?? 'Female'; ?></option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['race'] ?? 'Race'; ?></label>
        <input type="text" name="race" class="form-control rounded-pill">
      </div>
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['religion'] ?? 'Religion'; ?></label>
        <input type="text" name="religion" class="form-control rounded-pill">
      </div>
      <div class="mb-3">
        <label class="form-label"><?php echo $langArr['email'] ?? 'Email'; ?></label>
        <input type="email" name="email" class="form-control rounded-pill">
      </div>
      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary px-4 rounded-pill"><i class="fa fa-plus"></i> <?php echo $langArr['add'] ?? 'Add'; ?></button>
        <a href="student_list.php" class="btn btn-secondary px-4 rounded-pill"><?php echo $langArr['back'] ?? 'Back'; ?></a>
      </div>
    </form>
  </div>
</div>
<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.card { border: none; }
.form-control, .form-select { border-radius: 20px; }
.btn { border-radius: 20px; font-weight: 600; }
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
body.dark-mode {
    background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
    color: #f3f6fa;
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
body.dark-mode .table {
    background: #232946 !important;
    color: #f3f6fa !important;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 1.5px #7f7fd5 inset;
    border-radius: 12px;
}
body.dark-mode th, body.dark-mode td {
    color: #f3f6fa !important;
}
body.dark-mode .table-hover tbody tr:hover {
    background: linear-gradient(90deg, #232946 0%, #393a5a 100%) !important;
    box-shadow: 0 2px 12px #7f7fd544;
}
body.dark-mode .btn, body.dark-mode .btn-feature {
    color: #fff !important;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 2px #a1c4fd55 inset;
    border-radius: 22px;
}
</style> 
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