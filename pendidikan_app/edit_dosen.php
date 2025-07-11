<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

require 'app/config/db.php';
$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id = $id AND role = 'dosen'");
$dosen = $result->fetch_assoc();

if (!$dosen) {
    die("Data dosen tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 max-w-2xl">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="manajemen_dosen.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Manajemen Dosen</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-user-edit"></i> Edit Data Dosen</h2>
            
            <form method="post" action="app/controllers/user_crud.php" class="space-y-4">
                <input type="hidden" name="id" value="<?= $dosen['id'] ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input class="w-full mt-1 px-4 py-2 border rounded-lg" type="text" name="nama_lengkap" value="<?= htmlspecialchars($dosen['nama_lengkap']) ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input class="w-full mt-1 px-4 py-2 border rounded-lg" type="text" name="username" value="<?= htmlspecialchars($dosen['username']) ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                    <input class="w-full mt-1 px-4 py-2 border rounded-lg" type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah">
                </div>
                <button type="submit" name="update_dosen" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-sync-alt"></i> Perbarui Data
                </button>
            </form>
        </div>
    </div>
</body>
</html>
