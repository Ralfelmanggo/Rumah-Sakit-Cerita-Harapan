<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../includes/index.php");
    exit;
}

include '../includes/koneksi.php';

$pesan = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username   = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jabatan    = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $no_telepon = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $steam_hex  = mysqli_real_escape_string($koneksi, $_POST['steam_hex']);
    $id_petugas = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
    $role       = mysqli_real_escape_string($koneksi, $_POST['role']);

    $query = "INSERT INTO users (nama, username, password, role, no_telepon, steam_hex, id_petugas, jabatan) 
              VALUES ('$nama', '$username', '$password', '$role', '$no_telepon', '$steam_hex', '$id_petugas', '$jabatan')";

    if (mysqli_query($koneksi, $query)) {
        $pesan = "Petugas berhasil ditambahkan.";
    } else {
        $pesan = "Gagal menambahkan petugas: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tambah Petugas Baru - RSCH Admin</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f9fc;
    }
    .sidebar {
      width: 250px;
      background: rgb(3, 32, 82);
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px;
      color: white;
    }
    .sidebar h2 {
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
      background: rgb(85, 158, 221);
    }
    .main {
      margin-left: 250px;
      padding: 40px 60px;
      display: flex;
      justify-content: center;
      background-color: #f5f9fc;
      min-height: 100vh;
    }
    form {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      width: 100%;
      max-width: 800px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    form h2 {
      text-align: center;
      color: rgb(3, 32, 82);
      margin-bottom: 30px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      column-gap: 30px;
      row-gap: 20px;
    }
    label {
      font-weight: 600;
      display: block;
      margin-bottom: 6px;
      color: #333;
    }
    input[type="text"], input[type="password"], select {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .full-width {
      grid-column: span 2;
    }
    button {
      background: rgb(3, 32, 82);
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 10px;
    }
    button:hover {
      background: rgb(86, 161, 207);
    }
    .message {
      margin-top: 10px;
      color: red;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>RSCH - Admin</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="petugas.php" class="active">Data Petugas</a>
    <a href="absensi.php">Absensi</a>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="profile.php">Profil</a>
    <a href="../logout.php">Logout</a>
  </nav>
</div>

<div class="main">
  <form method="POST" action="">
    <h2>Tambah Petugas Baru</h2>
    <div class="form-grid">
      <div>
        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>
      </div>
      <div>
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div>
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div>
        <label>Jabatan</label>
        <input type="text" name="jabatan" required>
      </div>
      <div>
        <label>No. Telepon</label>
        <input type="text" name="no_telepon">
      </div>
      <div>
        <label>Steam Hex</label>
        <input type="text" name="steam_hex">
      </div>
      <div>
        <label>ID Petugas</label>
        <input type="text" name="id_petugas">
      </div>
      <div>
        <label>Role</label>
        <select name="role" required>
          <option value="paramedis">Paramedis</option>
          <option value="admin">Admin</option>
          <option value="superuser">Superuser</option>
        </select>
      </div>
      <div class="full-width">
        <button type="submit">Simpan</button>
      </div>
    </div>

    <?php if ($pesan): ?>
      <p class="message"><?= htmlspecialchars($pesan) ?></p>
    <?php endif; ?>
  </form>
</div>

</body>
</html>
