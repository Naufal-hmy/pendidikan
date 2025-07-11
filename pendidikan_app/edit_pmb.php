<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// PENGECEKAN HAK AKSES - HANYA ADMIN
if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Anda bukan admin."); // die() lebih cocok di sini
}

require 'app/config/db.php';
// ... sisa kode edit_mahasiswa.php tidak berubah ...
require 'app/config/db.php';

// Ambil ID dari URL
$id = $_GET['id'];

// Ambil data pendaftar yang akan diedit
$result = $conn->query("SELECT * FROM pmb WHERE id = $id");
$pmb = $result->fetch_assoc();

if (!$pmb) {
    echo "Data pendaftar tidak ditemukan!";
    exit;
}

// Daftar jurusan untuk dropdown
$jurusan_list = [
    "Teknik Informatika", 
    "Sistem Informasi", 
    "Manajemen", 
    "Akuntansi", 
    "Desain Komunikasi Visual"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Pendaftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 max-w-2xl">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="pmb.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke PMB</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-user-edit text-blue-500"></i> Edit Data Pendaftar</h2>
            
            <form method="post" action="app/controllers/pmb_crud.php" class="space-y-4">
                <input type="hidden" name="id" value="<?= $pmb['id'] ?>">

                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input id="nama" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="nama" value="<?= htmlspecialchars($pmb['nama']) ?>" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="email" name="email" value="<?= htmlspecialchars($pmb['email']) ?>" required>
                </div>
                 <div>
                    <label for="asal_sekolah" class="block text-sm font-medium text-gray-700">Asal Sekolah</label>
                    <input id="asal_sekolah" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="asal_sekolah" value="<?= htmlspecialchars($pmb['asal_sekolah']) ?>" required>
                </div>
                 <div>
                    <label for="jurusan" class="block text-sm font-medium text-gray-700">Pilihan Jurusan</label>
                    <select id="jurusan" name="jurusan" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="" disabled>-- Pilih Jurusan --</option>
                        <?php foreach($jurusan_list as $jur): ?>
                            <option value="<?= $jur ?>" <?= ($pmb['jurusan'] == $jur) ? 'selected' : '' ?>>
                                <?= $jur ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" name="update_pmb" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    <i class="fas fa-sync-alt"></i> Perbarui Data Pendaftar
                </button>
            </form>
        </div>
    </div>
</body>
</html>