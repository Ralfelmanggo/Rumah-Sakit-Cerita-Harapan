<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'paramedis') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
date_default_timezone_set("Asia/Jakarta");

// Ambil bulan & tahun sekarang
$bulan = date('m');
$tahun = date('Y');

// Ambil data absensi 1 bulan terakhir
$query = "SELECT * FROM absensi WHERE id_user = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY tanggal DESC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'iii', $user_id, $bulan, $tahun);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Total jam kerja
$total_jam_bulan_ini = 0;
$data_absensi = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data_absensi[] = $row;
    $total_jam_bulan_ini += $row['total_jam'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Riwayat Absensi - RSCH Paramedis</title>
<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f5f9fc;
  margin: 0;
}
.sidebar {
  width: 250px;
  background: #032052;
  height: 100vh;
  position: fixed;
  top: 0; left: 0;
  padding: 20px;
  color: white;
}
.sidebar h2 {
  margin-bottom: 20px;
}
.sidebar nav a {
  display: block;
  color: white;
  text-decoration: none;
  margin-bottom: 10px;
  padding: 10px;
  border-radius: 5px;
}
.sidebar nav a.active, .sidebar nav a:hover {
  background:rgb(85, 158, 221);
}
.main {
  margin-left: 250px;
  padding: 40px 80px;
  min-height: 100vh;
}
header h1 {
  color: #032052;
  margin-bottom: 10px;
}
.table-wrapper {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  max-width: 2000px;
  margin: 0 auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
}
th, td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: center;
}
th {
  background: #032052;
  color: white;
}
tr:nth-child(even) {
  background: #f9f9f9;
}
</style>
<head>
    <meta charset="UTF-8" />
    <title>Riwayat Absensi - RSCH Paramedis</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f9fc; margin: 0; }
        .sidebar { width: 250px; background: #032052; height: 100vh; position: fixed; padding: 20px; color: white; }
        .sidebar h2 { margin-bottom: 20px; }
        .sidebar nav a { display: block; color: white; text-decoration: none; margin-bottom: 10px; padding: 10px; border-radius: 5px; }
        .sidebar nav a.active, .sidebar nav a:hover { background: #559edd; }
        .main { margin-left: 250px; padding: 40px 80px; min-height: 100vh; }
        header h1 { color: #032052; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 0 8px rgba(0,0,0,0.05); }
        th, td { padding: 12px 14px; border-bottom: 1px solid #ddd; text-align: center; }
        th { background: #e3f2fd; color: #032052; }
        .total-box { background: #e0f7f1; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center; font-weight: bold; color: #032052; font-size: 1.2rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>RSCH - Paramedis</h2>
    <nav>
        <a href="dashboard.php">Dashboard</a>
    <a href="input_absensi.php">Input Absensi</a>
    <a href="riwayat_absensi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'riwayat_absensi.php' ? 'active' : '' ?>">Riwayat Absensi</a>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="profile.php">Edit Profil</a>
    <a href="../logout.php">Logout</a>>
    </nav>
</div>

<div class="main">
    <header>
        <h1>Riwayat Absensi Bulan <?= date('F Y') ?></h1>
    </header>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Total Jam</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_absensi as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['shift']) ?></td>
                    <td><?= htmlspecialchars($row['jam_masuk']) ?></td>
                    <td><?= htmlspecialchars($row['jam_keluar']) ?></td>
                    <td><?= htmlspecialchars($row['total_jam']) ?> jam</td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-box">
        Total Jam Kerja Bulan Ini: <?= number_format($total_jam_bulan_ini, 2) ?> Jam
    </div>
</div>

</body>
</html>