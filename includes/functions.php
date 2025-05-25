<?php
function bersihkan($data) {
    global $conn;
    
    // Validasi koneksi database
    if (!isset($conn)) {
        die("Koneksi database tidak tersedia");
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['flash_message'] = $message;
    }
    header("Location: $url");
    exit();
}

function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return "<div class='alert alert-success'>$message</div>";
    }
    return '';
}

function getStatusBadge($status) {
    return $status == 'selesai' 
        ? '<span class="badge badge-success">Selesai</span>' 
        : '<span class="badge badge-primary">Belum Selesai</span>';
}

function getPriorityClass($priority) {
    switch ($priority) {
        case 'rendah': return 'priority-low';
        case 'tinggi': return 'priority-high';
        default: return 'priority-medium';
    }
}

// Fungsi tambahan untuk memeriksa koneksi database sebelum operasi query
function checkDatabaseConnection() {
    global $conn;
    if (!$conn) {
        die("Koneksi database gagal");
    }
}
?>