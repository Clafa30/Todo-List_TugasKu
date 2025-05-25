<?php
session_start();
include __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id'])) {
    redirect('index.php', 'ID tugas tidak valid');
}

$id = bersihkan($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM tugas_kuliah WHERE id = $id");
$tugas = mysqli_fetch_assoc($result);

if (!$tugas) {
    redirect('index.php', 'Tugas tidak ditemukan');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mata_kuliah = bersihkan($_POST['mata_kuliah']);
    $deskripsi = bersihkan($_POST['deskripsi']);
    $tenggat_waktu = bersihkan($_POST['tenggat_waktu']);
    $prioritas = bersihkan($_POST['prioritas']);
    $status = bersihkan($_POST['status']);

    $stmt = $conn->prepare("UPDATE tugas_kuliah SET mata_kuliah=?, deskripsi=?, tenggat_waktu=?, prioritas=?, status=? WHERE id=?");
    $stmt->bind_param("sssssi", $mata_kuliah, $deskripsi, $tenggat_waktu, $prioritas, $status, $id);

    if ($stmt->execute()) {
        redirect('index.php', 'Tugas berhasil diperbarui');
    } else {
        $error = "Gagal memperbarui tugas: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1><i class="bi bi-journal-text"></i> Edit Tugas</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <input type="text" name="mata_kuliah" class="form-control" value="<?= htmlspecialchars($tugas['mata_kuliah']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Tugas</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($tugas['deskripsi']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tenggat Waktu</label>
                        <input type="datetime-local" name="tenggat_waktu" class="form-control"
                               value="<?= date('Y-m-d\TH:i', strtotime($tugas['tenggat_waktu'])) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Prioritas</label>
                        <select name="prioritas" class="form-select" required>
                            <option value="rendah" <?= $tugas['prioritas'] == 'rendah' ? 'selected' : '' ?>>Rendah</option>
                            <option value="sedang" <?= $tugas['prioritas'] == 'sedang' ? 'selected' : '' ?>>Sedang</option>
                            <option value="tinggi" <?= $tugas['prioritas'] == 'tinggi' ? 'selected' : '' ?>>Tinggi</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="belum_selesai" <?= $tugas['status'] == 'belum_selesai' ? 'selected' : '' ?>>Belum Selesai</option>
                            <option value="selesai" <?= $tugas['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>