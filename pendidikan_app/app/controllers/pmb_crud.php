<?php
session_start();
// FIX: Menggunakan __DIR__ untuk membuat path absolut ke file config
require __DIR__ . '/../config/db.php';

// ===================================================================
// FUNGSI BANTU (HELPERS)
// ===================================================================

/**
 * Fungsi untuk memeriksa apakah pengguna yang login adalah admin.
 * Jika tidak, skrip akan dihentikan.
 */
function cek_admin() {
    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
        die("AKSES DITOLAK: Anda tidak punya izin untuk melakukan tindakan ini.");
    }
}

/**
 * Fungsi untuk menangani upload file berkas pendaftaran.
 * @return string|false Nama file yang berhasil diupload atau false jika gagal.
 */
function upload_berkas() {
    if (!isset($_FILES['berkas']) || $_FILES['berkas']['error'] === UPLOAD_ERR_NO_FILE) {
        // Jika form edit tidak menyertakan file baru, ini bukan error.
        return null; 
    }

    $namaFile = $_FILES['berkas']['name'];
    $ukuranFile = $_FILES['berkas']['size'];
    $error = $_FILES['berkas']['error'];
    $tmpName = $_FILES['berkas']['tmp_name'];

    if ($error !== UPLOAD_ERR_OK) {
        echo "<script>alert('Terjadi kesalahan saat mengupload file.'); window.history.back();</script>";
        return false;
    }

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'pdf'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format file tidak valid! Hanya JPG, PNG, atau PDF yang diizinkan.'); window.history.back();</script>";
        return false;
    }

    if ($ukuranFile > 2000000) { // 2MB
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.'); window.history.back();</script>";
        return false;
    }

    $namaFileBaru = uniqid() . '.' . $ekstensiFile;
    if (move_uploaded_file($tmpName, __DIR__ . '/../../uploads/' . $namaFileBaru)) {
        return $namaFileBaru;
    }
    
    return false;
}


// ===================================================================
// PROSES CRUD PMB
// ===================================================================

// Proses Pendaftaran Baru (Create) - Boleh diakses publik/calon mahasiswa
if (isset($_POST['daftar'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $asal_sekolah = htmlspecialchars($_POST['asal_sekolah']);
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);

    $berkas = upload_berkas();
    if ($berkas === false) {
        exit; // Hentikan jika upload gagal
    }

    $stmt = $conn->prepare("INSERT INTO pmb (nama, email, asal_sekolah, jurusan, jenis_kelamin, berkas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $email, $asal_sekolah, $jurusan, $jenis_kelamin, $berkas);
    $stmt->execute();
    echo "<script>alert('Pendaftaran berhasil!'); window.location.href='../../pmb.php';</script>"; // Ganti dengan redirect ke halaman login/info jika perlu
    exit;
}

// Proses UPDATE Data Pendaftar (Hanya Admin)
if (isset($_POST['update_pmb'])) {
    cek_admin();
    $id = (int)$_POST['id'];
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $asal_sekolah = htmlspecialchars($_POST['asal_sekolah']);
    $jurusan = htmlspecialchars($_POST['jurusan']);
    // Logika untuk update berkas bisa ditambahkan di sini jika diperlukan

    $stmt = $conn->prepare("UPDATE pmb SET nama = ?, email = ?, asal_sekolah = ?, jurusan = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nama, $email, $asal_sekolah, $jurusan, $id);
    $stmt->execute();
    header("Location: ../../pmb.php");
    exit;
}

// Proses Terima Pendaftar (Hanya Admin)
if (isset($_GET['terima'])) {
    cek_admin();
    $id = (int)$_GET['terima'];

    $result = $conn->query("SELECT * FROM pmb WHERE id=$id AND status = 'diproses'");
    if ($result->num_rows > 0) {
        $calon_mhs = $result->fetch_assoc();
        $nama_lengkap = $calon_mhs['nama'];
        $jurusan = $calon_mhs['jurusan'];
        $tahun_masuk = date('Y');
        $nim = substr($tahun_masuk, -2) . str_pad($id, 4, '0', STR_PAD_LEFT);

        // Masukkan ke tabel mahasiswa
        $stmt_mhs = $conn->prepare("INSERT INTO mahasiswa (nama, nim, jurusan, tahun_masuk) VALUES (?, ?, ?, ?)");
        $stmt_mhs->bind_param("sssi", $nama_lengkap, $nim, $jurusan, $tahun_masuk);
        $stmt_mhs->execute();

        // Buat akun login untuk mahasiswa baru
        $username = $nim;
        $password = "password123"; // Password default tanpa hash
        $role = "mahasiswa";
        
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt_user->bind_param("ssss", $username, $password, $nama_lengkap, $role);
        $stmt_user->execute();
        
        // Update status pendaftar
        $conn->query("UPDATE pmb SET status = 'diterima' WHERE id=$id");
    }
    header("Location: ../../pmb.php");
    exit;
}

// Proses Tolak Pendaftar (Hanya Admin)
if (isset($_GET['tolak'])) {
    cek_admin();
    $id = (int)$_GET['tolak'];
    $conn->query("UPDATE pmb SET status = 'ditolak' WHERE id = $id");
    header("Location: ../../pmb.php");
    exit;
}

// Proses Hapus Pendaftar (Hanya Admin)
if (isset($_GET['hapus'])) {
    cek_admin();
    $id = (int)$_GET['hapus'];
    
    // Hapus file berkas terkait sebelum hapus data dari DB
    $result = $conn->query("SELECT berkas FROM pmb WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['berkas']) && file_exists(__DIR__ . '/../../uploads/' . $row['berkas'])) {
            unlink(__DIR__ . '/../../uploads/' . $row['berkas']);
        }
    }
    
    $conn->query("DELETE FROM pmb WHERE id=$id");
    header("Location: ../../pmb.php");
    exit;
}
?>
