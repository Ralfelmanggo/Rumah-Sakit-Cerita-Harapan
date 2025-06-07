<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../includes/index.php");
    exit;
}

include '../includes/koneksi.php';

// Hapus bonus jika ada parameter hapus
if (isset($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM bonus WHERE id = $hapus_id");
    $pesan = "Data bonus berhasil dihapus.";
}

// Ambil data bonus
$sql = "SELECT b.id, u.nama, b.bulan, b.tahun, b.bonus_operasi, b.bonus_farmasi, b.bonus_praktek_spesialis, b.bonus_asisten_dokter 
        FROM bonus b
        JOIN users u ON b.user_id = u.id
        ORDER BY b.tahun DESC, b.bulan DESC";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Bonus Gaji</title>
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
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: rgb(3, 32, 82);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #d0d0d0;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f0f8ff;
        }
        .btn {
            padding: 5px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .edit {
            background-color: #2a8dd2;
            color: white;
        }
        .hapus {
            background-color: #d32f2f;
            color: white;
        }
        .message {
            text-align: center;
            color: green;
            margin-top: 10px;
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
        <a href="daftar_bonus.php"class="active">Daftar Bonus Petugas</a>
        <a href="pengaturan_bonus.php">Adjust Bonus</a>
        <a href="absensi.php">Data Absensi</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="profile.php">Edit Profil</a>
        <a href="../logout.php">Logout</a>
  </nav>
    </div>
<div class="container">
    <h2>Daftar Bonus Gaji Petugas</h2>

    <?php if (isset($pesan)): ?>
        <div class="message"><?= $pesan ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nama Petugas</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Operasi</th>
                <th>Farmasi</th>
                <th>Spesialis</th>
                <th>Asisten Dokter</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= date('F', mktime(0, 0, 0, $row['bulan'], 1)) ?></td>
                    <td><?= $row['tahun'] ?></td>
                    <td><?= number_format($row['bonus_operasi']) ?></td>
                    <td><?= number_format($row['bonus_farmasi']) ?></td>
                    <td><?= number_format($row['bonus_praktek_spesialis']) ?></td>
                    <td><?= number_format($row['bonus_asisten_dokter']) ?></td>
                    <td>
                        <a class="btn edit" href="input_bonus.php?edit=<?= $row['id'] ?>">Edit</a>
                        <a class="btn hapus" href="daftar_bonus.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus bonus ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">Belum ada data bonus.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
