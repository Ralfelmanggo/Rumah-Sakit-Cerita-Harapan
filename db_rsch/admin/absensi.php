<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');
$bulan = date('m');
$tahun = date('Y');

// Proses reset jika tombol ditekan
$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_bulan'])) {
    $stmt_reset = mysqli_prepare($koneksi, "DELETE FROM absensi WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
    mysqli_stmt_bind_param($stmt_reset, "ss", $bulan, $tahun);
    if (mysqli_stmt_execute($stmt_reset)) {
        $pesan = "Data absensi bulan ini berhasil direset.";
    } else {
        $pesan = "Gagal mereset data: " . mysqli_error($koneksi);
    }
    mysqli_stmt_close($stmt_reset);
}

// Pencarian dan pagination
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : "";
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$like = "%{$cari}%";

// Hitung total baris
$stmt_count = mysqli_prepare($koneksi, "SELECT COUNT(DISTINCT u.id) as total FROM users u LEFT JOIN absensi a ON u.id = a.id_user AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ? WHERE u.role = 'paramedis' AND u.username LIKE ?");
mysqli_stmt_bind_param($stmt_count, "sss", $bulan, $tahun, $like);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$total_rows = mysqli_fetch_assoc($result_count)['total'];
$total_pages = ceil($total_rows / $limit);
mysqli_stmt_close($stmt_count);

// Ambil data absensi per user
$sql_total = "SELECT u.nama, COUNT(a.id) AS total_shift, SUM(a.total_jam) AS total_jam
              FROM users u
              LEFT JOIN absensi a ON u.id = a.id_user AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
              WHERE u.role = 'paramedis' AND u.nama LIKE ?
              GROUP BY u.id
              ORDER BY total_jam DESC
              LIMIT ? OFFSET ?";
$stmt_total = mysqli_prepare($koneksi, $sql_total);
mysqli_stmt_bind_param($stmt_total, "sssii", $bulan, $tahun, $like, $limit, $offset);
mysqli_stmt_execute($stmt_total);
$result_total = mysqli_stmt_get_result($stmt_total);
$jam_kerja = [];
while ($row = mysqli_fetch_assoc($result_total)) {
    $jam_kerja[] = $row;
}
mysqli_stmt_close($stmt_total);
function formatJamMenit($decimalJam) {
    $jam = floor($decimalJam);
    $menit = round(($decimalJam - $jam) * 60);
    return sprintf("%02d jam %02d menit", $jam, $menit);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Absensi Petugas - RSCH</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f2f7fb;
        }
        .sidebar {
            width: 240px;
            background: rgb(3, 32, 82);
            height: 100vh;
            position: fixed;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgb(85, 158, 221);
        }
        .main {
            margin-left: 240px;
            padding: 40px 60px;
        }
        h1 {
            color: rgb(3, 32, 82);
            margin-bottom: 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }
        .card h3 {
            margin-top: 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f1f4f8;
        }
        .btn-reset {
            background: #e53935;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-reset:hover {
            background: #c62828;
        }
        .pesan {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .btn-export {
            background: #2196F3;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        .btn-export:hover {
            background: #1976D2;
        }
        form.search-form {
            margin-bottom: 15px;
        }
        form.search-form input {
            padding: 8px;
            width: 250px;
        }
        .pagination {
            margin-top: 10px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            background: rgb(3, 32, 82);
            color: white;
            border-radius: 6px;
        }
        .pagination a.active {
            background: rgb(85, 158, 221);
        }
    </style>
</head>
<body>

<div class="sidebar">
  <h2>RSCH - Admin</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
        <a href="petugas.php">Data Petugas</a>
        <a href="gaji.php">Data Gaji Petugas</a>
        <a href="input_bonus.php">Input Bonus</a>
        <a href="daftar_bonus.php">Daftar Bonus Petugas</a>
        <a href="pengaturan_bonus.php">Adjust Bonus</a>
        <a href="absensi.php"class="active">Data Absensi</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="profile.php">Edit Profil</a>
        <a href="../logout.php">Logout</a>
  </nav>
    </div>

<div class="main">
    <h1>Data Absensi</h1>
    <?php if ($pesan): ?>
        <div class="pesan"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <div class="card">
        <h3>Total Jam & Shift Kerja Petugas - Bulan <?= date('F Y') ?></h3>
        <form class="search-form" method="get">
            <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari nama petugas...">
        </form>
        <a href="export_absensi.php" class="btn-export">Export ke Excel</a>
        <table>
            <thead>
                <tr>
                    <th>Nama Petugas</th>
                    <th>Total Jam Kerja</th>
                    <th>Total Shift</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jam_kerja)): ?>
                    <tr><td colspan="3" style="text-align:center; padding:20px;">Tidak ada data absensi bulan ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($jam_kerja as $data): ?>
                        <tr>
                            <td><?= htmlspecialchars($data['nama']) ?></td>
                            <td><?= formatJamMenit($data['total_jam'] ?? 0) ?></td>
                            <td><?= $data['total_shift'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?cari=<?= urlencode($cari) ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <div class="card">
        <h3>Reset Absensi Bulanan</h3>
        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi bulan ini?');">
            <button type="submit" name="reset_bulan" class="btn-reset">Reset Absensi Bulan Ini</button>
        </form>
    </div>
</div>

</body>
</html>
