<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];
    $tahun = $_POST['tahun'];

    $conn->query("INSERT INTO mahasiswa (nama, nim, jurusan, tahun_masuk) VALUES ('$nama', '$nim', '$jurusan', '$tahun')");
    header("Location: ../../mahasiswa.php");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM mahasiswa WHERE id=$id");
    header("Location: ../../mahasiswa.php");
}
?>