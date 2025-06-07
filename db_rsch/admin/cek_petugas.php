<?php
include 'includes/koneksi.php';  // Pastikan path sesuai

$result = mysqli_query($koneksi, "SELECT * FROM petugas LIMIT 20");

if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

echo "<h2>Daftar Petugas (max 20)</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>Nama</th><th>Jabatan</th><th>No Telepon</th><th>Role</th><th>Username</th><th>Created At</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
    echo "<td>" . htmlspecialchars($row['jabatan']) . "</td>";
    echo "<td>" . htmlspecialchars($row['no_telepon']) . "</td>";
    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
