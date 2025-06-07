<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'paramedis') {
    header('Location: ../login.php');
    exit;
}
include_once '/includes/koneksi.php';

$id_petugas = $_SESSION['id_petugas'];

$sql = "SELECT tanggal, shift, on_duty, off_duty, total_jam, keterangan
        FROM absensi
        WHERE id_petugas = ?
        ORDER BY tanggal DESC";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_petugas);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>List Absensi Saya</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
</head>
<body>
<div class="container mt-4">
    <h2>List Absensi Saya</h2>
    <a href="dashboard_paramedis.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>On Duty</th>
                <th>Off Duty</th>
                <th>Total Jam</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['shift']) ?></td>
                <td><?= htmlspecialchars($row['on_duty']) ?></td>
                <td><?= htmlspecialchars($row['off_duty']) ?></td>
                <td><?= htmlspecialchars($row['total_jam']) ?></td>
                <td><?= htmlspecialchars($row['keterangan']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
