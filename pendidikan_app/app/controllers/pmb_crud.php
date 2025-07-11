<?php
session_start();
require '../config/db.php';

// Fungsi untuk menangani upload file
function upload_berkas() {
    $namaFile = $_FILES['berkas']['name'];
    $ukuranFile = $_FILES['berkas']['size'];
    $error = $_FILES['berkas']['error'];
    $tmpName = $_FILES['berkas']['tmp_name'];

    // Cek apakah tidak ada file yang diupload
    if ($error === 4) {
        echo "<script>alert('Pilih file terlebih dahulu!');</script>";
        return false;
    }

    // Cek ekstensi file
    $ekstensiValid = ['jpg', 'jpeg', 'png', 'pdf'];
    $ekstensiFile = explode('.', $namaFile);
    $ekstensiFile = strtolower(end($ekstensiFile));
    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Yang Anda upload bukan gambar atau PDF!');</script>";
        return false;
    }

    // Cek ukuran file (max 2MB)
    if ($ukuranFile > 2000000) {
        echo "<script>alert('Ukuran file terlalu besar!');</script>";
        return false;
    }

    // Lolos pengecekan, generate nama baru dan pindahkan file
    $namaFileBaru = uniqid() . '.' . $ekstensiFile;
    move_uploaded_file($tmpName, '../../uploads/' . $namaFileBaru);
    return $namaFileBaru;
}


// Proses Pendaftaran Baru (Create)
if (isset($_POST['daftar'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $asal_sekolah = htmlspecialchars($_POST['asal_sekolah']);
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);

    // Upload berkas
    $berkas = upload_berkas();
    if (!$berkas) {
        // Jika upload gagal, hentikan proses
        header("Location: ../../pmb.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO pmb (nama, email, asal_sekolah, jurusan, jenis_kelamin, berkas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $email, $asal_sekolah, $jurusan, $jenis_kelamin, $berkas);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../pmb.php");
    exit;
}

// ... (kode untuk update, terima, tolak, hapus tetap sama) ...
// Proses UPDATE Data Pendaftar
if (isset($_POST['update_pmb'])) {
    // ...
}
// Proses Terima Pendaftar
if (isset($_GET['terima'])) {
    // ...
}
// Proses Tolak Pendaftar
if (isset($_GET['tolak'])) {
    // ...
}
// Proses Hapus Pendaftar
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Hapus file berkas terkait sebelum hapus data DB
    $result = $conn->query("SELECT berkas FROM pmb WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (file_exists('../../uploads/' . $row['berkas'])) {
            unlink('../../uploads/' . $row['berkas']);
        }
    }
    $conn->query("DELETE FROM pmb WHERE id=$id");
    header("Location: ../../pmb.php");
    exit;
}