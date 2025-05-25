<?php
session_start();
include __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    redirect('index.php', 'ID tugas tidak valid');
}

$id = bersihkan($_GET['id']);

// Hapus tugas
$stmt = $conn->prepare("DELETE FROM tugas_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    redirect('index.php', 'Tugas berhasil dihapus');
} else {
    redirect('index.php', 'Gagal menghapus tugas: ' . $stmt->error);
}

$stmt->close();
?>
