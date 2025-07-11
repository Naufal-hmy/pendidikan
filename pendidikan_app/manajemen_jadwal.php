<?php
session_start();
// ... (Lakukan pengecekan login dan role admin) ...
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

require 'app/config/db.php';

// Ambil data untuk form
$daftar_matkul = $conn->query("SELECT * FROM mata_kuliah ORDER BY nama_mk ASC");
$daftar_dosen = $conn->query("SELECT id, nama_lengkap FROM users WHERE role = 'dosen' ORDER BY nama_lengkap ASC");

// Ambil data jadwal yang sudah ada untuk ditampilkan di tabel
$query_jadwal = "
    SELECT 
        j.id, mk.kode_mk, mk.nama_mk, u.nama_lengkap AS nama_dosen, 
        j.hari, j.jam_mulai, j.jam_selesai, j.ruangan, j.tahun_akademik
    FROM jadwal_kuliah j
    JOIN mata_kuliah mk ON j.matkul_id = mk.id
    JOIN users u ON j.dosen_id = u.id
    ORDER BY j.id DESC
";
$daftar_jadwal = $conn->query($query_jadwal);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen Jadwal Kuliah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="akademik.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Portal Akademik</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-calendar-plus"></i> Manajemen Jadwal Kuliah</h2>
            
            <div class="bg-gray-50 p-6 rounded-lg mb-8">
                <h3 class="text-xl font-bold text-gray-700 mb-4">Buat Jadwal Baru</h3>
                <form method="post" action="app/controllers/akademik_crud.php" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <select name="matkul_id" class="w-full px-4 py-2 border rounded-lg" required>
                            <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                            <?php while($mk = $daftar_matkul->fetch_assoc()): ?>
                                <option value="<?= $mk['id'] ?>"><?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select name="dosen_id" class="w-full px-4 py-2 border rounded-lg" required>
                            <option value="" disabled selected>-- Pilih Dosen --</option>
                            <?php while($dosen = $daftar_dosen->fetch_assoc()): ?>
                                <option value="<?= $dosen['id'] ?>"><?= htmlspecialchars($dosen['nama_lengkap']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select name="hari" class="w-full px-4 py-2 border rounded-lg" required>
                            <option value="" disabled selected>-- Pilih Hari --</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                        <input class="w-full px-4 py-2 border rounded-lg" type="text" name="ruangan" placeholder="Ruangan (Contoh: R.301)" required>
                    </div>
                    <div class="space-y-4">
                         <input class="w-full px-4 py-2 border rounded-lg" type="text" name="tahun_akademik" placeholder="Tahun Akademik (Contoh: 2024/2025)" required>
                        <div>
                            <label class="text-sm">Jam Mulai</label>
                            <input class="w-full px-4 py-2 border rounded-lg" type="time" name="jam_mulai" required>
                        </div>
                        <div>
                            <label class="text-sm">Jam Selesai</label>
                            <input class="w-full px-4 py-2 border rounded-lg" type="time" name="jam_selesai" required>
                        </div>
                        <button type="submit" name="simpan_jadwal" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-save"></i> Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
            
            <h3 class="text-xl font-bold text-gray-700 mb-4">Daftar Jadwal Aktif</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-2 px-3 text-left">Mata Kuliah</th>
                            <th class="py-2 px-3 text-left">Dosen</th>
                            <th class="py-2 px-3 text-left">Jadwal</th>
                            <th class="py-2 px-3 text-left">Ruang</th>
                            <th class="py-2 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        <?php while ($j = $daftar_jadwal->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-2 px-3"><?= htmlspecialchars($j['nama_mk']) ?> <span class="text-xs text-gray-500">(<?= htmlspecialchars($j['kode_mk']) ?>)</span></td>
                            <td class="py-2 px-3"><?= htmlspecialchars($j['nama_dosen']) ?></td>
                            <td class="py-2 px-3"><?= htmlspecialchars($j['hari']) ?>, <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                            <td class="py-2 px-3"><?= htmlspecialchars($j['ruangan']) ?></td>
                            <td class="py-2 px-3 text-center">
                                <a class="text-red-500" href='app/controllers/akademik_crud.php?hapus_jadwal=<?= $j['id'] ?>' onclick="return confirm('Yakin hapus jadwal ini?')"><i class="fas fa-trash-alt"></i></a>
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