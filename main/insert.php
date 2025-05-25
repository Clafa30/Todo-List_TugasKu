<?php
session_start();
include __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mata_kuliah = bersihkan($_POST['mata_kuliah']);
    $deskripsi = bersihkan($_POST['deskripsi']);
    $tenggat_waktu = bersihkan($_POST['tenggat_waktu']);
    $prioritas = bersihkan($_POST['prioritas']);

    $user_id = $_SESSION['user_id']; // Ambil user ID dari session login

    $stmt = $conn->prepare("INSERT INTO tugas_kuliah (user_id, mata_kuliah, deskripsi, tenggat_waktu, prioritas) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $mata_kuliah, $deskripsi, $tenggat_waktu, $prioritas);
    
    if ($stmt->execute()) {
        redirect('index.php', 'Tugas berhasil ditambahkan');
    } else {
        $error = "Gagal menambahkan tugas: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-journal-plus"></i> Tambah Tugas Baru</h2>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="mata_kuliah" class="form-label">Mata Kuliah</label>
                        <input type="text" name="mata_kuliah" id="mata_kuliah" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Tugas</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tenggat_waktu" class="form-label">Tenggat Waktu</label>
                        <input type="datetime-local" name="tenggat_waktu" id="tenggat_waktu" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label for="prioritas" class="form-label">Prioritas</label>
                        <select name="prioritas" id="prioritas" class="form-select" required>
                            <option value="rendah">Rendah</option>
                            <option value="sedang" selected>Sedang</option>
                            <option value="tinggi">Tinggi</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Tugas
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>