<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

require 'app/config/db.php';

// Ambil ID mahasiswa dari session
// Pertama, kita perlu mendapatkan 'id' dari tabel 'mahasiswa' berdasarkan 'user_id' dari session
$user_id = $_SESSION['user_id'];
$mhs_result = $conn->query("SELECT id FROM mahasiswa WHERE nim = (SELECT username FROM users WHERE id = $user_id)");
if ($mhs_result->num_rows == 0) {
    die("Data mahasiswa tidak ditemukan. Pastikan Anda login sebagai mahasiswa.");
}
$mahasiswa = $mhs_result->fetch_assoc();
$mahasiswa_id = $mahasiswa['id'];


// Ambil jadwal kuliah yang tersedia untuk tahun akademik aktif
$tahun_akademik_aktif = '2024/2025'; // Ini bisa dibuat dinamis nanti
$query_jadwal_tersedia = "
    SELECT 
        j.id, mk.kode_mk, mk.nama_mk, mk.sks, u.nama_lengkap AS nama_dosen,
        j.hari, j.jam_mulai, j.jam_selesai, j.ruangan
    FROM jadwal_kuliah j
    JOIN mata_kuliah mk ON j.matkul_id = mk.id
    JOIN users u ON j.dosen_id = u.id
    WHERE j.tahun_akademik = '$tahun_akademik_aktif' 
    AND j.id NOT IN (SELECT jadwal_id FROM krs WHERE mahasiswa_id = $mahasiswa_id) -- Hanya tampilkan yg belum diambil
    ORDER BY mk.nama_mk
";
$jadwal_tersedia = $conn->query($query_jadwal_tersedia);


// Ambil KRS yang sudah diambil oleh mahasiswa ini
$query_krs_diambil = "
    SELECT 
        k.id as krs_id, mk.kode_mk, mk.nama_mk, mk.sks, u.nama_lengkap AS nama_dosen,
        j.hari, j.jam_mulai, j.jam_selesai, k.status_approval
    FROM krs k
    JOIN jadwal_kuliah j ON k.jadwal_id = j.id
    JOIN mata_kuliah mk ON j.matkul_id = mk.id
    JOIN users u ON j.dosen_id = u.id
    WHERE k.mahasiswa_id = $mahasiswa_id
    ORDER BY mk.nama_mk
";
$krs_diambil = $conn->query($query_krs_diambil);

$total_sks = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kartu Rencana Studi (KRS)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="akademik.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Portal Akademik</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-edit text-green-500"></i> Kartu Rencana Studi (KRS)</h2>

            <div class="mb-10">
                <h3 class="text-xl font-bold text-gray-700 mb-3">KRS Anda (Semester Ini)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="py-2 px-3 text-left">Kode MK</th>
                                <th class="py-2 px-3 text-left">Mata Kuliah</th>
                                <th class="py-2 px-3 text-center">SKS</th>
                                <th class="py-2 px-3 text-left">Dosen</th>
                                <th class="py-2 px-3 text-center">Status</th>
                                <th class="py-2 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <?php if ($krs_diambil->num_rows > 0): ?>
                                <?php while ($k = $krs_diambil->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3"><?= htmlspecialchars($k['kode_mk']) ?></td>
                                    <td class="py-2 px-3 font-semibold"><?= htmlspecialchars($k['nama_mk']) ?></td>
                                    <td class="py-2 px-3 text-center"><?= htmlspecialchars($k['sks']) ?></td>
                                    <td class="py-2 px-3"><?= htmlspecialchars($k['nama_dosen']) ?></td>
                                    <td class="py-2 px-3 text-center">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $k['status_approval'] == 'Disetujui' ? 'bg-green-100 text-green-800' : '' ?>
                                            <?= $k['status_approval'] == 'Ditolak' ? 'bg-red-100 text-red-800' : '' ?>
                                            <?= $k['status_approval'] == 'Menunggu' ? 'bg-yellow-100 text-yellow-800' : '' ?>">
                                            <?= htmlspecialchars($k['status_approval']) ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <?php if ($k['status_approval'] == 'Menunggu'): ?>
                                        <a class="text-red-500" href='app/controllers/akademik_crud.php?batal_krs=<?= $k['krs_id'] ?>' onclick="return confirm('Yakin batalkan mata kuliah ini?')"><i class="fas fa-trash-alt"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $total_sks += $k['sks']; ?>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4">Anda belum mengambil mata kuliah apapun.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="font-bold">
                            <tr>
                                <td colspan="2" class="text-right py-2 px-3">Total SKS:</td>
                                <td class="text-center py-2 px-3"><?= $total_sks ?></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold text-gray-700 mb-3">Pilih Mata Kuliah yang Tersedia</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                         <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="py-2 px-3 text-left">Mata Kuliah</th>
                                <th class="py-2 px-3 text-left">Jadwal</th>
                                <th class="py-2 px-3 text-center">SKS</th>
                                <th class="py-2 px-3 text-left">Dosen</th>
                                <th class="py-2 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <?php if ($jadwal_tersedia->num_rows > 0): ?>
                                <?php while ($j = $jadwal_tersedia->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-3 font-semibold"><?= htmlspecialchars($j['nama_mk']) ?></td>
                                    <td class="py-2 px-3"><?= htmlspecialchars($j['hari']) ?>, <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                                    <td class="py-2 px-3 text-center"><?= htmlspecialchars($j['sks']) ?></td>
                                    <td class="py-2 px-3"><?= htmlspecialchars($j['nama_dosen']) ?></td>
                                    <td class="py-2 px-3 text-center">
                                        <a class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded" href="app/controllers/akademik_crud.php?ambil_krs=<?= $j['id'] ?>">
                                            <i class="fas fa-plus"></i> Ambil
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4">Tidak ada jadwal mata kuliah yang tersedia untuk diambil.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</body>
</html>