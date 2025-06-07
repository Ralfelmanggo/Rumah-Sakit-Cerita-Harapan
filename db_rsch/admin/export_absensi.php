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

$sql = "SELECT u.username, SUM(a.total_jam) AS total_jam
        FROM users u
        LEFT JOIN absensi a ON u.id = a.id_user
        WHERE MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
        GROUP BY u.id
        ORDER BY total_jam DESC";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ss", $bulan, $tahun);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Header untuk export CSV yang bisa dibuka Excel
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=absensi_petugas_' . date('Ym') . '.csv');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom CSV
fputcsv($output, ['Nama Petugas', 'Total Jam Kerja']);

// Tulis data
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [$row['username'], number_format($row['total_jam'], 2)]);
}

fclose($output);
exit;
