<?php
session_start();
include '../includes/config.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$tugas_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Ambil daftar user selain yang sedang login
$stmt = $conn->prepare("SELECT id, username FROM users WHERE id != ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bagikan Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2><i class="bi bi-share-fill"></i> Bagikan Tugas</h2>
    <form method="POST" action="../includes/share_process.php">
        <input type="hidden" name="tugas_id" value="<?= $tugas_id ?>">
        <label for="user">Pilih pengguna untuk berbagi:</label>
        <select name="shared_to_user_id" id="user" class="form-control" required>
            <option value="">-- Pilih User --</option>
            <?php while ($user = $result->fetch_assoc()): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
            <?php endwhile; ?>
        </select>
        <br>
        <button type="submit" class="btn btn-primary"><i class="bi bi-send-check-fill"></i> Bagikan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
