<?php
require '../../vendor/autoload.php';
require '../config/db.php';
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$html = '<h1>Data Mahasiswa</h1><table border="1" cellpadding="5"><tr><th>Nama</th><th>NIM</th></tr>';
$query = $conn->query("SELECT * FROM mahasiswa");
while ($row = $query->fetch_assoc()) {
    $html .= "<tr><td>{$row['nama']}</td><td>{$row['nim']}</td></tr>";
}
$html .= '</table>';
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream("laporan_mahasiswa.pdf");
?>