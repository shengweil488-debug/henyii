<?php
session_start();
if (!isset($_SESSION['user'])) {
    exit('Not logged in');
}
require_once __DIR__ . '/../src/controllers/ActivityController.php';
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';
$id = $_GET['activity_id'] ?? null;
if (!$id) exit('No activity id');
$activity = ActivityController::detail($id);
$demographics = [
    'Melayu' => ['L' => $activity['stat_malay_m'] ?? 0, 'P' => $activity['stat_malay_f'] ?? 0],
    'Cina' => ['L' => $activity['stat_chinese_m'] ?? 0, 'P' => $activity['stat_chinese_f'] ?? 0],
    'India' => ['L' => $activity['stat_indian_m'] ?? 0, 'P' => $activity['stat_indian_f'] ?? 0],
    'Lain-lain' => ['L' => $activity['stat_others_m'] ?? 0, 'P' => $activity['stat_others_f'] ?? 0],
];
$objectives = json_decode($activity['objectives'] ?? '[]', true);
$content = json_decode($activity['content'] ?? '[]', true);
$followup = json_decode($activity['followup'] ?? '[]', true);
$participants = ParticipantController::list($id);
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Heng Ee Activity System');
$pdf->SetAuthor('Heng Ee');
$pdf->SetTitle('Laporan Aktiviti');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();
$pdf->SetFont('stsongstdlight', '', 12);
// 标题
$html = '<table border="1" cellpadding="4" style="width:100%;"><tr><td align="center"><b>LAPORAN PERTANDINGAN / AKTIVITI / PROGRAM SEKOLAH 2025<br><span style="font-size:14px;">2025年比赛/活动/课程报表</span></b></td></tr></table>';
$html .= '<h2 style="text-align:center; font-weight:bold;">LAPORAN PERTANDINGAN / AKTIVITI / PROGRAM SEKOLAH 2025</h2>';
$html .= '<div style="text-align:center; font-size:14px;">2025年比赛/活动/课程报表</div>';
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
// 活动基本信息表格
$html .= '<table border="1" cellpadding="5" style="width:100%; margin-top:10px;">
<tr><td width="35%">TAJUK PROGRAM / PERTANDINGAN:</td><td>' . htmlspecialchars($activity['title']) . '</td></tr>
<tr><td>赛事 / 活动：</td><td>' . htmlspecialchars($activity['event_type']) . '</td></tr>
<tr><td>TEMPAT:</td><td>' . htmlspecialchars($activity['location']) . '</td></tr>
<tr><td>TARIKH:</td><td>' . htmlspecialchars($activity['date']) . '</td></tr>
<tr><td>ANJURAN:</td><td>' . htmlspecialchars($activity['organizer']) . '</td></tr>
<tr><td>PERINGKAT:</td><td>' . htmlspecialchars($activity['level']) . '</td></tr>
<tr><td>PENCAPAIAN:</td><td>' . htmlspecialchars($activity['achievement']) . '</td></tr>
<tr><td>NAMA GURU DIBIMBING:</td><td>' . htmlspecialchars($teacherName) . '</td></tr>
</table>';
// Peserta统计表
$html .= '<h4 style="margin-top:18px; border-left:5px solid #1976d2; padding-left:8px;">Peserta :</h4>';
$html .= '<b>JUMLAH BESAR : ' . array_sum(array_map(function($d){return $d['L']+$d['P'];}, $demographics)) . '</b>';
$html .= '<table border="1" cellpadding="4" style="width:100%; margin-top:5px;">
<tr style="background:#f5f5f5;"><td align="center" colspan="9"><b>Jumlah Pelajar yang terlibat</b></td></tr>
<tr><td></td><td colspan="2">Melayu</td><td colspan="2">Cina</td><td colspan="2">India</td><td colspan="2">Lain-lain</td></tr>
<tr><td></td><td>L</td><td>P</td><td>L</td><td>P</td><td>L</td><td>P</td><td>L</td><td>P</td></tr>
<tr><td></td>';
foreach ($demographics as $d) $html .= '<td>' . $d['L'] . '</td><td>' . $d['P'] . '</td>';
$html .= '</tr></table>';
// Objectives
$html .= '<h4 style="margin-top:18px; border-left:5px solid #1976d2; padding-left:8px;">Objektif :</h4>';
$html .= '<table border="1" cellpadding="4" style="width:100%;"><tr><td>'; $i=1;
foreach($objectives as $obj) $html .= 'a) ' . htmlspecialchars($obj) . '<br>';
$html .= '</td></tr></table>';
// Content
$html .= '<h4 style="margin-top:18px; border-left:5px solid #1976d2; padding-left:8px;">Kandungan (Aktiviti yg dilakukan) :</h4>';
$html .= '<table border="1" cellpadding="4" style="width:100%;"><tr><td>'; $i=1;
foreach($content as $c) $html .= 'a) ' . htmlspecialchars($c) . '<br>';
$html .= '</td></tr></table>';
// Follow-up
$html .= '<h4 style="margin-top:18px; border-left:5px solid #1976d2; padding-left:8px;">Penambahbaikan / Tindakan Susulan :</h4>';
$html .= '<table border="1" cellpadding="4" style="width:100%;"><tr><td>'; $i=1;
foreach($followup as $f) $html .= 'a) ' . htmlspecialchars($f) . '<br>';
$html .= '</td></tr></table>';
// 参与者表格
$html .= '<h4 style="margin-top:18px; border-left:5px solid #1976d2; padding-left:8px;">Senarai Peserta :</h4>';
$html .= '<table border="1" cellpadding="4" style="width:100%; font-size:11px;"><thead><tr style="background:#f5f5f5;"><th>#</th><th>Name</th><th>Chinese Name</th><th>Class</th><th>Student ID</th><th>Gender</th><th>Race</th><th>Religion</th><th>Achievement</th></tr></thead><tbody>';
$i=1;
foreach ($participants as $p) {
    // 优先用已有的chinese_name，否则查库
    $chinese_name = $p['chinese_name'] ?? '';
    if (!$chinese_name && !empty($p['student_id'])) {
        $stmt_cn = $pdo->prepare('SELECT chinese_name FROM students WHERE id = ?');
        $stmt_cn->execute([$p['student_id']]);
        $chinese_name = $stmt_cn->fetchColumn() ?: '';
    }
    $html .= '<tr>';
    $html .= '<td>' . $i++ . '</td>';
    $html .= '<td>' . htmlspecialchars($p['student_name'] ?? $p['name'] ?? $p['username'] ?? '') . '</td>';
    $html .= '<td>' . htmlspecialchars($chinese_name) . '</td>';
    $html .= '<td>' . htmlspecialchars($p['class'] ?? '') . '</td>';
    $html .= '<td>' . htmlspecialchars($p['student_no'] ?? '') . '</td>';
    $html .= '<td>' . htmlspecialchars($p['gender'] ?? '') . '</td>';
    $html .= '<td>' . htmlspecialchars($p['race'] ?? '') . '</td>';
    $html .= '<td>' . htmlspecialchars($p['religion'] ?? '') . '</td>';
    $html .= '<td>' . htmlspecialchars($p['achievement'] ?? '') . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';
// 页码
$pdf->writeHTML($html, true, false, true, false, '');
if (ob_get_length()) ob_end_clean();
$pdf->setFooterFont(Array('helvetica', '', 10));
$pdf->setPrintFooter(true);
$pdf->SetY(-15);
$pdf->Cell(0, 10, $pdf->getAliasNumPage().' / '.$pdf->getAliasNbPages(), 0, false, 'R');
$pdf->Output('activity_'.$id.'.pdf', 'D');
exit; 