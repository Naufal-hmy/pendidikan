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
 * Fungsi untuk mendapatkan ID dari tabel 'mahasiswa' berdasarkan user_id dari session.
 * Ini penting untuk menghubungkan data user dengan data mahasiswa.
 * @param mysqli $conn Koneksi database
 * @return int|null ID mahasiswa atau null jika tidak ditemukan.
 */
function get_mahasiswa_id($conn) {
    if (!isset($_SESSION['user_id'])) return null;

    $user_id = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM mahasiswa WHERE nim = (SELECT username FROM users WHERE id = ?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['id'];
    }
    return null;
}


// ===================================================================
// CRUD MATA KULIAH (Hanya Admin)
// ===================================================================

if (isset($_POST['simpan_matkul'])) {
    cek_admin();
    $kode_mk = htmlspecialchars($_POST['kode_mk']);
    $nama_mk = htmlspecialchars($_POST['nama_mk']);
    $sks = (int)$_POST['sks'];
    $semester = (int)$_POST['semester_target'];

    $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester_target) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $kode_mk, $nama_mk, $sks, $semester);
    $stmt->execute();
    header("Location: ../../manajemen_matkul.php");
    exit;
}

if (isset($_GET['hapus_matkul'])) {
    cek_admin();
    $id = (int)$_GET['hapus_matkul'];
    $conn->query("DELETE FROM mata_kuliah WHERE id=$id");
    header("Location: ../../manajemen_matkul.php");
    exit;
}


// ===================================================================
// CRUD JADWAL KULIAH (Hanya Admin)
// ===================================================================

if (isset($_POST['simpan_jadwal'])) {
    cek_admin();
    $matkul_id = (int)$_POST['matkul_id'];
    $dosen_id = (int)$_POST['dosen_id'];
    $hari = htmlspecialchars($_POST['hari']);
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $ruangan = htmlspecialchars($_POST['ruangan']);
    $tahun_akademik = htmlspecialchars($_POST['tahun_akademik']);

    $stmt = $conn->prepare("INSERT INTO jadwal_kuliah (matkul_id, dosen_id, hari, jam_mulai, jam_selesai, ruangan, tahun_akademik) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $matkul_id, $dosen_id, $hari, $jam_mulai, $jam_selesai, $ruangan, $tahun_akademik);
    $stmt->execute();
    header("Location: ../../manajemen_jadwal.php");
    exit;
}

if (isset($_GET['hapus_jadwal'])) {
    cek_admin();
    $id = (int)$_GET['hapus_jadwal'];
    $conn->query("DELETE FROM jadwal_kuliah WHERE id=$id");
    header("Location: ../../manajemen_jadwal.php");
    exit;
}


// ===================================================================
// MANAJEMEN KRS (Hanya Mahasiswa)
// ===================================================================

if (isset($_GET['ambil_krs'])) {
    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'mahasiswa') {
        die("AKSES DITOLAK: Hanya mahasiswa yang bisa mengambil KRS.");
    }
    
    $mahasiswa_id = get_mahasiswa_id($conn);
    $jadwal_id = (int)$_GET['ambil_krs'];

    if ($mahasiswa_id) {
        $cek = $conn->query("SELECT id FROM krs WHERE mahasiswa_id = $mahasiswa_id AND jadwal_id = $jadwal_id");
        if ($cek->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO krs (mahasiswa_id, jadwal_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $mahasiswa_id, $jadwal_id);
            $stmt->execute();
        }
    }
    header("Location: ../../krs.php");
    exit;
}

if (isset($_GET['batal_krs'])) {
    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'mahasiswa') {
        die("AKSES DITOLAK: Hanya mahasiswa yang bisa membatalkan KRS.");
    }

    $mahasiswa_id = get_mahasiswa_id($conn);
    $krs_id = (int)$_GET['batal_krs'];

    if ($mahasiswa_id) {
        $stmt = $conn->prepare("DELETE FROM krs WHERE id = ? AND mahasiswa_id = ? AND status_approval = 'Menunggu'");
        $stmt->bind_param("ii", $krs_id, $mahasiswa_id);
        $stmt->execute();
    }
    header("Location: ../../krs.php");
    exit;
}


// ===================================================================
// PERSETUJUAN KRS (Hanya Admin)
// ===================================================================

if (isset($_GET['setujui_krs'])) {
    cek_admin();
    $krs_id = (int)$_GET['setujui_krs'];
    $mahasiswa_id = (int)$_GET['mhs_id'];

    $conn->query("UPDATE krs SET status_approval = 'Disetujui' WHERE id = $krs_id");
    header("Location: ../../detail_persetujuan_krs.php?mhs_id=$mahasiswa_id");
    exit;
}

if (isset($_GET['tolak_krs'])) {
    cek_admin();
    $krs_id = (int)$_GET['tolak_krs'];
    $mahasiswa_id = (int)$_GET['mhs_id'];

    $conn->query("UPDATE krs SET status_approval = 'Ditolak' WHERE id = $krs_id");
    header("Location: ../../detail_persetujuan_krs.php?mhs_id=$mahasiswa_id");
    exit;
}


// ===================================================================
// INPUT NILAI KHS (Hanya Admin)
// ===================================================================

if (isset($_POST['simpan_nilai'])) {
    cek_admin();

    $krs_ids = $_POST['krs_id'];
    $nilai_hurufs = $_POST['nilai_huruf'];

    $nilai_map = [
        'A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0
    ];

    $stmt = $conn->prepare("INSERT INTO khs (krs_id, nilai_huruf, nilai_angka) VALUES (?, ?, ?)");

    foreach ($krs_ids as $index => $krs_id) {
        $nilai_huruf = $nilai_hurufs[$index];
        if (!empty($nilai_huruf)) {
            $nilai_angka = $nilai_map[$nilai_huruf];
            $stmt->bind_param("isd", $krs_id, $nilai_huruf, $nilai_angka);
            $stmt->execute();
        }
    }

    $stmt->close();
    header("Location: ../../input_nilai.php");
    exit;
}
?>
