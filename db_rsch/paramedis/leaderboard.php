<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'paramedis') {
    header("Location: ../index.php");
    exit;
}

require '../includes/koneksi.php';

$bulan = date('m');
$tahun = date('Y');

// Pagination setup
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$count_sql = "
SELECT COUNT(DISTINCT u.id) AS total
FROM users u
LEFT JOIN absensi a 
    ON u.id = a.id_user AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
WHERE u.role = 'paramedis'";
$count_stmt = $koneksi->prepare($count_sql);
$count_stmt->bind_param('ii', $bulan, $tahun);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// Ambil data leaderboard
$sql = "
SELECT u.id, u.nama, u.jabatan, SUM(a.total_jam) AS total_jam
FROM users u
LEFT JOIN absensi a 
    ON u.id = a.id_user AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
WHERE u.role = 'paramedis'
GROUP BY u.id
ORDER BY total_jam DESC
LIMIT ? OFFSET ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('iiii', $bulan, $tahun, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Leaderboard - RSCH</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f9fc;
    }
    .sidebar {
      width: 250px;
      background-color: rgb(3, 32, 81);
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      padding: 20px;
      color: white;
    }
    .sidebar h2 {
      margin-bottom: 30px;
    }
    .sidebar nav a {
      display: block;
      color: white;
      padding: 12px 15px;
      margin-bottom: 10px;
      border-radius: 5px;
      text-decoration: none;
    }
    .sidebar nav a:hover,
    .sidebar nav a.active {
      background-color: rgb(87, 190, 237);
    }
    .main-content {
      margin-left: 300px;
      padding: 30px;
    }
    header h1 {
      color: rgb(3, 32, 81);
    }
    table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      margin-top: 20px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    thead {
      background-color: rgb(3, 32, 81);
      color: white;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    tbody tr:hover {
      background-color: #f0f0f0;
    }
    .pagination {
      margin-top: 20px;
      text-align: center;
    }
    .pagination a {
      margin: 0 5px;
      text-decoration: none;
      background-color: rgb(3, 32, 81);
      color: white;
      padding: 8px 12px;
      border-radius: 4px;
    }
    .pagination a.active {
      background-color: rgb(87, 190, 237);
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>RSCH - Paramedis</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="input_absensi.php">Input Absensi</a>
    <a href="riwayat_absensi.php">Riwayat Absensi</a>
    <a href="leaderboard.php" class="active">Leaderboard</a>
    <a href="profile.php">Edit Profil</a>
    <a href="../logout.php">Logout</a>
  </nav>
</div>

<div class="main-content">
  <header>
    <h1>Leaderboard Total Jam Bulanan</h1>
    <p>Peringkat tertinggi bulan <?= date('F Y') ?></p>
  </header>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Jabatan</th>
        <th>Total Jam</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows == 0): ?>
        <tr><td colspan="4" style="text-align:center;">Tidak ada data</td></tr>
      <?php else: ?>
        <?php $no = $offset + 1; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            $totalMenit = round(($row['total_jam'] ?? 0) * 60);
            $jam = floor($totalMenit / 60);
            $menit = $totalMenit % 60;
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['jabatan']) ?></td>
            <td><?= $jam ?> jam <?= $menit ?> menit</td>
          </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</div>

</body>
</html>
