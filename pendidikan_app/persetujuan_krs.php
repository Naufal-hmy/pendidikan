<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

require 'app/config/db.php';

// Ambil daftar mahasiswa yang sudah mengisi KRS dan masih ada yang menunggu persetujuan
$query_mahasiswa_krs = "
    SELECT DISTINCT m.id, m.nama, m.nim
    FROM mahasiswa m
    JOIN krs k ON m.id = k.mahasiswa_id
    WHERE k.status_approval = 'Menunggu'
    ORDER BY m.nama ASC
";
$mahasiswa_list = $conn->query($query_mahasiswa_krs);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Persetujuan KRS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="akademik.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Portal Akademik</a>
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-user-check"></i> Persetujuan KRS Mahasiswa</h2>
            
            <p class="text-gray-600 mb-6">Berikut adalah daftar mahasiswa yang KRS-nya memerlukan persetujuan. Klik pada nama mahasiswa untuk melihat detail.</p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-2 px-3 text-left">NIM</th>
                            <th class="py-2 px-3 text-left">Nama Mahasiswa</th>
                            <th class="py-2 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        <?php if ($mahasiswa_list->num_rows > 0): ?>
                            <?php while ($mhs = $mahasiswa_list->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="py-2 px-3"><?= htmlspecialchars($mhs['nim']) ?></td>
                                <td class="py-2 px-3 font-semibold"><?= htmlspecialchars($mhs['nama']) ?></td>
                                <td class="py-2 px-3 text-center">
                                    <a href="detail_persetujuan_krs.php?mhs_id=<?= $mhs['id'] ?>" class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded">
                                        Lihat Detail KRS
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center py-4">Tidak ada KRS yang memerlukan persetujuan saat ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>