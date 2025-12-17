<?php
session_start();
while (ob_get_level() > 0) {
    ob_end_clean();
}
ini_set('display_errors', 0);
error_reporting(0);
require_once '../vendor/autoload.php';
require_once '../src/models/User.php';
require_once '../src/models/Activity.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 获取过滤参数
$selectedLevel = $_GET['level'] ?? '';

// 获取活动数据
$activity = new Activity();
$activities = $activity->getAllActivitiesForExport($selectedLevel);

// 创建新的 Spreadsheet 对象
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 设置标题行
$headers = [
    'A1' => '活动名称',
    'B1' => '创建者',
    'C1' => 'Level',
    'D1' => '状态',
    'E1' => '创建时间',
    'F1' => '活动日期',
    'G1' => '地点',
    'H1' => '活动类型',
    'I1' => '组织者',
    'J1' => '成就'
];
foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

// 填充数据
$row = 2;
foreach ($activities as $activity) {
    $activityDate = strtotime($activity['date']);
    $currentDate = time();
    if ($activityDate > $currentDate) {
        $status = '即将开始';
    } elseif ($activityDate < $currentDate) {
        $status = '已结束';
    } else {
        $status = '进行中';
    }

    $sheet->setCellValue('A' . $row, $activity['title']);
    $sheet->setCellValue('B' . $row, $activity['creator_name']);
    $sheet->setCellValue('C' . $row, $activity['level'] ?? 'N/A');
    $sheet->setCellValue('D' . $row, $status);
    $sheet->setCellValue('E' . $row, date('Y-m-d H:i', strtotime($activity['created_at'])));
    $sheet->setCellValue('F' . $row, $activity['date']);
    $sheet->setCellValue('G' . $row, $activity['location']);
    $sheet->setCellValue('H' . $row, $activity['event_type']);
    $sheet->setCellValue('I' . $row, $activity['organizer']);
    $sheet->setCellValue('J' . $row, $activity['achievement']);
    $row++;
}

// 自动调整列宽
foreach (range('A', 'J') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// 设置文件名
$filename = 'activities_export_' . date('Y-m-d_H-i-s');
if (!empty($selectedLevel)) {
    $filename .= '_' . str_replace('/', '_', $selectedLevel);
}
$filename .= '.xlsx';

// 设置响应头
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// 输出 Excel 文件
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('php://output');
exit; 