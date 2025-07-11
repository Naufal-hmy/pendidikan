<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Aplikasi Pendidikan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 max-w-4xl">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div>
                <img src="kampusundira.jpg" alt="Banner Kampus" class="w-full h-48 object-cover">
            </div>

            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2"><i class="fas fa-school text-blue-500"></i> Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>!</h2>
                <p class="text-gray-600">Anda login sebagai: <strong class="font-medium text-blue-600"><?= htmlspecialchars($_SESSION['role']) ?></strong></p>
                <hr class="my-6">
                <h3 class="text-2xl font-bold text-gray-700 mb-4"><i class="fas fa-bars"></i> Menu Navigasi</h3>
                <nav class="space-y-2">
                    <a href="index.php" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-200 hover:bg-gray-300 group hover:shadow transition duration-300">
                        <i class="fas fa-home w-6 h-6 text-blue-500"></i><span class="ml-3">Beranda</span>
                    </a>
                    <a href="profil.php" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg hover:bg-gray-100 group hover:shadow transition duration-300">
                        <i class="fas fa-user w-6 h-6 text-gray-500 group-hover:text-blue-500"></i><span class="ml-3">Profil Saya</span>
                    </a>
                    <a href="akademik.php" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg hover:bg-gray-100 group hover:shadow transition duration-300">
                        <i class="fas fa-book-open w-6 h-6 text-gray-500 group-hover:text-blue-500"></i><span class="ml-3">Akademik</span>
                    </a>

                    <?php if ($_SESSION['role'] === 'admin'): // Tampilkan menu ini HANYA untuk ADMIN ?>
                    <a href="mahasiswa.php" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg hover:bg-gray-100 group hover:shadow transition duration-300">
                        <i class="fas fa-users w-6 h-6 text-gray-500 group-hover:text-blue-500"></i><span class="ml-3">Data Mahasiswa</span>
                    </a>
                    <a href="pmb.php" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg hover:bg-gray-100 group hover:shadow transition duration-300">
                        <i class="fas fa-user-plus w-6 h-6 text-gray-500 group-hover:text-blue-500"></i><span class="ml-3">Penerimaan Mahasiswa Baru</span>
                    </a>
                    <?php endif; ?>

                    <a href="logout.php" class="flex items-center p-3 text-base font-bold text-red-600 rounded-lg hover:bg-red-100 group hover:shadow transition duration-300">
                        <i class="fas fa-sign-out-alt w-6 h-6 text-red-500"></i><span class="ml-3">Logout</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</body>
</html>