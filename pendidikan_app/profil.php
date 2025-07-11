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
    <title>Profil Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 max-w-2xl">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="index.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            <h1 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-user-circle text-blue-500"></i> Profil Saya</h1>
            <div class="space-y-4">
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-user w-6 h-6 text-gray-500"></i>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Username</p>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['username']) ?></p>
                    </div>
                </div>
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-id-card w-6 h-6 text-gray-500"></i>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Nama Lengkap</p>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
                    </div>
                </div>
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-shield-alt w-6 h-6 text-gray-500"></i>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Role / Peran</p>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['role']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>