<?php
require 'app/config/db.php';
$data = $conn->query("SELECT * FROM mahasiswa");
?>
<h2>Data Mahasiswa</h2>
<table border="1" cellpadding="5">
<tr><th>Nama</th><th>NIM</th><th>Jurusan</th><th>Tahun</th><th>Aksi</th></tr>
<?php while ($mhs = $data->fetch_assoc()): ?>
<tr>
<td><?= $mhs['nama'] ?></td>
<td><?= $mhs['nim'] ?></td>
<td><?= $mhs['jurusan'] ?></td>
<td><?= $mhs['tahun_masuk'] ?></td>
<td><a href='app/controllers/mahasiswa_crud.php?delete=<?= $mhs['id'] ?>'>Hapus</a></td>
</tr>
<?php endwhile; ?>
</table>

<h3>Tambah Mahasiswa</h3>
<form method="post" action="app/controllers/mahasiswa_crud.php">
<input type="text" name="nama" placeholder="Nama" required>
<input type="text" name="nim" placeholder="NIM" required>
<input type="text" name="jurusan" placeholder="Jurusan" required>
<input type="number" name="tahun" placeholder="Tahun Masuk" required>
<button type="submit">Simpan</button>
</form>
<a href='app/reports/export_excel.php'>Export ke Excel</a>