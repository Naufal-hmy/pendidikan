<?php
session_start();
// ... (Lakukan pengecekan login dan role admin seperti di halaman lain) ...
require 'app/config/db.php';
$daftar_matkul = $conn->query("SELECT * FROM mata_kuliah");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen Mata Kuliah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="akademik.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Portal Akademik</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-book"></i> Manajemen Mata Kuliah</h2>
            
            <div class="bg-gray-50 p-6 rounded-lg mb-8">
                <h3 class="text-xl font-bold text-gray-700 mb-4">Tambah Mata Kuliah</h3>
                <form method="post" action="app/controllers/akademik_crud.php" class="space-y-4">
                    <input class="w-full px-4 py-2 border rounded-lg" type="text" name="kode_mk" placeholder="Kode MK (Contoh: IF001)" required>
                    <input class="w-full px-4 py-2 border rounded-lg" type="text" name="nama_mk" placeholder="Nama Mata Kuliah" required>
                    <input class="w-full px-4 py-2 border rounded-lg" type="number" name="sks" placeholder="Jumlah SKS" required>
                    <input class="w-full px-4 py-2 border rounded-lg" type="number" name="semester_target" placeholder="Semester Target" required>
                    <button type="submit" name="simpan_matkul" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Kode MK</th>
                            <th class="py-3 px-4 text-left">Nama Mata Kuliah</th>
                            <th class="py-3 px-4 text-center">SKS</th>
                            <th class="py-3 px-4 text-center">Semester</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php while ($mk = $daftar_matkul->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-3 px-4"><?= htmlspecialchars($mk['kode_mk']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($mk['nama_mk']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($mk['sks']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($mk['semester_target']) ?></td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <a class="text-red-500" href='app/controllers/akademik_crud.php?hapus_matkul=<?= $mk['id'] ?>' onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash-alt"></i></a>
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