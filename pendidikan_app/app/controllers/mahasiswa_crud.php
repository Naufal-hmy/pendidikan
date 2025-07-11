<?php
session_start(); // Mulai session untuk cek role
require '../config/db.php';

// PENGECEKAN HAK AKSES - HANYA ADMIN
// Jika tidak login atau bukan admin, hentikan eksekusi
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    die("AKSES DITOLAK: Anda tidak punya izin untuk melakukan tindakan ini.");
}

// Proses TAMBAH Mahasiswa Baru
if (isset($_POST['simpan'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $nim = htmlspecialchars($_POST['nim']);
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $tahun = htmlspecialchars($_POST['tahun']);

    $stmt = $conn->prepare("INSERT INTO mahasiswa (nama, nim, jurusan, tahun_masuk) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nama, $nim, $jurusan, $tahun);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../mahasiswa.php");
    exit;
}

// Proses UPDATE Data Mahasiswa
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = htmlspecialchars($_POST['nama']);
    $nim = htmlspecialchars($_POST['nim']);
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $tahun = htmlspecialchars($_POST['tahun']);

    $stmt = $conn->prepare("UPDATE mahasiswa SET nama = ?, nim = ?, jurusan = ?, tahun_masuk = ? WHERE id = ?");
    $stmt->bind_param("sssii", $nama, $nim, $jurusan, $tahun, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../mahasiswa.php");
    exit;
}


// Proses DELETE Data Mahasiswa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM mahasiswa WHERE id=$id");
    header("Location: ../../mahasiswa.php");
    exit;
}
?>