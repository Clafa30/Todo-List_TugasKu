<?php
ob_start(); // Hindari error karena output sebelum header()
session_start();
include 'config.php'; // koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bersihkan input
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Validasi sederhana
    if (empty($username) || empty($password)) {
        header("Location: ../index.php?error=Username dan password harus diisi.");
        exit;
    }

    // Cek user di database
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $user, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Simpan data ke session
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $user;

            // Redirect ke dashboard
            header("Location: ../main/index.php");
            exit;
        } else {
            header("Location: ../index.php?error=Password salah.");
        }
    } else {
        header("Location: ../index.php?error=Username tidak ditemukan.");
    }

    $stmt->close();
}
?>
