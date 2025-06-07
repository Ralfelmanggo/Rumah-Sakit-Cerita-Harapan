<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../includes/index.php");
    exit;
}

include '../includes/koneksi.php';

// Aktifkan debug untuk menangani error query (sementara)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET['id']) || !isset($_GET['bulan']) || !isset($_GET['tahun'])) {
    die("Data tidak lengkap.");
}

$id_user = intval($_GET['id']);
$bulan = intval($_GET['bulan']);
$tahun = intval($_GET['tahun']);

// Tarif gaji per jam berdasarkan jabatan
$tarif_per_jam = [
    'CEO' => 150000,
    'Direksi' => 100000,
    'Staff' => 80000,
    'Paramedis' => 50000,
];

// Ambil info user
$user_q = $koneksi->prepare("SELECT nama, jabatan FROM users WHERE id = ?");
$user_q->bind_param("i", $id_user);
$user_q->execute();
$user = $user_q->get_result()->fetch_assoc();

if (!$user) {
    die("User tidak ditemukan.");
}

// Hitung total jam kerja
$jam_q = $koneksi->prepare("
    SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)), 0) AS total_menit
    FROM absensi
    WHERE id_user = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?
");
$jam_q->bind_param("iii", $id_user, $bulan, $tahun);
$jam_q->execute();
$total_menit = $jam_q->get_result()->fetch_assoc()['total_menit'];
$total_jam = round($total_menit / 60, 2);

// Hitung gaji pokok
$jabatan = $user['jabatan'];
$tarif = $tarif_per_jam[$jabatan] ?? 0;
$gaji_pokok = $total_jam * $tarif;

// Ambil bonus
$bonus_q = $koneksi->prepare("
    SELECT 
        IFNULL(SUM(bonus_operasi),0) AS bonus_operasi,
        IFNULL(SUM(bonus_farmasi),0) AS bonus_farmasi,
        IFNULL(SUM(bonus_praktek_spesialis),0) AS bonus_praktek_spesialis,
        IFNULL(SUM(bonus_asisten_dokter),0) AS bonus_asisten_dokter
    FROM bonus 
    WHERE user_id = ? AND bulan = ? AND tahun = ?
");
$bonus_q->bind_param("iii", $id_user, $bulan, $tahun);
$bonus_q->execute();
$bonus = $bonus_q->get_result()->fetch_assoc();
$total_bonus = array_sum($bonus);
$total_gaji = $gaji_pokok + $total_bonus;

// Format bulan
$nama_bulan = date('F', mktime(0, 0, 0, $bulan, 10));
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Nota Gaji <?= htmlspecialchars($user['nama']) ?></title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f3f9ff;
      padding: 40px;
      color: #333;
    }
    .nota-container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 12px rgba(0,0,0,0.1);
      max-width: 700px;
      margin: auto;
    }
    h1, h2 {
      text-align: center;
      color: rgb(3, 32, 82);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
    }
    th, td {
      text-align: left;
      padding: 8px 10px;
    }
    th {
      background-color: #f0f4f8;
    }
    .total {
      font-weight: bold;
      color: rgb(3, 32, 82);
    }
    .print-button {
      margin-top: 25px;
      display: flex;
      justify-content: center;
    }
    .print-button button {
      background-color: rgb(3, 32, 82);
      color: white;
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #333;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="nota-container">
  <h1>Nota Gaji Petugas Rumah Sakit Cerita Harapan</h1>
  <h2><?= htmlspecialchars($user['nama']) ?> - <?= $nama_bulan . " " . $tahun ?></h2>

  <table>
    <tr>
      <th>Nama</th>
      <td><?= htmlspecialchars($user['nama']) ?></td>
    </tr>
    <tr>
      <th>Jabatan</th>
      <td><?= htmlspecialchars($jabatan) ?></td>
    </tr>
    <tr>
      <th>Total Jam Kerja</th>
      <td><?= number_format($total_jam, 2) ?> jam</td>
    </tr>
    <tr>
      <th>Tarif / Jam</th>
      <td>Rp <?= number_format($tarif, 0, ',', '.') ?></td>
    </tr>
    <tr>
      <th>Gaji Pokok</th>
      <td>Rp <?= number_format($gaji_pokok, 0, ',', '.') ?></td>
    </tr>
    <tr>
      <th>Bonus Operasi</th>
      <td>Rp <?= number_format($bonus['bonus_operasi'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <th>Bonus Farmasi</th>
      <td>Rp <?= number_format($bonus['bonus_farmasi'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <th>Bonus Praktek Spesialis</th>
      <td>Rp <?= number_format($bonus['bonus_praktek_spesialis'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <th>Bonus Asisten Dokter</th>
      <td>Rp <?= number_format($bonus['bonus_asisten_dokter'], 0, ',', '.') ?></td>
    </tr>
    <tr class="total">
      <th>Total Bonus</th>
      <td>Rp <?= number_format($total_bonus, 0, ',', '.') ?></td>
    </tr>
    <tr class="total">
      <th>Total Gaji</th>
      <td><strong>Rp <?= number_format($total_gaji, 0, ',', '.') ?></strong></td>
    </tr>
  </table>

  <div class="print-button">
    <button onclick="window.print()">Cetak Nota</button>
  </div>

  <a href="gaji.php" class="back-link">← Kembali ke Halaman Gaji</a>
</div>

</body>
</html>
