<?php
// Memanggil pustaka dari Composer dan koneksi database
require '../../vendor/autoload.php';
require '../config/db.php';

// Menggunakan namespace dari library yang dibutuhkan
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

// Memeriksa format yang diminta dari URL (excel atau pdf)
$format = isset($_GET['format']) ? $_GET['format'] : '';

// Mengambil semua data dari tabel pmb
$query = $conn->query("SELECT * FROM pmb ORDER BY nama ASC");

// Jika format yang diminta adalah EXCEL
if ($format == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Menulis header kolom
    $sheet->setCellValue('A1', 'Nama Lengkap');
    $sheet->setCellValue('B1', 'Email');
    $sheet->setCellValue('C1', 'Asal Sekolah');
    $sheet->setCellValue('D1', 'Pilihan Jurusan');
    $sheet->setCellValue('E1', 'Status Pendaftaran');

    // Menulis data dari database ke setiap baris
    $rowNumber = 2;
    while ($data = $query->fetch_assoc()) {
        $sheet->setCellValue("A$rowNumber", $data['nama']);
        $sheet->setCellValue("B$rowNumber", $data['email']);
        $sheet->setCellValue("C$rowNumber", $data['asal_sekolah']);
        $sheet->setCellValue("D$rowNumber", $data['jurusan']);
        $sheet->setCellValue("E$rowNumber", $data['status']);
        $rowNumber++;
    }

    // Mengatur header HTTP untuk mengunduh file Excel
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="laporan_pendaftar.xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');

// Jika format yang diminta adalah PDF
} elseif ($format == 'pdf') {
    $dompdf = new Dompdf();
    
    // Membuat struktur HTML untuk konten PDF
    $html = '
        <style>
            table { width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 12px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
        <h1>Laporan Data Pendaftar Mahasiswa Baru</h1>
        <table>
            <tr>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Asal Sekolah</th>
                <th>Pilihan Jurusan</th>
                <th>Status</th>
            </tr>';
    
    // Menambahkan data dari database ke dalam tabel HTML
    while ($data = $query->fetch_assoc()) {
        $html .= "<tr>
            <td>" . htmlspecialchars($data['nama']) . "</td>
            <td>" . htmlspecialchars($data['email']) . "</td>
            <td>" . htmlspecialchars($data['asal_sekolah']) . "</td>
            <td>" . htmlspecialchars($data['jurusan']) . "</td>
            <td>" . htmlspecialchars($data['status']) . "</td>
        </tr>";
    }
    $html .= '</table>';
    
    // Memuat HTML ke Dompdf, mengatur kertas, dan merender PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape'); // Mengatur kertas menjadi A4 landscape
    $dompdf->render();
    
    // Mengirimkan file PDF ke browser untuk ditampilkan atau diunduh
    $dompdf->stream("laporan_pendaftar.pdf", ["Attachment" => 0]); // Attachment 0 = tampilkan, 1 = langsung unduh
} else {
    echo "Format tidak valid.";
}
?>