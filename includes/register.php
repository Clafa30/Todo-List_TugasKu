<?php
session_start();
include 'config.php';
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = bersihkan($_POST['username']);
    $email = bersihkan($_POST['email']);
    $passwordInput = $_POST['password'];

    // Validasi dasar
    if (empty($username) || empty($email) || empty($passwordInput)) {
        redirect('register.php', 'Semua field harus diisi.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect('register.php', 'Format email tidak valid.');
    }

    if (strlen($passwordInput) < 6) {
        redirect('register.php', 'Password minimal 6 karakter.');
    }

    // Enkripsi password
    $password = password_hash($passwordInput, PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah digunakan
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        redirect('../index.php', 'Username atau email sudah terdaftar.');
    }

    $check->close();

    // Simpan user baru
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        redirect('../index.php', 'Pendaftaran berhasil. Silakan login.');
    } else {
        redirect('../index.php', 'Pendaftaran gagal.');
    }

    $stmt->close();
}
?>
