<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../includes/index.php");
    exit;
}

include '../includes/koneksi.php';

$keyword = '';
if (isset($_GET['cari']) && !empty(trim($_GET['cari']))) {
    $keyword = trim($_GET['cari']);
    $query = "SELECT id, nama, Jabatan, no_telepon, username, steam_hex, role FROM users 
              WHERE role != 'admin' AND 
              (nama LIKE ? OR username LIKE ?)
              ORDER BY id ASC";
    $stmt = $koneksi->prepare($query);
    $likeKeyword = "%$keyword%";
    $stmt->bind_param("ss", $likeKeyword, $likeKeyword);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT id, nama, Jabatan, no_telepon, username, steam_hex, role FROM users WHERE role != 'admin' ORDER BY id ASC";
    $result = mysqli_query($koneksi, $query);
}

$petugasList = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $petugasList[] = $row;
    }
} else {
    die("Query error: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Data Petugas - Admin RSCH</title>
<style>
  * {
    box-sizing: border-box;
    margin: 0; padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  body {
    background: #f5f9fc;
    color: #333;
  }
  .sidebar {
    width: 250px;
    background-color: rgb(3, 32, 81);
    height: 100vh;
    position: fixed;
    top: 0; left: 0;
    padding: 20px;
    color: white;
  }
  .sidebar h2 {
    margin-bottom: 30px;
    font-weight: 700;
    font-size: 24px;
    letter-spacing: 2px;
  }
  .sidebar nav a {
    display: block;
    color: white;
    padding: 12px 15px;
    text-decoration: none;
    margin-bottom: 8px;
    border-radius: 4px;
    transition: background 0.3s;
  }
  .sidebar nav a:hover, .sidebar nav a.active {
    background-color: rgb(85, 158, 221);
  }
  .main-content {
    margin-left: 250px;
    padding: 30px;
  }
  header {
    margin-bottom: 30px;
  }
  header h1 {
    color: rgb(3, 32, 84);
    font-weight: 700;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
  }
  thead {
    background-color: rgb(3, 32, 82);
    color: white;
  }
  thead th {
    padding: 12px;
    text-align: left;
  }
  tbody tr:hover {
    background-color: #f5f5f5;
  }
  tbody td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
  }
  .btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-block;
  }
  .btn-edit {
    background-color: rgb(3, 32, 82);
    color: white;
  }
  .btn-delete {
    background-color: #f44336;
    color: white;
  }
  footer {
    margin-top: 50px;
    text-align: center;
    color: #777;
  }
  form.search-form {
    margin-bottom: 20px;
  }
  form.search-form input[type="text"] {
    padding: 8px;
    width: 250px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 1rem;
  }
  form.search-form button {
    padding: 8px 15px;
    background-color: rgb(3, 32, 82);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    margin-left: 10px;
  }
  form.search-form a.reset-btn {
    background-color: #ccc;
    color: black;
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    margin-left: 10px;
  }
</style>
</head>
<body>

<div class="sidebar">
  <h2>RSCH - Admin</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
        <a href="petugas.php"class="active">Data Petugas</a>
        <a href="gaji.php">Data Gaji Petugas</a>
        <a href="input_bonus.php">Input Bonus</a>
        <a href="daftar_bonus.php">Daftar Bonus Petugas</a>
        <a href="pengaturan_bonus.php">Adjust Bonus</a>
        <a href="absensi.php">Data Absensi</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="profile.php">Edit Profil</a>
        <a href="../logout.php">Logout</a>
  </nav>
</div>

<div class="main-content">
  <header>
    <h1>Data Petugas</h1>
    <p>Kelola data semua petugas di RSCH</p>
  </header>

  <section>
    <a href="tambah_petugas.php" class="btn btn-edit" style="margin-bottom:15px; display:inline-block;">+ Tambah Petugas</a>

    <form method="GET" class="search-form">
      <input type="text" name="cari" placeholder="Cari nama atau username..." value="<?= htmlspecialchars($keyword) ?>" />
      <button type="submit">Cari</button>
      <?php if ($keyword !== ''): ?>
        <a href="petugas.php" class="reset-btn">Reset</a>
      <?php endif; ?>
    </form>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Jabatan</th>
          <th>Nomor Telepon</th>
          <th>Username</th>
          <th>Steam Hex</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($petugasList)): ?>
          <tr><td colspan="8" style="text-align:center; padding:20px;">Belum ada data petugas.</td></tr>
        <?php else: ?>
          <?php foreach ($petugasList as $index => $petugas): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($petugas['nama']) ?></td>
              <td><?= htmlspecialchars($petugas['Jabatan']) ?></td>
              <td><?= htmlspecialchars($petugas['no_telepon']) ?></td>
              <td><?= htmlspecialchars($petugas['username']) ?></td>
              <td><?= htmlspecialchars($petugas['steam_hex']) ?></td>
              <td><?= htmlspecialchars($petugas['role']) ?></td>
              <td>
                <a href="edit_petugas.php?id=<?= $petugas['id'] ?>" class="btn btn-edit">Edit</a>
                <a href="hapus_petugas.php?id=<?= $petugas['id'] ?>" onclick="return confirm('Yakin hapus petugas ini?')" class="btn btn-delete">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <footer>
    &copy; 2025 RSCH - Rumah Sakit Cerita Harapan
  </footer>
</div>

</body>
</html>
