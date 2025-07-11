<?php
session_start();
// ... (kode session check dan koneksi db) ...
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    echo "<h1>Akses Ditolak!</h1><p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>";
    echo "<a href='index.php'>Kembali ke Beranda</a>";
    exit;
}
require 'app/config/db.php';
$data_pmb = $conn->query("SELECT * FROM pmb ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Mahasiswa Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <a href="index.php" class="text-blue-500 hover:underline mb-6 inline-block"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            <h1 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-user-plus"></i> Penerimaan Mahasiswa Baru (PMB)</h1>

            <div class="bg-gray-50 p-6 rounded-lg mb-8">
                <h3 class="text-xl font-bold text-gray-700 mb-4"><i class="fas fa-edit"></i> Formulir Pendaftaran</h3>
                <form method="post" action="app/controllers/pmb_crud.php" class="space-y-4" enctype="multipart/form-data">
                    <input class="w-full px-4 py-2 border rounded-lg" type="text" name="nama" placeholder="Nama Lengkap" required>
                    <input class="w-full px-4 py-2 border rounded-lg" type="email" name="email" placeholder="Email" required>
                    <input class="w-full px-4 py-2 border rounded-lg" type="text" name="asal_sekolah" placeholder="Asal Sekolah" required>
                    <select name="jurusan" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="" disabled selected>-- Pilih Jurusan --</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                        <option value="Manajemen">Manajemen</option>
                    </select>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <div class="mt-2 space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio" name="jenis_kelamin" value="Laki-laki" required>
                                <span class="ml-2">Laki-laki</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio" name="jenis_kelamin" value="Perempuan">
                                <span class="ml-2">Perempuan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="berkas" class="block text-sm font-medium text-gray-700">Unggah Berkas (PDF/JPG, max 2MB)</label>
                        <input id="berkas" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" type="file" name="berkas" required>
                    </div>

                    <button type="submit" name="daftar" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-paper-plane"></i> Daftar Sekarang
                    </button>
                </form>
            </div>
            
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-list"></i> Data Pendaftar</h2>
                <div class="space-x-2">
                     <a href="app/reports/export_pmb.php?format=excel" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="app/reports/export_pmb.php?format=pdf" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Jurusan</th>
                            <th class="py-3 px-4 text-left">Jenis Kelamin</th>
                            <th class="py-3 px-4 text-center">Berkas</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php while ($pmb = $data_pmb->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-3 px-4"><?= htmlspecialchars($pmb['nama']) ?></td>
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($pmb['jurusan']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($pmb['jenis_kelamin']) ?></td>
                            <td class="py-3 px-4 text-center">
                                <?php if (!empty($pmb['berkas'])): ?>
                                    <a href="uploads/<?= htmlspecialchars($pmb['berkas']) ?>" target="_blank" class="text-blue-500 hover:underline">
                                        <i class="fas fa-file-alt"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $pmb['status'] == 'diterima' ? 'bg-green-100 text-green-800' : '' ?>
                                    <?= $pmb['status'] == 'ditolak' ? 'bg-red-100 text-red-800' : '' ?>
                                    <?= $pmb['status'] == 'diproses' ? 'bg-yellow-100 text-yellow-800' : '' ?>">
                                    <?= htmlspecialchars($pmb['status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <?php if ($pmb['status'] == 'diproses'): ?>
                                    <a class="text-green-500" title="Terima" href='app/controllers/pmb_crud.php?terima=<?= $pmb['id'] ?>'><i class="fas fa-check"></i></a>
                                    <a class="text-yellow-600" title="Tolak" href='app/controllers/pmb_crud.php?tolak=<?= $pmb['id'] ?>'><i class="fas fa-times"></i></a>
                                    <a class="text-blue-500" title="Edit" href='edit_pmb.php?id=<?= $pmb['id'] ?>'><i class="fas fa-pencil-alt"></i></a>
                                <?php endif; ?>
                                <a class="text-red-500" title="Hapus" href='app/controllers/pmb_crud.php?hapus=<?= $pmb['id'] ?>' onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash-alt"></i></a>
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