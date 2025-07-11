<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

require 'app/config/db.php';
$mahasiswa_id = (int)$_GET['mhs_id'];

// Ambil info mahasiswa
$info_mhs = $conn->query("SELECT nama, nim FROM mahasiswa WHERE id=$mahasiswa_id")->fetch_assoc();

// Ambil KRS yang sudah diambil oleh mahasiswa ini
$query_krs = "
    SELECT 
        k.id as krs_id, mk.kode_mk, mk.nama_mk, mk.sks, k.status_approval
    FROM krs k
    JOIN jadwal_kuliah j ON k.jadwal_id = j.id
    JOIN mata_kuliah mk ON j.matkul_id = mk.id
    WHERE k.mahasiswa_id = $mahasiswa_id
    ORDER BY mk.nama_mk
";
$krs_diambil = $conn->query($query_krs);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Persetujuan KRS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="persetujuan_krs.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Mahasiswa</a>
            <h2 class="text-2xl font-bold text-gray-800">Detail KRS: <?= htmlspecialchars($info_mhs['nama']) ?></h2>
            <p class="text-gray-600 mb-4">NIM: <?= htmlspecialchars($info_mhs['nim']) ?></p>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-2 px-3 text-left">Kode MK</th>
                            <th class="py-2 px-3 text-left">Mata Kuliah</th>
                            <th class="py-2 px-3 text-center">SKS</th>
                            <th class="py-2 px-3 text-center">Status</th>
                            <th class="py-2 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        <?php while ($k = $krs_diambil->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-2 px-3"><?= htmlspecialchars($k['kode_mk']) ?></td>
                            <td class="py-2 px-3 font-semibold"><?= htmlspecialchars($k['nama_mk']) ?></td>
                            <td class="py-2 px-3 text-center"><?= htmlspecialchars($k['sks']) ?></td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $k['status_approval'] == 'Disetujui' ? 'bg-green-100 text-green-800' : '' ?>
                                    <?= $k['status_approval'] == 'Ditolak' ? 'bg-red-100 text-red-800' : '' ?>
                                    <?= $k['status_approval'] == 'Menunggu' ? 'bg-yellow-100 text-yellow-800' : '' ?>">
                                    <?= htmlspecialchars($k['status_approval']) ?>
                                </span>
                            </td>
                            <td class="py-2 px-3 text-center space-x-2">
                                <?php if ($k['status_approval'] == 'Menunggu'): ?>
                                <a href="app/controllers/akademik_crud.php?setujui_krs=<?= $k['krs_id'] ?>&mhs_id=<?= $mahasiswa_id ?>" class="text-green-500"><i class="fas fa-check"></i> Setujui</a>
                                <a href="app/controllers/akademik_crud.php?tolak_krs=<?= $k['krs_id'] ?>&mhs_id=<?= $mahasiswa_id ?>" class="text-red-500"><i class="fas fa-times"></i> Tolak</a>
                                <?php else: echo '-'; endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>