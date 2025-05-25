<?php
session_start();
include '../includes/config.php';

// Enable error reporting untuk debugging (hapus di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ambil data JSON dari fetch
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$tugas_id = intval($data['task_id'] ?? 0);
$user_input = trim($data['user_input'] ?? '');
$shared_by_user_id = $_SESSION['user_id'] ?? 0;

if (!$tugas_id || !$user_input) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
    exit;
}

// Cek apakah user_input berupa username atau user_id (angka)
if (ctype_digit($user_input)) {
    $shared_to_user_id = intval($user_input);
} else {
    // Cari user berdasarkan username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $user_input);
    $stmt->execute();
    $stmt->bind_result($shared_to_user_id);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
        exit;
    }
    $stmt->close();
}

// Cegah duplikasi share
$cek = $conn->prepare("SELECT id FROM shared_tasks WHERE tugas_id = ? AND shared_to_user_id = ?");
$cek->bind_param("ii", $tugas_id, $shared_to_user_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO shared_tasks (tugas_id, shared_to_user_id, shared_by_user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $tugas_id, $shared_to_user_id, $shared_by_user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data share']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Tugas sudah dibagikan ke user ini']);
}
$cek->close();
