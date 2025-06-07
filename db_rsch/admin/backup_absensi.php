<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

require '../includes/config.php';

// Ambil data absensi lengkap
$stmt = $pdo->query("
    SELECT a.*, p.nama AS nama_petugas
    FROM absensi a
    LEFT JOIN petugas p ON a.petugas_id = p.id
    ORDER BY a.tanggal DESC, a.shift
");

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buat header untuk file CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="backup_absensi_' . date('Ymd_His') . '.csv"');

// Buka output stream
$output = fopen('php://output', 'w');

// Header kolom CSV
fputcsv($output, ['ID', 'Nama Petugas', 'Tanggal', 'Hari', 'Shift', 'On Duty', 'Off Duty', 'Total Jam']);

// Tulis setiap baris
foreach ($data as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nama_petugas'],
        $row['tanggal'],
        $row['hari'],
        $row['shift'],
        $row['on_duty'],
        $row['off_duty'],
        $row['total_jam']
    ]);
}

fclose($output);
exit;
