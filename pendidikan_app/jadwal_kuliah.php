<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

require 'app/config/db.php';

// Ambil data jadwal yang sudah ada untuk ditampilkan di tabel
$query_jadwal = "
    SELECT 
        mk.kode_mk, mk.nama_mk, mk.sks, u.nama_lengkap AS nama_dosen, 
        j.hari, j.jam_mulai, j.jam_selesai, j.ruangan, j.tahun_akademik
    FROM jadwal_kuliah j
    JOIN mata_kuliah mk ON j.matkul_id = mk.id
    JOIN users u ON j.dosen_id = u.id
    WHERE j.tahun_akademik = '2024/2025' -- Filter untuk tahun akademik aktif (bisa dibuat dinamis)
    ORDER BY j.hari, j.jam_mulai
";
$daftar_jadwal = $conn->query($query_jadwal);

// Kelompokkan jadwal berdasarkan hari
$jadwal_per_hari = [];
while ($row = $daftar_jadwal->fetch_assoc()) {
    $jadwal_per_hari[$row['hari']][] = $row;
}
$hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Jadwal Kuliah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="akademik.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Portal Akademik</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-calendar-alt"></i> Jadwal Kuliah - Tahun Akademik 2024/2025</h2>
            
            <div class="space-y-8">
                <?php foreach ($hari_list as $hari): ?>
                    <?php if (isset($jadwal_per_hari[$hari])): ?>
                    <div>
                        <h3 class="text-xl font-bold text-gray-700 mb-3 border-b-2 border-blue-500 pb-2"><?= $hari ?></h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200 text-gray-600">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Jam</th>
                                        <th class="py-2 px-3 text-left">Kode MK</th>
                                        <th class="py-2 px-3 text-left">Mata Kuliah</th>
                                        <th class="py-2 px-3 text-left">SKS</th>
                                        <th class="py-2 px-3 text-left">Dosen</th>
                                        <th class="py-2 px-3 text-left">Ruang</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    <?php foreach ($jadwal_per_hari[$hari] as $j): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-3"><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                                        <td class="py-2 px-3"><?= htmlspecialchars($j['kode_mk']) ?></td>
                                        <td class="py-2 px-3 font-semibold"><?= htmlspecialchars($j['nama_mk']) ?></td>
                                        <td class="py-2 px-3"><?= htmlspecialchars($j['sks']) ?></td>
                                        <td class="py-2 px-3"><?= htmlspecialchars($j['nama_dosen']) ?></td>
                                        <td class="py-2 px-3"><?= htmlspecialchars($j['ruangan']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</body>
</html>