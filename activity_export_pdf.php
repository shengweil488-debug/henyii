<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ActivityController.php';
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
require_once __DIR__ . '/../vendor/autoload.php';
$activity_id = $_GET['activity_id'] ?? null;
if (!$activity_id) { exit('No activity id'); }
$activity = ActivityController::detail($activity_id);
$participants = ParticipantController::list($activity_id);
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Heng Ee Activity System');
$pdf->SetAuthor('Heng Ee');
$pdf->SetTitle('Activity Report');
$pdf->SetMargins(18, 18, 18);
$pdf->AddPage();
$pdf->SetFont('stsongstdlight', '', 14); // 中文支持
// ======= 标题区块 =======
$pdf->SetFillColor(25, 118, 210);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('', 'B', 22);
$pdf->Cell(0, 18, '活动报告 Activity Report', 0, 1, 'C', 1);
$pdf->Ln(2);
// ======= 活动标题 =======
$pdf->SetFont('', 'B', 18);
$pdf->SetTextColor(25, 118, 210);
$pdf->Cell(0, 12, htmlspecialchars($activity['title']), 0, 1, 'C');
$pdf->Ln(2);
// ======= 活动信息卡片 =======
$pdf->SetFillColor(227, 240, 252);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('', '', 13);
$pdf->Cell(40, 10, "📅 日期", 0, 0, 'R', 0);
$pdf->Cell(60, 10, $activity['date'], 0, 0, 'L', 0);
$pdf->Cell(40, 10, "📍 地点", 0, 0, 'R', 0);
$pdf->Cell(60, 10, htmlspecialchars($activity['location']), 0, 1, 'L', 0);
$pdf->Cell(40, 10, "📝 描述", 0, 0, 'R', 0);
$pdf->Cell(160, 10, htmlspecialchars($activity['description']), 0, 1, 'L', 0);
$pdf->Ln(4);
// ======= 参与者表格卡片 =======
$pdf->SetFont('', 'B', 15);
$pdf->SetTextColor(25, 118, 210);
$pdf->Cell(0, 12, '参与者列表 Participants', 0, 1, 'C');
$pdf->SetTextColor(0,0,0);
// 表格外卡片
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(227, 240, 252);
$pdf->SetLineWidth(0.7);
$pdf->RoundedRect(16, $pdf->GetY(), 265, 10 + (count($participants)+1)*10, 6, '1234', 'DF', array(), array(246,250,253));
$pdf->Ln(2);
$pdf->SetY($pdf->GetY()+2);
// 表头渐变
$pdf->SetFont('', 'B', 12);
$pdf->SetFillColor(25, 118, 210);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(18, 10, 'ID', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '学号', 0, 0, 'C', 1);
$pdf->Cell(50, 10, '姓名', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '用户名', 0, 0, 'C', 1);
$pdf->Cell(30, 10, '班级', 0, 0, 'C', 1);
$pdf->Cell(20, 10, '性别', 0, 0, 'C', 1);
$pdf->Cell(30, 10, '民族', 0, 0, 'C', 1);
$pdf->Cell(30, 10, '宗教', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '成就', 0, 1, 'C', 1);
// 斑马条纹
$pdf->SetFont('', '', 12);
$fill = 0;
foreach ($participants as $p) {
    if ($fill) {
        $pdf->SetFillColor(227, 240, 252);
    } else {
        $pdf->SetFillColor(255,255,255);
    }
    $pdf->SetTextColor(0,0,0);
    $pdf->Cell(18, 10, $p['id'], 0, 0, 'C', 1);
    $pdf->Cell(40, 10, htmlspecialchars($p['student_no']), 0, 0, 'C', 1);
    $pdf->Cell(50, 10, htmlspecialchars($p['student_name']), 0, 0, 'C', 1);
    $pdf->Cell(40, 10, htmlspecialchars($p['username']), 0, 0, 'C', 1);
    $pdf->Cell(30, 10, htmlspecialchars($p['class'] ?? ''), 0, 0, 'C', 1);
    $pdf->Cell(20, 10, htmlspecialchars($p['gender'] ?? ''), 0, 0, 'C', 1);
    $pdf->Cell(30, 10, htmlspecialchars($p['race'] ?? ''), 0, 0, 'C', 1);
    $pdf->Cell(30, 10, htmlspecialchars($p['religion'] ?? ''), 0, 0, 'C', 1);
    $pdf->Cell(40, 10, htmlspecialchars($p['achievement'] ?? ''), 0, 1, 'C', 1);
    $fill = !$fill;
}
// ======= 页脚 =======
$pdf->SetY(-18);
$pdf->SetFont('', '', 10);
$pdf->SetTextColor(120,120,120);
$pdf->Cell(0, 10, '导出时间：' . date('Y-m-d H:i:s') . '   Powered by Heng Ee Activity System', 0, 0, 'R');
$pdf->Output('activity_' . $activity_id . '.pdf', 'D');
exit; 