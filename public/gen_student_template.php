<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray([
    ['学号','姓名','班级','性别','民族','宗教','邮箱']
], NULL, 'A1');

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/student_import_template.xlsx');
echo "模板已生成: public/student_import_template.xlsx\n"; 