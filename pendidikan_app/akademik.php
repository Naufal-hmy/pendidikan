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
    <title>Portal Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 max-w-4xl">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="index.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            <h1 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-university text-blue-500"></i> Portal Akademik</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="jadwal_kuliah.php" class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-lg shadow transition">
                    <i class="fas fa-calendar-alt fa-2x text-blue-500"></i>
                    <h3 class="font-bold text-lg text-blue-800 mt-2">Jadwal Kuliah</h3>
                    <p class="text-gray-600 text-sm mt-1">Lihat jadwal perkuliahan semester aktif.</p>
                </a>
                
                <a href="krs.php" class="block p-6 bg-green-50 hover:bg-green-100 rounded-lg shadow transition">
                    <i class="fas fa-edit fa-2x text-green-500"></i>
                    <h3 class="font-bold text-lg text-green-800 mt-2">Kartu Rencana Studi (KRS)</h3>
                    <p class="text-gray-600 text-sm mt-1">Isi dan lihat rencana studi semester Anda.</p>
                </a>

                <a href="khs.php" class="block p-6 bg-yellow-50 hover:bg-yellow-100 rounded-lg shadow transition">
                    <i class="fas fa-poll-result fa-2x text-yellow-500"></i>
                    <h3 class="font-bold text-lg text-yellow-800 mt-2">Kartu Hasil Studi (KHS)</h3>
                    <p class="text-gray-600 text-sm mt-1">Lihat rekapitulasi dan transkrip nilai.</p>
                </a>


                <?php if ($_SESSION['role'] === 'admin'): // Menu ini khusus untuk admin ?>
                    <a href="manajemen_matkul.php" class="block p-6 bg-purple-50 hover:bg-purple-100 rounded-lg shadow transition">
                        <i class="fas fa-book fa-2x text-purple-500"></i>
                        <h3 class="font-bold text-lg text-purple-800 mt-2">Manajemen Mata Kuliah</h3>
                        <p class="text-gray-600 text-sm mt-1">Kelola data master mata kuliah.</p>
                    </a>
                    
                    <a href="manajemen_jadwal.php" class="block p-6 bg-teal-50 hover:bg-teal-100 rounded-lg shadow transition">
                        <i class="fas fa-calendar-plus fa-2x text-teal-500"></i>
                        <h3 class="font-bold text-lg text-teal-800 mt-2">Manajemen Jadwal Kuliah</h3>
                        <p class="text-gray-600 text-sm mt-1">Atur jadwal kelas per semester.</p>
                    </a>

                    <a href="persetujuan_krs.php" class="block p-6 bg-orange-50 hover:bg-orange-100 rounded-lg shadow transition">
                        <i class="fas fa-user-check fa-2x text-orange-500"></i>
                        <h3 class="font-bold text-lg text-orange-800 mt-2">Persetujuan KRS</h3>
                        <p class="text-gray-600 text-sm mt-1">Setujui atau tolak KRS mahasiswa.</p>
                    </a>

                    <a href="input_nilai.php" class="block p-6 bg-red-50 hover:bg-red-100 rounded-lg shadow transition">
                        <i class="fas fa-marker fa-2x text-red-500"></i>
                        <h3 class="font-bold text-lg text-red-800 mt-2">Input Nilai KHS</h3>
                        <p class="text-gray-600 text-sm mt-1">Input nilai akhir mahasiswa.</p>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>