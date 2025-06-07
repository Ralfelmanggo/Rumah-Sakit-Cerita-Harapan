<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../includes/index.php");
    exit;
}

include '../includes/koneksi.php';

// Ambil semua user
$users = $koneksi->query("SELECT id, nama FROM users ORDER BY nama");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $bonus_operasi = $_POST['bonus_operasi'];
    $bonus_farmasi = $_POST['bonus_farmasi'];
    $bonus_spesialis = $_POST['bonus_spesialis'];
    $bonus_asisten = $_POST['bonus_asisten'];

    // Cek apakah bonus untuk user/bulan/tahun sudah ada
    $cek = $koneksi->prepare("SELECT id FROM bonus WHERE user_id=? AND bulan=? AND tahun=?");
    $cek->bind_param("iii", $user_id, $bulan, $tahun);
    $cek->execute();
    $cek_result = $cek->get_result();

    if ($cek_result->num_rows > 0) {
        // Update bonus
        $update = $koneksi->prepare("UPDATE bonus SET bonus_operasi=?, bonus_farmasi=?, bonus_praktek_spesialis=?, bonus_asisten_dokter=? WHERE user_id=? AND bulan=? AND tahun=?");
        $update->bind_param("iiiiiii", $bonus_operasi, $bonus_farmasi, $bonus_spesialis, $bonus_asisten, $user_id, $bulan, $tahun);
        $update->execute();
        $message = "Bonus berhasil diperbarui.";
    } else {
        // Insert bonus
        $insert = $koneksi->prepare("INSERT INTO bonus (user_id, bulan, tahun, bonus_operasi, bonus_farmasi, bonus_praktek_spesialis, bonus_asisten_dokter) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("iiiiiii", $user_id, $bulan, $tahun, $bonus_operasi, $bonus_farmasi, $bonus_spesialis, $bonus_asisten);
        $insert->execute();
        $message = "Bonus berhasil ditambahkan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Bonus Gaji</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #eef6ff;
      padding: 40px;
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
    .sidebar nav a {
      display: block;
      color: white;
      text-decoration: none;
      margin-bottom: 12px;
      padding: 10px;
      border-radius: 5px;
    }
    .sidebar nav a.active,
    .sidebar nav a:hover {
      background: rgb(85, 158, 221);
    }
    .form-container {
      background: white;
      max-width: 600px;
      margin: auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: rgb(3, 32, 82);
    }
    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      background-color: rgb(3, 32, 82);
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }
    .message {
      text-align: center;
      margin-top: 15px;
      color: green;
    }
  </style>
</head>
<body>
<div class="sidebar">
  <h1>RSCH - Admin</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
        <a href="petugas.php">Data Petugas</a>
        <a href="gaji.php">Data Gaji Petugas</a>
        <a href="input_bonus.php"class="active">Input Bonus</a>
        <a href="daftar_bonus.php">Daftar Bonus Petugas</a>
        <a href="pengaturan_bonus.php">Adjust Bonus</a>
        <a href="absensi.php">Data Absensi</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="profile.php">Edit Profil</a>
        <a href="../logout.php">Logout</a>
  </nav>
</div>
<div class="form-container">
  <h2>Input / Edit Bonus Petugas</h2>

  <?php if (isset($message)): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Nama Petugas</label>
    <select name="user_id" required>
      <option value="">-- Pilih Petugas --</option>
      <?php while ($row = $users->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Bulan</label>
    <select name="bulan" required>
      <?php
      for ($b = 1; $b <= 12; $b++) {
          echo "<option value='$b'>" . date('F', mktime(0, 0, 0, $b, 1)) . "</option>";
      }
      ?>
    </select>

    <label>Tahun</label>
    <input type="number" name="tahun" value="<?= date('Y') ?>" required>

    <label>Bonus Operasi</label>
    <input type="number" name="bonus_operasi" value="0">

    <label>Bonus Farmasi</label>
    <input type="number" name="bonus_farmasi" value="0">

    <label>Bonus Praktek Spesialis</label>
    <input type="number" name="bonus_spesialis" value="0">

    <label>Bonus Asisten Dokter</label>
    <input type="number" name="bonus_asisten" value="0">

    <button type="submit">Simpan Bonus</button>
  </form>
</div>
<a href="Dashboard.php" class="back-link">← Kembali ke Halaman Dashboard</a>
</div>
</body>
</html>
