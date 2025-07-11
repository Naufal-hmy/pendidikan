<?php
require '../../vendor/autoload.php';
require '../config/db.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Nama');
$sheet->setCellValue('B1', 'NIM');
$sheet->setCellValue('C1', 'Jurusan');
$sheet->setCellValue('D1', 'Tahun Masuk');

$query = $conn->query("SELECT * FROM mahasiswa");
$row = 2;
while ($m = $query->fetch_assoc()) {
    $sheet->setCellValue("A$row", $m['nama']);
    $sheet->setCellValue("B$row", $m['nim']);
    $sheet->setCellValue("C$row", $m['jurusan']);
    $sheet->setCellValue("D$row", $m['tahun_masuk']);
    $row++;
}

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="mahasiswa.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
?>