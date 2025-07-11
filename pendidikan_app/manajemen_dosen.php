<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak! Anda bukan admin.");
}

require 'app/config/db.php';

// Ambil semua data pengguna dengan peran 'dosen'
$daftar_dosen = $conn->query("SELECT * FROM users WHERE role = 'dosen' ORDER BY nama_lengkap ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="index.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-chalkboard-teacher"></i> Manajemen Dosen</h2>
            
            <!-- Form Tambah Dosen -->
            <div class="bg-gray-50 p-6 rounded-lg mb-8">
                <h3 class="text-xl font-bold text-gray-700 mb-4"><i class="fas fa-user-plus"></i> Tambah Dosen Baru</h3>
                <form method="post" action="app/controllers/user_crud.php" class="space-y-4">
                    <input class="w-full px-4 py-2 border rounded-lg" type="text" name="nama_lengkap" placeholder="Nama Lengkap Dosen (dengan gelar)" required>
                    <input class="w-full px-4 py-2 border rounded-lg" type="text" name="username" placeholder="Username (NIDN atau NIP)" required>
                    <input class="w-full px-4 py-2 border rounded-lg" type="password" name="password" placeholder="Password Awal" required>
                    <button type="submit" name="simpan_dosen" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save"></i> Simpan Dosen
                    </button>
                </form>
            </div>
            
            <!-- Tabel Daftar Dosen -->
            <h3 class="text-xl font-bold text-gray-700 mb-4">Daftar Dosen Aktif</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-2 px-3 text-left">Nama Lengkap</th>
                            <th class="py-2 px-3 text-left">Username</th>
                            <th class="py-2 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        <?php while ($dosen = $daftar_dosen->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-2 px-3 font-semibold"><?= htmlspecialchars($dosen['nama_lengkap']) ?></td>
                            <td class="py-2 px-3"><?= htmlspecialchars($dosen['username']) ?></td>
                            <td class="py-2 px-3 text-center space-x-4">
                                <a class="text-yellow-500 hover:text-yellow-700" href='edit_dosen.php?id=<?= $dosen['id'] ?>' title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a class="text-red-500 hover:text-red-700" href='app/controllers/user_crud.php?hapus_dosen=<?= $dosen['id'] ?>' onclick="return confirm('Yakin hapus data dosen ini?')" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
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
