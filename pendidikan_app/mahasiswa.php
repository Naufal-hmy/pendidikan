<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// PENGECEKAN HAK AKSES - HANYA ADMIN
if ($_SESSION['role'] !== 'admin') {
    echo "<h1>Akses Ditolak!</h1><p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>";
    echo "<a href='index.php'>Kembali ke Beranda</a>";
    exit;
}

require 'app/config/db.php';
$data = $conn->query("SELECT * FROM mahasiswa ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="index.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-users"></i> Data Mahasiswa</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">NIM</th>
                            <th class="py-3 px-4 text-left">Jurusan</th>
                            <th class="py-3 px-4 text-left">Tahun</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php while ($mhs = $data->fetch_assoc()): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-4"><?= htmlspecialchars($mhs['nama']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($mhs['nim']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($mhs['jurusan']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($mhs['tahun_masuk']) ?></td>
                            <td class="py-3 px-4 text-center space-x-4">
                                <a class="text-yellow-500 hover:text-yellow-700" href='edit_mahasiswa.php?id=<?= $mhs['id'] ?>'>
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </a>
                                <a class="text-red-500 hover:text-red-700" href='app/controllers/mahasiswa_crud.php?delete=<?= $mhs['id'] ?>' onclick="return confirm('Yakin hapus data ini?')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex space-x-2">
                <a href='app/reports/export_excel.php' class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    <i class="fas fa-file-excel"></i> Export ke Excel
                </a>
                <a href='app/reports/laporan_mahasiswa.php' class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    <i class="fas fa-file-pdf"></i> Export ke PDF
                </a>
            </div>
            
            <hr class="my-8">

            <h3 class="text-xl font-bold text-gray-700 mb-4"><i class="fas fa-user-plus"></i> Tambah Mahasiswa</h3>
            <form method="post" action="app/controllers/mahasiswa_crud.php" class="space-y-4">
                <input class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="nama" placeholder="Nama" required>
                <input class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="nim" placeholder="NIM" required>
                <input class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="jurusan" placeholder="Jurusan" required>
                <input class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="number" name="tahun" placeholder="Tahun Masuk" required>
                <button type="submit" name="simpan" class="