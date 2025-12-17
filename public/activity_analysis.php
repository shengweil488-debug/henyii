<?php
session_start();
require_once __DIR__ . '/../config/database.php';
// 加载语言包
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
// 活动总数
$total = $pdo->query('SELECT COUNT(*) FROM activities')->fetchColumn();
// 参与学生总数
$studentCount = $pdo->query('SELECT COUNT(DISTINCT student_id) FROM participants')->fetchColumn();
// 按类型统计
$typeStats = $pdo->query('SELECT event_type, COUNT(*) as cnt FROM activities GROUP BY event_type')->fetchAll(PDO::FETCH_ASSOC);
// 按月统计
$monthStats = $pdo->query("SELECT DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as cnt FROM activities GROUP BY month ORDER BY month")->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- 引入 Bootstrap 和 Chart.js -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<div class="night-toggle" id="nightToggleBtn" title="夜间/白天模式" style="position:fixed;top:32px;right:40px;z-index:1000;">
    <i class="fas fa-moon"></i>
</div>
<div class="page-container">
  <div class="container">
    <div class="page-header">
      <h1 class="page-title"><?php echo $langArr['activity_analysis'] ?? '活动数据分析'; ?></h1>
      <p class="page-subtitle"><?php echo $langArr['analysis_description'] ?? '查看活动数据，了解学生参与情况和趋势。'; ?></p>
    </div>
    <div class="stats-grid">
      <div class="stat-card gradient-blue">
        <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-number"><?php echo $total; ?></div>
        <div class="stat-label"><?php echo $langArr['total_activities'] ?? '活动总数'; ?></div>
      </div>
      <div class="stat-card gradient-green">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-number"><?php echo $studentCount; ?></div>
        <div class="stat-label"><?php echo $langArr['total_students'] ?? '参与学生总数'; ?></div>
      </div>
    </div>
    <div class="row g-4 mb-5">
      <div class="col-md-6">
        <div class="content-card">
          <h5 class="fw-bold mb-4" style="color:#1976d2;"><?php echo $langArr['stat_by_type'] ?? '按活动类型统计'; ?></h5>
          <?php foreach($typeStats as $t): ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="fw-semibold"><?php echo htmlspecialchars($t['event_type'] ?: ($langArr['uncategorized'] ?? '未分类')); ?></span>
                <span class="fw-bold text-primary"><?php echo $t['cnt']; ?></span>
              </div>
              <div class="progress" style="height:16px;">
                <div class="progress-bar bg-info" role="progressbar" style="width:<?php echo $total?round($t['cnt']/$total*100):0; ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-md-6">
        <div class="content-card">
          <h5 class="fw-bold mb-4" style="color:#1976d2;"><?php echo $langArr['stat_by_month'] ?? '按月份统计'; ?></h5>
          <div class="chart-container">
          <canvas id="monthChart" height="180"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
// 按月份统计图表
const monthData = <?php echo json_encode($monthStats); ?>;
const ctx = document.getElementById('monthChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: monthData.map(m=>m.month),
    datasets: [{
      label: <?php echo json_encode($langArr['activities'] ?? '活动数'); ?>,
      data: monthData.map(m=>m.cnt),
      backgroundColor: 'rgba(33,150,243,0.7)',
      borderRadius: 8
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});
</script>
<style>
body {
    background: linear-gradient(120deg,#a1c4fd 60%,#c2e9fb 100%);
    min-height: 100vh;
    font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
    color: #232946;
    transition: background 0.5s, color 0.5s;
}
.page-container {
    padding: 30px 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.content-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    margin-bottom: 30px;
}
.page-header {
    text-align: center;
    margin-bottom: 40px;
}
.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.page-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    font-weight: 400;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}
.stat-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(31, 38, 135, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}
.stat-card:hover::before {
    left: 100%;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.2);
}
.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}
.stat-label {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 500;
}
.chart-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(31, 38, 135, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}
.chart-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(102, 126, 234, 0.02), rgba(118, 75, 162, 0.02));
    pointer-events: none;
}
.chart-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    text-align: center;
}
.filter-section {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(31, 38, 135, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
}
.form-control {
    border-radius: 10px;
    border: 1.5px solid #e3f0fc;
    padding: 10px 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
}
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: white;
}
.btn-filter {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}
.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}
.btn-export {
    background: linear-gradient(45deg, #27ae60, #2ecc71);
    border: none;
    color: white;
    padding: 12px 25px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
    color: white;
    text-decoration: none;
}
.navbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 20px rgba(31, 38, 135, 0.1);
}
.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.nav-link {
    font-weight: 500;
    color: #2c3e50;
    transition: all 0.3s ease;
    border-radius: 8px;
    padding: 8px 16px;
}
.nav-link:hover {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}
.night-mode-toggle {
    background: rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.2);
    color: #667eea;
    padding: 8px 12px;
    border-radius: 20px;
    transition: all 0.3s ease;
}
.night-mode-toggle:hover {
    background: rgba(102, 126, 234, 0.2);
    transform: scale(1.05);
}
.night-toggle {
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
/* 夜间模式 */
body.dark-mode {
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
    color: #e3f2fd;
}
body.dark-mode .page-container {
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
}
body.dark-mode .content-card,
body.dark-mode .chart-container,
body.dark-mode .filter-section {
    background: rgba(26, 26, 46, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #e3f2fd;
}
body.dark-mode .page-title {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
body.dark-mode .page-subtitle {
    color: #e3f2fd;
    font-weight: 400;
}
body.dark-mode .stat-number {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
body.dark-mode .stat-label {
    color: #e3f2fd;
    font-weight: 500;
}
body.dark-mode .chart-title {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
body.dark-mode .form-control {
    background: rgba(26, 26, 46, 0.8);
    border-color: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-weight: 500;
}
body.dark-mode .form-control:focus {
    background: rgba(26, 26, 46, 0.95);
    border-color: #667eea;
    color: #ffffff;
}
body.dark-mode .navbar {
    background: rgba(26, 26, 46, 0.95);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
body.dark-mode .nav-link {
    color: #ffffff;
    font-weight: 500;
}
body.dark-mode .nav-link:hover {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}
body.dark-mode .navbar-brand {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
body.dark-mode .stat-card.gradient-blue {
    background: linear-gradient(90deg,#232946 60%,#7f7fd5 100%);
}
body.dark-mode .stat-card.gradient-green {
    background: linear-gradient(90deg,#232946 0%,#43e97b 100%);
}
body.dark-mode .progress {
    background: rgba(80,90,140,0.22);
}
body.dark-mode .progress-bar {
    background: linear-gradient(90deg, #7f7fd5 0%, #42a5f5 100%) !important;
    box-shadow: 0 2px 8px #7f7fd544;
}
body.dark-mode h1, body.dark-mode h5, body.dark-mode .display-4 {
    color: #fff !important;
    text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
}
</style> 
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