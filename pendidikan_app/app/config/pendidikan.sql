
CREATE DATABASE IF NOT EXISTS pendidikan;
USE pendidikan;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100),
    role ENUM('admin','mahasiswa','dosen') DEFAULT 'mahasiswa'
);

-- Tabel mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    tahun_masuk INT NOT NULL
);

-- Tabel pendaftaran (PMB)
CREATE TABLE IF NOT EXISTS pmb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    asal_sekolah VARCHAR(100),
    status ENUM('diproses','diterima','ditolak') DEFAULT 'diproses'
);

-- Contoh data awal admin
INSERT INTO users (username, password, nama_lengkap, role)
VALUES ('admin', 'admin123', 'Administrator', 'admin');
