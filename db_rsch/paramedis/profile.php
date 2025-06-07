<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$pesan = '';
$error_password = '';

// Ambil data user sekarang
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    die("User tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_profile'])) {
    // Update profil
    $nama       = $_POST['nama'];
    $no_telepon = $_POST['no_telepon'];
    $steam_hex  = $_POST['steam_hex'];
    $id_user = $_POST['id_user'];
    $username   = $_POST['username'];

    $query = "UPDATE users SET 
                nama = ?, 
                no_telepon = ?, 
                steam_hex = ?, 
                id_user = ?, 
                username = ? 
              WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "sssssi", $nama, $no_telepon, $steam_hex, $id_petugas, $username, $user_id);
      if (mysqli_stmt_execute($stmt)) {
        $pesan = "Profil berhasil diperbarui.";
        // Refresh data setelah update
        $data['nama'] = $nama;
        $data['no_telepon'] = $no_telepon;
        $data['steam_hex'] = $steam_hex;
        $data['id_user'] = $id_user;
        $data['username'] = $username;
      } else {
        $pesan = "Gagal memperbarui profil.";
      }
      mysqli_stmt_close($stmt);
    } else {
      $pesan = "Gagal mempersiapkan query.";
    }
  }

  if (isset($_POST['reset_password'])) {
    // Reset password
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konf = $_POST['password_konf'];

    // Cek password lama cocok?
    if (password_verify($password_lama, $data['password'])) {
      // Cek konfirmasi password baru
      if ($password_baru === $password_konf) {
        $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $hash_baru, $user_id);
        if (mysqli_stmt_execute($stmt)) {
          $pesan = "Password berhasil diubah.";
        } else {
          $error_password = "Gagal mengubah password.";
        }
        mysqli_stmt_close($stmt);
      } else {
        $error_password = "Password baru dan konfirmasi tidak cocok.";
      }
    } else {
      $error_password = "Password lama salah.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Absensi Paramedis - RSCH</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f9fc;
    }
    .sidebar {
      width: 250px;
      background: rgb(3, 32, 82);
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      padding: 20px;
      color: white;
    }
    .sidebar h1 {
      font-size: 24px;
      margin-bottom: 20px;
    }
    .sidebar nav a {
      display: block;
      color: white;
      text-decoration: none;
      margin-bottom: 10px;
      padding: 10px;
      border-radius: 4px;
    }
    .sidebar nav a:hover,
    .sidebar nav a.active {
      background:rgb(85, 158, 221);
    }
    .main {
      margin-left: 250px;
      padding: 40px 60px;
      background-color: #f5f9fc;
      min-height: 100vh;
      max-width: 800px;
    }
    h2 {
      color:rgb(3, 32, 83);
      margin-bottom: 10px;
    }
    form {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-bottom: 40px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px 30px;
    }
    label {
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
      color: #333;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
      margin-bottom: 12px;
    }
    .full-width {
      grid-column: span 2;
    }
    button {
      background:rgb(3, 32, 83);
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
    }
    button:hover {
      background:rgb(85, 158, 221);
    }
    .message {
      color: green;
      margin-top: 10px;
      font-weight: 600;
      text-align: center;
    }
    .error {
      color: red;
      margin-top: 10px;
      font-weight: 600;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h1>RSCH - Paramedis</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="input_absensi.php">Input Absensi</a>
    <a href="riwayat_absensi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'riwayat_absensi.php' ? 'active' : '' ?>">Riwayat Absensi</a>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="profile.php" class="active">Edit Profil</a>
    <a href="../logout.php">Logout</a>
  </nav>
</div>

<div class="main">
  <!-- Form Edit Profile -->
  <form method="POST" action="">
    <h2>Edit Profil</h2>
    <div class="form-grid">
      <div>
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama'] ?? '') ?>" required>
      </div>
      <div>
        <label>Nomor Telepon</label>
        <input type="text" name="no_telepon" value="<?= htmlspecialchars($data['no_telepon'] ?? '') ?>">
      </div>
      <div>
        <label>Steam Hex</label>
        <input type="text" name="steam_hex" value="<?= htmlspecialchars($data['steam_hex'] ?? '') ?>">
      </div>
      <div>
        <label>ID Petugas</label>
        <input type="text" name="id_petugas" value="<?= htmlspecialchars($data['id_petugas'] ?? '') ?>">
      </div>
      <div class="full-width">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($data['username'] ?? '') ?>" required>
      </div>
      <div class="full-width">
        <button type="submit" name="update_profile">Simpan Perubahan</button>
      </div>
    </div>
    <?php if ($pesan && isset($_POST['update_profile'])): ?>
      <p class="message"><?= htmlspecialchars($pesan) ?></p>
    <?php endif; ?>
  </form>

  <!-- Form Reset Password -->
  <form method="POST" action="">
    <h2>Reset Password</h2>
    <div class="form-grid">
      <div class="full-width">
        <label>Password Lama</label>
        <input type="password" name="password_lama" required>
      </div>
      <div>
        <label>Password Baru</label>
        <input type="password" name="password_baru" required>
      </div>
      <div>
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="password_konf" required>
      </div>
      <div class="full-width">
        <button type="submit" name="reset_password">Ganti Password</button>
      </div>
    </div>
    <?php if ($error_password): ?>
      <p class="error"><?= htmlspecialchars($error_password) ?></p>
    <?php elseif ($pesan && isset($_POST['reset_password'])): ?>
      <p class="message"><?= htmlspecialchars($pesan) ?></p>
    <?php endif; ?>
  </form>
</div>

</body>
</html>
