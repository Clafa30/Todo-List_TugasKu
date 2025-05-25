<?php
session_start();
include '../includes/config.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('../images/bg-pattern.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-card {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .profile-card h2 {
            margin-bottom: 1.5rem;
            color: #343a40;
        }

        .profile-card p {
            font-size: 0.95rem;
            margin: 0.5rem 0;
        }

        .profile-card .label {
            font-weight: 600;
            color: #495057;
        }

        .button-group {
            margin-top: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.9rem;
            border: none;
            border-radius: 0.45rem;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
            transition: 0.2s ease-in-out;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-primary { background: #4361ee; color: #fff; }
        .btn-primary:hover { background: #3a56d4; }

        .btn-warning { background: #f8961e; color: #fff; }
        .btn-warning:hover { background: #e6850f; }

        .btn-danger { background: #f72585; color: #fff; }
        .btn-danger:hover { background: #e2176f; }

        .btn-light { background: #e9ecef; color: #343a40; }
        .btn-light:hover { background: #dee2e6; }

        hr {
            margin: 2rem 0 1rem;
            border: none;
            height: 1px;
            background-color: #dee2e6;
        }
    </style>
</head>
<body>
<div class="profile-card">
    <h2><i class="bi bi-person-circle"></i> Profil Pengguna</h2>

    <p><span class="label">Username:</span><br> <?= htmlspecialchars($username) ?></p>
    <p><span class="label">Email:</span><br> <?= htmlspecialchars($email) ?></p>

    <div class="button-group">
        <a href="edit_profile.php" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="edit_password.php" class="btn btn-warning"><i class="bi bi-key"></i> Password</a>
        <button id="btnLogout" class="btn btn-danger">Logout</button>
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <hr>

    <div class="button-group">
        <a href="../main/index.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

</body>
</html>
