<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
require 'app/config/db.php';
require 'app/controllers/akademik_crud.php'; // Kita butuh fungsi get_mahasiswa_id

$mahasiswa_id = get_mahasiswa_id($conn);
if (!$mahasiswa_id) {
    die("Data mahasiswa tidak ditemukan.");
}

// Ambil KHS mahasiswa yang login
$query = "
    SELECT mk.kode_mk, mk.nama_mk, mk.sks, kh.nilai_huruf, kh.nilai_angka
    FROM khs kh
    JOIN krs k ON kh.krs_id = k.id
    JOIN jadwal_kuliah j ON k.jadwal_id = j.id
    JOIN mata_kuliah mk ON j.matkul_id = mk.id
    WHERE k.mahasiswa_id = $mahasiswa_id
    ORDER BY mk.nama_mk
";
$khs_data = $conn->query($query);

// Hitung IPK
$total_sks = 0;
$total_bobot = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kartu Hasil Studi (KHS)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="akademik.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Portal Akademik</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-poll-result text-yellow-500"></i> Kartu Hasil Studi (KHS)</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-2 px-3 text-left">Kode MK</th>
                            <th class="py-2 px-3 text-left">Mata Kuliah</th>
                            <th class="py-2 px-3 text-center">SKS</th>
                            <th class="py-2 px-3 text-center">Nilai</th>
                            <th class="py-2 px-3 text-center">Bobot</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        <?php if ($khs_data->num_rows > 0): ?>
                            <?php while ($khs = $khs_data->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="py-2 px-3"><?= htmlspecialchars($khs['kode_mk']) ?></td>
                                <td class="py-2 px-3 font-semibold"><?= htmlspecialchars($khs['nama_mk']) ?></td>
                                <td class="py-2 px-3 text-center"><?= htmlspecialchars($khs['sks']) ?></td>
                                <td class="py-2 px-3 text-center"><?= htmlspecialchars($khs['nilai_huruf']) ?></td>
                                <td class="py-2 px-3 text-center"><?= $bobot = (float)$khs['nilai_angka'] * (int)$khs['sks'] ?></td>
                            </tr>
                            <?php 
                                $total_sks += (int)$khs['sks'];
                                $total_bobot += $bobot;
                            ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                             <tr><td colspan="5" class="text-center py-4">Belum ada nilai yang tersedia.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="font-bold bg-gray-100">
                        <tr>
                            <td colspan="2" class="text-right py-3 px-3">Total</td>
                            <td class="text-center py-3 px-3"><?= $total_sks ?></td>
                            <td></td>
                            <td class="text-center py-3 px-3"><?= $total_bobot ?></td>
                        </tr>
                         <tr>
                            <td colspan="4" class="text-right py-3 px-3">Indeks Prestasi Kumulatif (IPK)</td>
                            <td class="text-center py-3 px-3 text-blue-600">
                                <?= ($total_sks > 0) ? number_format($total_bobot / $total_sks, 2) : '0.00' ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</body>
</html>