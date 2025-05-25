<?php
session_start();
include __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    redirect('index.php', 'ID tugas tidak valid');
}

$id = bersihkan($_GET['id']);

// Update status menjadi selesai
$stmt = $conn->prepare("UPDATE tugas_kuliah SET status='selesai' WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirect('index.php', 'Tugas berhasil ditandai sebagai selesai');
} else {
    redirect('index.php', 'Gagal memperbarui status tugas: ' . $stmt->error);
}

$stmt->close();
?>