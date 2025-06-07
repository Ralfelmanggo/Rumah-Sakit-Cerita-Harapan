<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../includes/index.php");
    exit;
}

include '../includes/koneksi.php';

// Simpan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['nominal'] as $id => $nominal) {
        $nominal = intval($nominal);
        $stmt = $koneksi->prepare("UPDATE bonus_setting SET nominal = ? WHERE id = ?");
        $stmt->bind_param("ii", $nominal, $id);
        $stmt->execute();
    }
    $pesan = "Nominal bonus berhasil diperbarui.";
}

// Ambil data bonus
$data = $koneksi->query("SELECT * FROM bonus_setting ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengaturan Bonus - Admin RSCH</title>
  <link rel="stylesheet" href="../assets/style.css"> <!-- jika Anda punya file CSS umum -->
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
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
    .container {
      margin-left: 300px;
      padding: 30px;
    }

    .card {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 25px;
    }

    h2 {
      margin-bottom: 20px;
      color: rgb(3, 32, 82);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    thead {
      background-color: rgb(3, 32, 82);
      color: white;
    }

    input[type="number"] {
      width: 100%;
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    button {
      background-color: rgb(3, 32, 82);
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
    }

    button:hover {
      background-color: #021c4c;
    }

    .pesan {
      color: green;
      margin-bottom: 15px;
    }

    .back-link {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: rgb(3, 32, 82);
      font-weight: bold;
    }

    .back-link:hover {
      text-decoration: underline;
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
        <a href="input_bonus.php">Input Bonus</a>
        <a href="daftar_bonus.php">Daftar Bonus Petugas</a>
        <a href="pengaturan_bonus.php"class="active">Adjust Bonus</a>
        <a href="absensi.php">Data Absensi</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="profile.php">Edit Profil</a>
        <a href="../logout.php">Logout</a>
  </nav>
</div>

<div class="container">
  <div class="card">
    <h2>Pengaturan Nominal Bonus</h2>
    <?php if (isset($pesan)) echo "<div class='pesan'>$pesan</div>"; ?>
    <form method="post">
      <table>
        <thead>
          <tr>
            <th>Jenis Bonus</th>
            <th>Nominal per Unit (Rp)</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $data->fetch_assoc()): ?>
            <tr>
              <td><?= ucwords(str_replace('_', ' ', $row['jenis_bonus'])) ?></td>
              <td><input type="number" name="nominal[<?= $row['id'] ?>]" value="<?= $row['nominal'] ?>" required></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <button type="submit">Simpan Perubahan</button>
    </form>
    <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
  </div>
</div>

</body>
</html>
