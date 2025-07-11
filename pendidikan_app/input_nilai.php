<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

require 'app/config/db.php';

// Ambil semua entri KRS yang disetujui dan belum ada nilainya
$query = "
    SELECT 
        k.id AS krs_id, 
        m.nim, 
        m.nama AS nama_mahasiswa, 
        mk.nama_mk
    FROM krs k
    JOIN mahasiswa m ON k.mahasiswa_id = m.id
    JOIN jadwal_kuliah j ON k.jadwal_id = j.id
    JOIN mata_kuliah mk ON j.matkul_id = mk.id
    LEFT JOIN khs kh ON k.id = kh.krs_id
    WHERE k.status_approval = 'Disetujui' AND kh.id IS NULL
    ORDER BY m.nama, mk.nama_mk
";
$list_krs = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Nilai KHS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="akademik.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Portal Akademik</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-marker"></i> Input Nilai KHS</h2>

            <form action="app/controllers/akademik_crud.php" method="POST">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="py-2 px-3 text-left">Mahasiswa</th>
                                <th class="py-2 px-3 text-left">Mata Kuliah</th>
                                <th class="py-2 px-3 text-left">Input Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <?php if ($list_krs->num_rows > 0): ?>
                                <?php while ($k = $list_krs->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="py-2 px-3">
                                        <div class="font-semibold"><?= htmlspecialchars($k['nama_mahasiswa']) ?></div>
                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($k['nim']) ?></div>
                                    </td>
                                    <td class="py-2 px-3"><?= htmlspecialchars($k['nama_mk']) ?></td>
                                    <td class="py-2 px-3">
                                        <input type="hidden" name="krs_id[]" value="<?= $k['krs_id'] ?>">
                                        <select name="nilai_huruf[]" class="w-full px-2 py-1 border rounded-lg">
                                            <option value="">--Pilih--</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-4">Tidak ada data untuk diinput nilai.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($list_krs->num_rows > 0): ?>
                <div class="mt-6 text-right">
                    <button type="submit" name="simpan_nilai" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save"></i> Simpan Semua Nilai
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>