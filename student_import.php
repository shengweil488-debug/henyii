<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../src/controllers/StudentController.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/models/Student.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
$error = '';
$success = '';
$imported = 0;
$skipped = 0;
$failed = 0;
$failRows = [];
$templateUrl = 'student_import_template.xlsx'; // 模板文件名
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel'])) {
    $file = $_FILES['excel']['tmp_name'];
    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        if (count($rows) < 2) throw new Exception('Excel无有效数据');
        // 自动识别表头
        $header = array_map('trim', $rows[0]);
        $map = [
            '学号' => null, '姓名' => null, '华文名字' => null, 'chinese_name' => null, '班级' => null, '性别' => null, '民族' => null, '宗教' => null, '邮箱' => null
        ];
        foreach ($header as $i => $h) {
            foreach ($map as $k => $v) {
                if (strpos($h, $k) !== false) $map[$k] = $i;
            }
        }
        if ($map['学号'] === null || $map['姓名'] === null) throw new Exception('表头需包含“学号”和“姓名”');
        for ($rowNum = 1; $rowNum < count($rows); $rowNum++) {
            $row = $rows[$rowNum];
            $student_no = $row[$map['学号']] ?? '';
            $name = $row[$map['姓名']] ?? '';
            $chinese_name = '';
            if ($map['华文名字'] !== null) $chinese_name = $row[$map['华文名字']] ?? '';
            elseif ($map['chinese_name'] !== null) $chinese_name = $row[$map['chinese_name']] ?? '';
            $class = $map['班级'] !== null ? ($row[$map['班级']] ?? '') : '';
            $gender = $map['性别'] !== null ? ($row[$map['性别']] ?? '') : '';
            $race = $map['民族'] !== null ? ($row[$map['民族']] ?? '') : '';
            $religion = $map['宗教'] !== null ? ($row[$map['宗教']] ?? '') : '';
            $email = $map['邮箱'] !== null ? ($row[$map['邮箱']] ?? '') : '';
            // 自动补全学号或姓名
            if (!$student_no && $name) {
                $student = StudentController::findByName($name);
                if ($student) $student_no = $student['student_no'];
            }
            if (!$name && $student_no) {
                $student = StudentController::detail($student_no);
                if ($student) $name = $student['name'];
            }
            if (!$student_no || !$name) { $failed++; $failRows[] = $rowNum+1; continue; }
            if (StudentController::detail($student_no)) { $skipped++; continue; } // 查重
            if (StudentController::create($student_no, $name, $chinese_name, $class, $gender, $race, $email, $religion)) {
                $imported++;
            } else {
                $failed++;
                $failRows[] = $rowNum+1;
            }
        }
        $success = "导入成功{$imported}条，跳过{$skipped}条，失败{$failed}条";
        if ($failed) $error = '失败行: '.implode(',', $failRows);
    } catch (Exception $e) {
        $error = '导入失败: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['import_students'] ?? '导入学生（Excel）'; ?></title>
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
            box-shadow: 0 8px 32px rgba(67,206,162,0.13);
            padding: 40px 32px 32px 32px;
            margin: 48px 0;
            max-width: 700px;
            width: 100%;
        }
        h2 {
            color: #43cea2;
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
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
            transition: filter 0.2s, transform 0.2s;
        }
        .btn-primary {
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
            color: #43cea2;
            margin-bottom: 6px;
        }
        .form-control {
            border-radius: 18px;
            border: 1.5px solid #e3f0fc;
            font-size: 1.08rem;
            padding: 10px 16px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #43cea2;
            box-shadow: 0 0 0 2px #43cea233;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        table {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            margin-top: 18px;
            box-shadow: 0 2px 12px rgba(67,206,162,0.06);
        }
        th {
            background: #e3f0fc;
            color: #43cea2;
            font-weight: 700;
            font-size: 1.08rem;
            border-bottom: 2px solid #b6d0f7;
        }
        td, th {
            vertical-align: middle;
            text-align: center;
            padding: 12px 8px;
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
        @media (max-width: 900px) {
            .main-card { padding: 18px 2vw; }
            th, td { font-size: 0.98rem; }
        }
        @media (max-width: 600px) {
            .main-card { padding: 8px 0; border-radius: 12px; }
            h2 { font-size: 1.2rem; }
        }
        .button-row {
          display: flex;
          gap: 16px;
          align-items: center;
        }
        .btn {
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 22px;
          font-weight: 700;
          font-size: 1.08rem;
          padding: 8px 28px;
          border: none;
          min-width: 100px;
          height: 44px;
          box-shadow: 0 2px 8px rgba(67,206,162,0.08);
          transition: filter 0.2s, transform 0.2s;
        }
        .btn i {
          margin-right: 8px;
          font-size: 1.2em;
        }
        .btn-save {
          background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
          color: #fff;
        }
        .btn-cancel {
          background: #bdbdbd;
          color: #fff;
        }
        .btn:hover {
          filter: brightness(1.08);
          transform: translateY(-2px) scale(1.03);
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
</head>
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
<div class="main-card">
    <h2 class="mb-4"><?php echo $langArr['import_students'] ?? '导入学生（Excel）'; ?></h2>
      <div class="text-center mb-4">
        <span class="import-title px-4 py-2 rounded-4 d-inline-block mb-2 shadow"><?php echo $langArr['import_students'] ?? '导入学生（Excel）'; ?></span>
      </div>
      <?php if ($error || $success): ?>
      <div class="mb-4">
        <?php if ($success): ?>
          <div class="alert border-0 rounded-4 shadow-sm d-flex align-items-center mb-2" style="background:linear-gradient(90deg,#e3f2fd 60%,#bbdefb 100%);color:#1976d2;">
            <span class="material-icons me-2" style="font-size:2rem;">task_alt</span>
            <div><?php echo $success; ?></div>
          </div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert border-0 rounded-4 shadow-sm d-flex align-items-center mb-2" style="background:linear-gradient(90deg,#ffebee 60%,#ffcdd2 100%);color:#d32f2f;">
            <span class="material-icons me-2" style="font-size:2rem;">error_outline</span>
            <div><?php echo $error; ?></div>
          </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3 import-upload-card p-3 rounded-4 shadow-sm">
          <label class="form-label fw-bold"><?php echo $langArr['select_excel_file'] ?? '选择Excel文件'; ?></label>
          <input type="file" name="excel" class="form-control mb-2" required accept=".xls,.xlsx">
          <a href="<?php echo $templateUrl; ?>" class="btn btn-template w-100">⬇ <?php echo $langArr['download_template'] ?? '下载导入模板'; ?></a>
        </div>
        <div class="button-row mt-3">
          <button type="submit" class="btn btn-save"><i class="fa fa-upload"></i> <?php echo $langArr['import'] ?? '导入'; ?></button>
        </div>
      </form>
    </div>
</body>
</html> 