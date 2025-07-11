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

// Ambil data mahasiswa yang akan diedit
$result = $conn->query("SELECT * FROM mahasiswa WHERE id = $id");
$mhs = $result->fetch_assoc();

if (!$mhs) {
    echo "Data tidak ditemukan!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="mahasiswa.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Data Mahasiswa</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-pencil-alt"></i> Edit Data Mahasiswa</h2>
            
            <form method="post" action="app/controllers/mahasiswa_crud.php" class="space-y-4">
                <input type="hidden" name="id" value="<?= $mhs['id'] ?>">

                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input id="nama" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="nama" value="<?= htmlspecialchars($mhs['nama']) ?>" required>
                </div>
                <div>
                    <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                    <input id="nim" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="nim" value="<?= htmlspecialchars($mhs['nim']) ?>" required>
                </div>
                 <div>
                    <label for="jurusan" class="block text-sm font-medium text-gray-700">Jurusan</label>
                    <input id="jurusan" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="jurusan" value="<?= htmlspecialchars($mhs['jurusan']) ?>" required>
                </div>
                 <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun Masuk</label>
                    <input id="tahun" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="number" name="tahun" value="<?= htmlspecialchars($mhs['tahun_masuk']) ?>" required>
                </div>
                
                <button type="submit" name="update" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    <i class="fas fa-sync-alt"></i> Perbarui Data
                </button>
            </form>
        </div>
    </div>
</body>
</html>