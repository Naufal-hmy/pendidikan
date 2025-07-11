<?php
session_start();
require '../config/db.php';

// Fungsi untuk memeriksa apakah pengguna yang login adalah admin.
function cek_admin() {
    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
        die("AKSES DITOLAK: Anda tidak punya izin untuk melakukan tindakan ini.");
    }
}

// Proses Simpan Dosen Baru
if (isset($_POST['simpan_dosen'])) {
    cek_admin();
    $nama_lengkap = htmlspecialchars($_POST['nama_lengkap']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Menggunakan hash untuk keamanan
    $role = 'dosen';

    $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $nama_lengkap, $role);
    $stmt->execute();
    header("Location: ../../manajemen_dosen.php");
    exit;
}

// Proses Update Data Dosen
if (isset($_POST['update_dosen'])) {
    cek_admin();
    $id = (int)$_POST['id'];
    $nama_lengkap = htmlspecialchars($_POST['nama_lengkap']);
    $username = htmlspecialchars($_POST['username']);
    
    // Cek apakah password baru diisi
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, username = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama_lengkap, $username, $password, $id);
    } else {
        // Jika password tidak diubah
        $stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, username = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nama_lengkap, $username, $id);
    }
    $stmt->execute();
    header("Location: ../../manajemen_dosen.php");
    exit;
}

// Proses Hapus Dosen
if (isset($_GET['hapus_dosen'])) {
    cek_admin();
    $id = (int)$_GET['hapus_dosen'];
    // Tambahan: Sebaiknya cek dulu apakah dosen ini terkait dengan jadwal kuliah sebelum dihapus
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'dosen'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ../../manajemen_dosen.php");
    exit;
}

?>
