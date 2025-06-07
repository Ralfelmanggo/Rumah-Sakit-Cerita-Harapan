<?php
include '../includes/koneksi.php';
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'paramedis') {
    header('Location: ../login.php');
    exit;
}

// Total petugas paramedis
$query_petugas = "SELECT COUNT(*) AS total FROM users WHERE role = 'paramedis'";
$result_petugas = mysqli_query($koneksi, $query_petugas);
if (!$result_petugas) {
    die("Query petugas gagal: " . mysqli_error($koneksi));
}
$data_petugas = mysqli_fetch_assoc($result_petugas);

// Total absensi hari ini
$tanggal_hari_ini = date('Y-m-d');
$query_absensi = "SELECT COUNT(*) AS total FROM absensi WHERE tanggal = '$tanggal_hari_ini'";
$result_absensi = mysqli_query($koneksi, $query_absensi);
if (!$result_absensi) {
    die("Query absensi gagal: " . mysqli_error($koneksi));
}
$data_absensi = mysqli_fetch_assoc($result_absensi);

// Leaderboard petugas teraktif (5 teratas)
$query_leaderboard = "
  SELECT u.nama, COUNT(a.id) AS total_shift
  FROM absensi a
  JOIN users u ON a.id_user = u.id
  GROUP BY a.id_user
  ORDER BY total_shift DESC
  LIMIT 5
";
$result_leaderboard = mysqli_query($koneksi, $query_leaderboard);
if (!$result_leaderboard) {
    die("Query leaderboard gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Paramedis - RSCH</title>
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
    .main {
      margin-left: 250px;
      padding: 60px;
      background-color: #f5f9fc;
      min-height: 200vh;
    }
    h1 {
      color: rgb(3, 32, 82);
      margin-bottom: 20px;
    }
    .card-container {
      display: flex;
      gap: 30px;
      margin-bottom: 40px;
    }
    .card {
      flex: 1;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .card h3 {
      margin: 0 0 10px;
      color: #333;
    }
    .value {
      font-size: 32px;
      font-weight: bold;
      color: rgb(3, 32, 82);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: center;
    }
    table th {
      background: rgb(3, 32, 82);
      color: white;
    }
    table tr:nth-child(even) {
      background-color: #f0f8ff;
    }
    .tanggal-hari-ini {
      margin-bottom: 20px;
      font-weight: 600;
      color: rgb(3, 32, 82);
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>RSCH - Paramedis</h2>
  <nav>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="input_absensi.php">Input Absensi</a>
    <a href="riwayat_absensi.php">Riwayat Absensi</a>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="profile.php">Edit Profil</a>
    <a href="../logout.php">Logout</a>
  </nav>
</div>

<div class="main">
  <h1>Dashboard - Paramedis</h1>
  <div class="tanggal-hari-ini">
    Tanggal hari ini: <?= date('d M Y') ?>
  </div>

  <div class="card-container">
    <div class="card">
      <h3>Total Petugas</h3>
      <div class="value"><?= $data_petugas['total'] ?></div>
    </div>
    <div class="card">
      <h3>Total Absensi Hari Ini</h3>
      <div class="value"><?= $data_absensi['total'] ?></div>
    </div>
  </div>

  <div class="card">
    <
