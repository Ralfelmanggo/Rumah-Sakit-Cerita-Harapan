<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$bulan = date('m');
$tahun = date('Y');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM absensi WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
    mysqli_stmt_bind_param($stmt, "ii", $bulan, $tahun);
    if (mysqli_stmt_execute($stmt)) {
        echo "Data absensi bulan ini berhasil direset.";
    } else {
        echo "Gagal reset data: " . mysqli_error($koneksi);
    }
    mysqli_stmt_close($stmt);
}
?>

<form method="POST">
    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus semua absensi bulan ini?')">
        Reset Absensi Bulan Ini
    </button>
</form>
