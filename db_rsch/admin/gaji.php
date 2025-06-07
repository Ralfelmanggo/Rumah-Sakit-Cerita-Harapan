<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../includes/index.php");
    exit;
}

include '../includes/koneksi.php';

// Bulan & Tahun saat ini
$bulan = date('m');
$tahun = date('Y');
if (isset($_GET['bulan']) && isset($_GET['tahun'])) {
    $bulan = intval($_GET['bulan']);
    $tahun = intval($_GET['tahun']);
}

// Tarif gaji per jam berdasarkan jabatan
$tarif_per_jam = [
    'CEO' => 150000,
    'Direksi' => 100000,
    'Staff' => 80000,
    'Paramedis' => 50000,
];

// Ambil data petugas selain admin
$query = "SELECT id, nama, Jabatan FROM users WHERE role != 'admin' ORDER BY nama ASC";
$result = $koneksi->query($query);
$gaji_data = [];

while ($row = $result->fetch_assoc()) {
    $id_user = $row['id'];

    // Total jam kerja bulan ini
    $jam_kerja_query = $koneksi->prepare("
        SELECT IFNULL(SUM(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)), 0) AS total_menit
        FROM absensi
        WHERE id_user = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?
    ");
    if (!$jam_kerja_query) {
        die("Query jam kerja gagal: " . $koneksi->error);
    }
    $jam_kerja_query->bind_param("iii", $id_user, $bulan, $tahun);
    $jam_kerja_query->execute();
    $jam_kerja_res = $jam_kerja_query->get_result()->fetch_assoc();
    $total_menit = $jam_kerja_res['total_menit'];
    $total_jam = round($total_menit / 60, 2);

    // Tarif gaji per jam
    $jabatan_key = strtolower($row['Jabatan']);
    $tarif = $tarif_per_jam[$jabatan_key] ?? 0;
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
    if (!$bonus_q) {
        die("Query bonus gagal: " . $koneksi->error);
    }
    $bonus_q->bind_param("iii", $user_id, $bulan, $tahun);
    $bonus_q->execute();
    $bonus_res = $bonus_q->get_result()->fetch_assoc();

    $total_bonus = $bonus_res['bonus_operasi'] + $bonus_res['bonus_farmasi'] +
                   $bonus_res['bonus_praktek_spesialis'] + $bonus_res['bonus_asisten_dokter'];
    $total_gaji = $gaji_pokok + $total_bonus;

   $gaji_data[] = [
    'id' => $id_user,
    'nama' => $row['nama'],
    'jabatan' => $row['Jabatan'],
    'total_jam' => $total_jam,
    'gaji_pokok' => $gaji_pokok,
    'bonus_operasi' => $bonus_res['bonus_operasi'],
    'bonus_farmasi' => $bonus_res['bonus_farmasi'],
    'bonus_praktek_spesialis' => $bonus_res['bonus_praktek_spesialis'],
    'bonus_asisten_dokter' => $bonus_res['bonus_asisten_dokter'],
    'total_bonus' => $total_bonus,
    'total_gaji' => $total_gaji,
];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Perhitungan Gaji - Admin RSCH</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f9fc;
      padding: 20px;
      margin: 0;
      color: #333;
    }
    
    h1 {
      color: rgb(3, 32, 82);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      margin-top: 20px;
      overflow: hidden;
    }
    th, td {
      padding: 10px 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    thead {
      background-color: rgb(3, 32, 82);
      color: white;
    }
    .btn-nota {
      background-color: rgb(3, 32, 82);
      color: white;
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 4px;
      font-weight: bold;
    }
    form.filter {
      margin-bottom: 20px;
    }
    form select, form button {
      padding: 8px;
      font-size: 1rem;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    form button {
      background-color: rgb(3, 32, 82);
      color: white;
      border: none;
      margin-left: 10px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<h1>Perhitungan Gaji Bulanan</h1>

<form method="GET" class="filter">
  <label for="bulan">Bulan:</label>
  <select id="bulan" name="bulan">
    <?php
      for ($m=1; $m<=12; $m++) {
        $selected = ($bulan == $m) ? "selected" : "";
        echo "<option value='$m' $selected>" . date('F', mktime(0,0,0,$m,10)) . "</option>";
      }
    ?>
  </select>
  <label for="tahun">Tahun:</label>
  <select id="tahun" name="tahun">
    <?php
      $year_start = 2023;
      $year_end = date('Y') + 1;
      for ($y=$year_start; $y<=$year_end; $y++) {
        $selected = ($tahun == $y) ? "selected" : "";
        echo "<option value='$y' $selected>$y</option>";
      }
    ?>
  </select>
  <button type="submit">Tampilkan</button>
</form>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama</th>
      <th>Jabatan</th>
      <th>Total Jam Kerja</th>
      <th>Gaji Pokok</th>
      <th>Bonus Operasi</th>
      <th>Bonus Farmasi</th>
      <th>Bonus Spesialis</th>
      <th>Bonus Asisten</th>
      <th>Total Bonus</th>
      <th>Total Gaji</th>
      <th>Nota</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($gaji_data)): ?>
      <tr><td colspan="12" style="text-align:center; padding:20px;">Tidak ada data gaji untuk periode ini.</td></tr>
    <?php else: ?>
      <?php foreach ($gaji_data as $index => $g): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($g['nama']) ?></td>
          <td><?= htmlspecialchars($g['jabatan']) ?></td>
          <td><?= number_format($g['total_jam'], 2) ?> jam</td>
          <td>Rp <?= number_format($g['gaji_pokok'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($g['bonus_operasi'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($g['bonus_farmasi'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($g['bonus_praktek_spesialis'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($g['bonus_asisten_dokter'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($g['total_bonus'], 0, ',', '.') ?></td>
          <td><strong>Rp <?= number_format($g['total_gaji'], 0, ',', '.') ?></strong></td>
          <td><a href="nota_gaji.php?id=<?= $g['id'] ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" target="_blank" class="btn-nota">Lihat Nota</a></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<a href="Dashboard.php" class="back-link">← Kembali ke Halaman Dashboard</a>
</div>
</body>
</html>
