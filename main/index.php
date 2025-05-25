<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Include config & functions
include __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/functions.php';

// Get current user ID
$user_id = $_SESSION['user_id'] ?? 0;

// Get For User
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Filter dan sorting
$status_filter = isset($_GET['status']) ? bersihkan($_GET['status']) : '';
$priority_filter = isset($_GET['priority']) ? bersihkan($_GET['priority']) : '';
$allowed_sort_columns = [
    'tenggat_waktu ASC',
    'tenggat_waktu DESC',
    'prioritas DESC',
    'created_at DESC'
];
$sort = in_array($sort = $_GET['sort'] ?? 'tenggat_waktu ASC', $allowed_sort_columns) ? $sort : 'tenggat_waktu ASC';

// Filter SQL tambahan
$status_sql = $status_filter ? "AND tk.status = '$status_filter'" : '';
$priority_sql = $priority_filter ? "AND tk.prioritas = '$priority_filter'" : '';

$query = "
    (
        SELECT tk.*, NULL AS shared_by_user_id, NULL AS shared_by_username
        FROM tugas_kuliah tk
        WHERE tk.user_id = $user_id $status_sql $priority_sql
    )
    UNION
    (
        SELECT tk.*, st.shared_by_user_id, u.username AS shared_by_username
        FROM tugas_kuliah tk
        JOIN shared_tasks st ON tk.id = st.tugas_id
        JOIN users u ON st.shared_by_user_id = u.id
        WHERE st.shared_to_user_id = $user_id $status_sql $priority_sql
    )
    ORDER BY $sort
";


// Eksekusi query
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengelola Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header (Navbar) -->
    <header class="navbar">
    <div class="navbar-container">
        <h1 class="brand-title">
        <i class="bi bi-journal-check"></i> TugasKu
        </h1>

        <div class="navbar-actions">
        <a href="insert.php" class="btn-icon" title="Tugas Baru">
            <i class="bi bi-plus-circle"></i>
        </a>
        <button id="filterToggleBtn" class="btn-icon" title="Filter">
            <i class="bi bi-funnel-fill"></i>
        </button>
        <button id="profileToggleBtn" class="btn-user">
        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
        </button>
        </div>
    </div>
    </header>

    <main class="container">
        <?php echo showFlashMessage(); ?>

        <!-- Overlay Filter -->
        <div id="filterOverlay" class="filter-overlay hidden">
        <div class="filter-content">
            <button id="closeFilterBtn" class="btn btn-danger btn-sm">Tutup</button>

            <!-- Filter Status -->
            <div>
            <label>Status:</label>
            <select onchange="window.location.href='?status='+this.value" class="form-control">
                <option value="">Semua</option>
                <option value="belum_selesai" <?= $status_filter == 'belum_selesai' ? 'selected' : '' ?>>Belum Selesai</option>
                <option value="selesai" <?= $status_filter == 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
            </div>

            <!-- Filter Prioritas -->
            <div>
            <label>Prioritas:</label>
            <select onchange="window.location.href='?priority='+this.value" class="form-control">
                <option value="">Semua</option>
                <option value="rendah" <?= $priority_filter == 'rendah' ? 'selected' : '' ?>>Rendah</option>
                <option value="sedang" <?= $priority_filter == 'sedang' ? 'selected' : '' ?>>Sedang</option>
                <option value="tinggi" <?= $priority_filter == 'tinggi' ? 'selected' : '' ?>>Tinggi</option>
            </select>
            </div>

            <!-- Sort -->
            <div>
            <label>Urutkan:</label>
            <select onchange="window.location.href='?sort='+this.value" class="form-control">
                <option value="tenggat_waktu ASC" <?= $sort == 'tenggat_waktu ASC' ? 'selected' : '' ?>>Terdekat</option>
                <option value="tenggat_waktu DESC" <?= $sort == 'tenggat_waktu DESC' ? 'selected' : '' ?>>Terjauh</option>
                <option value="prioritas DESC" <?= $sort == 'prioritas DESC' ? 'selected' : '' ?>>Prioritas</option>
                <option value="created_at DESC" <?= $sort == 'created_at DESC' ? 'selected' : '' ?>>Terbaru</option>
            </select>
            </div>
        </div>
        </div>

        <div class="task-list">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="task-card">
                        <div class="task-header">
                            <h3><?= htmlspecialchars($row['mata_kuliah']) ?></h3>
                            <div class="badge <?= getPriorityClass($row['prioritas']) ?>">
                                <?= ucfirst($row['prioritas']) ?>
                            </div>
                        </div>
                        <p class="deskripsi"><?= nl2br(htmlspecialchars(str_replace(["\\r\\n", "\\n", "\\r"], "\n", $row['deskripsi']))) ?></p>
                        <div class="task-footer">
                            <div class="task-time-status">
                                <small><i class="bi bi-clock"></i> <?= date('d M Y H:i', strtotime($row['tenggat_waktu'])) ?></small>
                                <small><?= getStatusBadge($row['status']) ?></small>
                                <?php if (!empty($row['shared_by_user_id'])): ?>
                                    <small class="badge rounded-pill bg-info">Dishare oleh <?= htmlspecialchars($row['shared_by_username']) ?></small>  
                                <?php endif; ?>
                            </div>
                            <div class="task-actions">
                                <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tugas ini?')"><i class="bi bi-trash"></i></a>
                                <?php if ($row['status'] == 'belum_selesai'): ?>
                                    <a href="mark_complete.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i></a>
                                <?php endif; ?>

                                <!-- Tetsing phase share -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Bagikan Tugas">
                                        <i class="bi bi-share-fill"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="openShareModal(<?= $row['id'] ?>); return false;">
                                                <i class="bi bi-person-fill"></i> Share ke User
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://wa.me/?text=<?= urlencode($row['mata_kuliah'] . ' - ' . strip_tags($row['deskripsi'])) ?>" target="_blank" rel="noopener">
                                                <i class="bi bi-whatsapp"></i> Share via WhatsApp
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center;">Tidak ada tugas ditemukan.</p>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Modal Share ke User -->
    <div id="shareModal" style="display:none; position:fixed;top:50%;left:50%;transform:translate(-50%, -50%);
    background:#fff; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.3); z-index:10000;">
    <h5>Share Tugas via User</h5>
    <form id="shareForm" onsubmit="return submitShare(event)">
        <input type="hidden" id="taskId" name="task_id" value="">
        <label for="userInput">Masukkan Username atau User ID:</label><br>
        <input type="text" id="userInput" name="user_input" required>
        <br><br>
        <button type="submit">Kirim</button>
        <button type="button" onclick="closeShareModal()">Batal</button>
    </form>
    <div id="shareFeedback" style="margin-top:10px;color:red;"></div>
    </div>

    <!-- Notifikasi Overlay -->
    <div id="notifOverlay" style="
    display:none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #28a745;
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    font-weight: bold;
    z-index: 10500;
    min-width: 200px;
    text-align: center;
    ">
    Berhasil membagikan tugas!
    </div>

    <!-- Profile Overlay -->
    <div id="profileOverlay" class="overlay hidden">
    <div class="profile-modal">
        <button id="closeProfileBtn" class="btn-close">&times;</button>
        <div class="profile-card">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($username) ?>" alt="Avatar" class="profile-avatar">

            <h2><i class="bi bi-person-circle"></i> Profil</h2>
            
            <div class="profile-info">
                <p><span class="label">User ID:</span> <?= htmlspecialchars($user_id) ?></p>
                <p><span class="label">Username:</span><br><span class="label-username"><?= htmlspecialchars($username) ?></span></p>
                <p><span class="label">Email:</span><br><span class="label-email"><?= htmlspecialchars($email) ?></span></p>
            </div>

            <div class="button-group">
                <a href="#" id="openEditProfileOverlay" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Profile</a>
                <a href="#" id="openPasswordOverlay" class="btn btn-warning"><i class="bi bi-key"></i>Password</a>
                <button id="btnLogout" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </div>
        </div>
    </div>
    </div>

    <!-- Password Overlay -->
    <div id="passwordOverlay" class="overlay hidden">
        <div class="modal-box">
            <button id="closePasswordBtn" class="btn-close">&times;</button>
            <h3><i class="bi bi-key"></i> Ganti Password</h3>
            <form id="passwordForm">
            <p id="passwordMessage" style="color: red; font-weight: 500;"></p>
                <label>Password Saat Ini:</label>
                <input type="password" name="current_password" class="form-control" required>
                
                <label>Password Baru:</label>
                <input type="password" name="new_password" class="form-control" required>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-warning">Simpan</button>
                    <button type="button" id="cancelPassword" class="btn btn-light">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Profile -->
    <div id="editProfileOverlay" class="overlay hidden">
        <div class="modal-box">
            <button id="closeEditProfileBtn" class="btn-close">&times;</button>
            <h3><i class="bi bi-pencil-square"></i> Edit Profil</h3>
            <form id="editProfileForm">
                <label>Username:</label>
                <input type="text" name="username" id="editUsername" class="form-control" value="<?= htmlspecialchars($username) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" id="editEmail" class="form-control" value="<?= htmlspecialchars($email) ?>" required>

                <div class="button-group">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" id="cancelEditProfile" class="btn btn-light">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logout Confirmation Overlay -->
    <div id="logoutOverlay" class="overlay hidden">
        <div class="modal-box">
            <h3><i class="bi bi-box-arrow-right"></i> Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin logout?</p>
            <div class="button-group">
                <button id="confirmLogout" class="btn btn-danger">Ya, Logout</button>
                <button id="cancelLogout" class="btn btn-secondary">Batal</button>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide flash message after 3 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.style.display = 'none';
        }, 3000);

        function showProfile() {
            document.getElementById("overlay").style.display = "block";
        }    
       
        // Untuk Share
        // testing phase
        function openShareModal(taskId) {
        document.getElementById('taskId').value = taskId;
        document.getElementById('shareFeedback').innerText = '';
        document.getElementById('shareModal').style.display = 'block';
        }

        function closeShareModal() {
        document.getElementById('shareModal').style.display = 'none';
        }

        async function submitShare(e) {
        e.preventDefault();
        const taskId = document.getElementById('taskId').value;
        const userInput = document.getElementById('userInput').value.trim();

        if (!userInput) {
            showNotif('Mohon masukkan username atau user ID.', false);
            return false;
        }

        // Kirim data via fetch ke backend
        try {
            const response = await fetch('/includes/share_process.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({task_id: taskId, user_input: userInput})
            });
            const data = await response.json();

            if (data.success) {
            showNotif('Tugas berhasil dibagikan.', true);
            setTimeout(() => {
                closeShareModal();
            }, 1500);
            } else {
            showNotif(data.message || 'Terjadi kesalahan.', false);
            }
        } catch (err) {
            showNotif('Gagal mengirim data. Coba lagi.', false);
        }

        return false;
        }

        function showNotif(message, isSuccess) {
        const notif = document.getElementById('notifOverlay');
        notif.innerText = message;
        notif.style.backgroundColor = isSuccess ? '#28a745' : '#dc3545'; // hijau/red
        notif.style.display = 'block';

        setTimeout(() => {
            notif.style.display = 'none';
        }, 2500);
        }

        // Overlay Filter
        const filterToggleBtn = document.getElementById('filterToggleBtn');
        const filterOverlay = document.getElementById('filterOverlay');
        const closeFilterBtn = document.getElementById('closeFilterBtn');

        filterToggleBtn.addEventListener('click', () => {
        filterOverlay.classList.remove('hidden');
        });

        closeFilterBtn.addEventListener('click', () => {
        filterOverlay.classList.add('hidden');
        });

        // Optional: klik di luar filter-content juga tutup overlay
        filterOverlay.addEventListener('click', (e) => {
        if (e.target === filterOverlay) {
            filterOverlay.classList.add('hidden');
        }
        });

        // User Overlay
        document.getElementById('profileToggleBtn').addEventListener('click', function() {
        document.getElementById('profileOverlay').classList.remove('hidden');
        });

        document.getElementById('closeProfileBtn').addEventListener('click', function() {
        document.getElementById('profileOverlay').classList.add('hidden');
        });

        document.getElementById('profileOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
        });

        // Overlay Ganti Password
        document.getElementById('openPasswordOverlay').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('passwordOverlay').classList.remove('hidden');
        });

        document.getElementById('closePasswordBtn').addEventListener('click', function() {
            document.getElementById('passwordOverlay').classList.add('hidden');
        });

        document.getElementById('cancelPassword').addEventListener('click', function() {
            document.getElementById('passwordOverlay').classList.add('hidden');
        });

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('edit_password.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msgEl = document.getElementById('passwordMessage');
                msgEl.textContent = data.message;
                msgEl.style.color = data.success ? 'green' : 'red';

                if (data.success) {
                    setTimeout(() => {
                        msgEl.textContent = '';
                        document.getElementById('passwordOverlay').classList.add('hidden');
                        document.getElementById('passwordForm').reset();
                    }, 1500);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan saat mengubah password.');
            });
        });

        document.getElementById('cancelPassword').addEventListener('click', function() {
            document.getElementById('passwordOverlay').classList.add('hidden');
            document.getElementById('passwordForm').reset();
            document.getElementById('passwordMessage').textContent = '';
        });
 
        // Overlay Edit Profile 
        document.getElementById('openEditProfileOverlay').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('editProfileOverlay').classList.remove('hidden');
        });

        document.getElementById('closeEditProfileBtn').addEventListener('click', function() {
            document.getElementById('editProfileOverlay').classList.add('hidden');
        });

        document.getElementById('cancelEditProfile').addEventListener('click', function() {
            document.getElementById('editProfileOverlay').classList.add('hidden');
        });

        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('edit_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update tampilan di halaman
                    document.querySelector('.label-username').textContent = data.username;
                    document.querySelector('.label-email').textContent = data.email;

                    // Tutup overlay
                    document.getElementById('editProfileOverlay').classList.add('hidden');
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan.');
            });
        });

        // Overlay LogOut
        document.getElementById('btnLogout').addEventListener('click', function () {
            document.getElementById('logoutOverlay').classList.remove('hidden');
        });

        document.getElementById('cancelLogout').addEventListener('click', function () {
            document.getElementById('logoutOverlay').classList.add('hidden');
        });

        document.getElementById('confirmLogout').addEventListener('click', function () {
            window.location.href = 'logout.php';
        });

    </script>
</body>
</html>