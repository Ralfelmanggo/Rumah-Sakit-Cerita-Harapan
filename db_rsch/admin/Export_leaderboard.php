<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

require '../includes/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=leaderboard_rsch_" . date('Y_m') . ".xls");

$bulan = date('m');
$tahun = date('Y');

$sql = "
SELECT u.nama, u.role, SUM(a.total_jam) AS total_jam
FROM users u
LEFT JOIN absensi a 
    ON u.id = a.id_user AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
WHERE u.role = 'paramedis'
GROUP BY u.id
ORDER BY total_jam DESC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('ii', $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();

// Format jam dan menit
function formatJamMenit($totalJam) {
    $jam = floor($totalJam);
    $menit = round(($totalJam - $jam) * 60);
    return "$jam jam $menit menit";
}

echo "<table border='1'>";
echo "<tr><th>No</th><th>Nama</th><th>Jabatan</th><th>Total Jam Kerja</th></tr>";
$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$no}</td>";
    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
    echo "<td>" . formatJamMenit($row['total_jam'] ?? 0) . "</td>";
    echo "</tr>";
    $no++;
}
echo "</table>";
?>
