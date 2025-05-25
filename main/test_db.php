<?php
$conn = mysqli_connect("localhost", "root", "", "tugas_mahasiswa");
if (!$conn) {
    die("Gagal koneksi: " . mysqli_connect_error());
}
echo "Koneksi berhasil!";
mysqli_close($conn);
?>