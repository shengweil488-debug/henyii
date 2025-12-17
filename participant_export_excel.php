<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ActivityController.php';
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$row = 1;
// Load language pack
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
// 标题
$sheet->mergeCells("A$row:I$row");
$sheet->setCellValue("A$row", $langArr['report_title_full'] ?? 'LAPORAN PERTANDINGAN / AKTIVITI / PROGRAM SEKOLAH 2025  2025年比赛/活动/课程报表');
$sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(16);
$row++;
$sheet->mergeCells("A$row:I$row");
$sheet->setCellValue("A$row", $langArr['report_title_ms'] ?? 'LAPORAN PERTANDINGAN / AKTIVITI / PROGRAM SEKOLAH 2025');
$sheet->getStyle("A$row")->getFont()->setBold(true)->setSize(14);
$row++;
$sheet->mergeCells("A$row:I$row");
$sheet->setCellValue("A$row", $langArr['report_title_zh'] ?? '2025年比赛/活动/课程报表');
$row += 2;
// 活动基本信息表格
$sheet->setCellValue("A$row", $langArr['program_title'] ?? 'TAJUK PROGRAM / PERTANDINGAN:');
$sheet->setCellValue("B$row", $activity['title']); $row++;
$sheet->setCellValue("A$row", $langArr['event_type_label'] ?? '赛事 / 活动:');
$sheet->setCellValue("B$row", $activity['event_type']); $row++;
$sheet->setCellValue("A$row", $langArr['location_label'] ?? 'TEMPAT:');
$sheet->setCellValue("B$row", $activity['location']); $row++;
$sheet->setCellValue("A$row", $langArr['date_label'] ?? 'TARIKH:');
$sheet->setCellValue("B$row", $activity['date']); $row++;
$sheet->setCellValue("A$row", $langArr['organizer_label'] ?? 'ANJURAN:');
$sheet->setCellValue("B$row", $activity['organizer']); $row++;
$sheet->setCellValue("A$row", $langArr['level_label'] ?? 'PERINGKAT:');
$sheet->setCellValue("B$row", $activity['level']); $row++;
$sheet->setCellValue("A$row", $langArr['achievement_label'] ?? 'PENCAPAIAN:');
$sheet->setCellValue("B$row", $activity['achievement']); $row++;
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
$sheet->setCellValue("A$row", $langArr['teacher_label'] ?? 'NAMA GURU DIBIMBING:');
$sheet->setCellValue("B$row", $teacherName); $row++;
$row++;
// Peserta统计表
$sheet->setCellValue("A$row", $langArr['participant_label'] ?? 'Peserta :'); $row++;
$sheet->setCellValue("A$row", ($langArr['total_label'] ?? 'JUMLAH BESAR') . ' : ' . array_sum(array_map(function($d){return $d['L']+$d['P'];}, $demographics)) ); $row++;
$sheet->mergeCells("A$row:I$row");
$sheet->setCellValue("A$row", $langArr['total_students_involved'] ?? 'Jumlah Pelajar yang terlibat'); $row++;
$sheet->setCellValue("B$row", $langArr['malay'] ?? 'Melayu');
$sheet->setCellValue("D$row", $langArr['chinese'] ?? 'Cina');
$sheet->setCellValue("F$row", $langArr['indian'] ?? 'India');
$sheet->setCellValue("H$row", $langArr['others'] ?? 'Lain-lain'); $row++;
$sheet->setCellValue("A$row", '');
$sheet->setCellValue("B$row", $langArr['male'] ?? 'L');
$sheet->setCellValue("C$row", $langArr['female'] ?? 'P');
$sheet->setCellValue("D$row", $langArr['male'] ?? 'L');
$sheet->setCellValue("E$row", $langArr['female'] ?? 'P');
$sheet->setCellValue("F$row", $langArr['male'] ?? 'L');
$sheet->setCellValue("G$row", $langArr['female'] ?? 'P');
$sheet->setCellValue("H$row", $langArr['male'] ?? 'L');
$sheet->setCellValue("I$row", $langArr['female'] ?? 'P'); $row++;
$sheet->setCellValue("A$row", '');
$col = 'B';
foreach ($demographics as $d) {
    $sheet->setCellValue($col.$row, $d['L']); $col++;
    $sheet->setCellValue($col.$row, $d['P']); $col++;
}
$row += 2;
// Objectives
$sheet->setCellValue("A$row", $langArr['objective_label'] ?? 'Objektif :'); $row++;
foreach($objectives as $obj) {
    $sheet->setCellValue("A$row", 'a) ' . $obj); $row++;
}
$row++;
// Content
$sheet->setCellValue("A$row", $langArr['content_label'] ?? 'Kandungan (Aktiviti yg dilakukan) :'); $row++;
foreach($content as $c) {
    $sheet->setCellValue("A$row", 'a) ' . $c); $row++;
}
$row++;
// Follow-up
$sheet->setCellValue("A$row", $langArr['followup_label'] ?? 'Penambahbaikan / Tindakan Susulan :'); $row++;
foreach($followup as $f) {
    $sheet->setCellValue("A$row", 'a) ' . $f); $row++;
}
$row++;
// 参与者表格
$sheet->setCellValue("A$row", $langArr['participant_list_label'] ?? 'Senarai Peserta :'); $row++;
$headers = ['#', $langArr['name'] ?? 'Name', $langArr['chinese_name'] ?? 'Chinese Name', $langArr['class'] ?? 'Class', $langArr['student_id'] ?? 'Student ID', $langArr['gender'] ?? 'Gender', $langArr['race'] ?? 'Race', $langArr['religion'] ?? 'Religion', $langArr['achievement'] ?? 'Achievement'];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col.$row, $h); $col++;
}
$row++;
$i=1;
foreach ($participants as $p) {
    $col = 'A';
    $chinese_name = $p['chinese_name'] ?? '';
    if (!$chinese_name && !empty($p['student_id'])) {
        $stmt_cn = $pdo->prepare('SELECT chinese_name FROM students WHERE id = ?');
        $stmt_cn->execute([$p['student_id']]);
        $chinese_name = $stmt_cn->fetchColumn() ?: '';
    }
    $sheet->setCellValue($col++.$row, $i++);
    $sheet->setCellValue($col++.$row, $p['student_name'] ?? $p['name'] ?? $p['username'] ?? '');
    $sheet->setCellValue($col++.$row, $chinese_name);
    $sheet->setCellValue($col++.$row, $p['class'] ?? '');
    $sheet->setCellValue($col++.$row, $p['student_no'] ?? '');
    $sheet->setCellValue($col++.$row, $p['gender'] ?? '');
    $sheet->setCellValue($col++.$row, $p['race'] ?? '');
    $sheet->setCellValue($col++.$row, $p['religion'] ?? '');
    $sheet->setCellValue($col++.$row, $p['achievement'] ?? '');
    $row++;
}
// 美化
$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(20);
$filename = 'activity_' . $id . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
if (ob_get_length()) ob_end_clean();
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 