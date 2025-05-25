<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "tugas_mahasiswa";

// Buat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Pastikan tabel ada
// $check_table = "CREATE TABLE IF NOT EXISTS tugas_kuliah (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     mata_kuliah VARCHAR(100) NOT NULL,
//     deskripsi TEXT,
//     tenggat_waktu DATETIME NOT NULL,
//     prioritas ENUM('rendah', 'sedang', 'tinggi') DEFAULT 'sedang',
//     status ENUM('belum_selesai', 'selesai') DEFAULT 'belum_selesai',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     INDEX (status),
//     INDEX (prioritas),
//     INDEX (tenggat_waktu)
// )";

// if (!mysqli_query($conn, $check_table)) {
//     die("Error creating table: " . mysqli_error($conn));
// }
?>