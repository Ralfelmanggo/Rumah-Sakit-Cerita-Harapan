<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'paramedis') {
    header("Location: ../login.php");
    exit;
}

date_default_timezone_set("Asia/Jakarta");

$user_id = $_SESSION['user_id'];
$tanggal_hari_ini = date('Y-m-d');
$pesan = '';
$shift_selected = $_POST['shift'] ?? 1;

// Jika submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['shift'])) {
    $action = $_POST['action'];
    $shift = intval($_POST['shift']);
    
    if ($shift < 1 || $shift > 5) {
        $pesan = "Shift tidak valid.";
    } else {
        if ($action === 'onduty') {
            $jam_masuk = date('H:i:s');
            $keterangan = 'On Duty';

            // Cek apakah On Duty sudah ada untuk user, tanggal dan shift ini
            $cek = mysqli_prepare($koneksi, "SELECT id FROM absensi WHERE id_user = ? AND tanggal = ? AND shift = ?");
            mysqli_stmt_bind_param($cek, "isi", $user_id, $tanggal_hari_ini, $shift);
            mysqli_stmt_execute($cek);
            mysqli_stmt_store_result($cek);

            if (mysqli_stmt_num_rows($cek) > 0) {
                $pesan = "Anda sudah On Duty untuk shift $shift hari ini.";
            } else {
                // Insert On Duty
                $insert = mysqli_prepare($koneksi, "INSERT INTO absensi (id_user, tanggal, shift, jam_masuk, keterangan) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($insert, "isiss", $user_id, $tanggal_hari_ini, $shift, $jam_masuk, $keterangan);
                if (mysqli_stmt_execute($insert)) {
                    $pesan = "On Duty berhasil pada jam $jam_masuk untuk shift $shift.";
                } else {
                    $pesan = "Gagal menyimpan On Duty: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($insert);
            }
            mysqli_stmt_close($cek);

        } elseif ($action === 'offduty') {
            $jam_keluar = date('H:i:s');
            $keterangan = 'Off Duty';

            // Cari absensi On Duty yang belum Off Duty
            $query = mysqli_prepare($koneksi, "SELECT id, jam_masuk FROM absensi WHERE id_user = ? AND tanggal = ? AND shift = ? AND jam_keluar IS NULL");
            mysqli_stmt_bind_param($query, "isi", $user_id, $tanggal_hari_ini, $shift);
            mysqli_stmt_execute($query);
            mysqli_stmt_bind_result($query, $absensi_id, $jam_masuk_db);
            mysqli_stmt_store_result($query);

            if (mysqli_stmt_num_rows($query) > 0) {
                mysqli_stmt_fetch($query);

                $start = strtotime($jam_masuk_db);
                $end = strtotime($jam_keluar);
                $total_jam = round(($end - $start) / 3600, 2);

                $update = mysqli_prepare($koneksi, "UPDATE absensi SET jam_keluar = ?, total_jam = ?, keterangan = ? WHERE id = ?");
                mysqli_stmt_bind_param($update, "sdsi", $jam_keluar, $total_jam, $keterangan, $absensi_id);
                if (mysqli_stmt_execute($update)) {
                    $pesan = "Off Duty berhasil pada jam $jam_keluar. Total jam kerja: $total_jam jam untuk shift $shift.";
                } else {
                    $pesan = "Gagal update Off Duty: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($update);
            } else {
                $pesan = "Tidak ditemukan data On Duty yang aktif untuk shift $shift. Silakan On Duty terlebih dahulu.";
            }
            mysqli_stmt_close($query);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Input Absensi Paramedis - RSCH</title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f5f9fc;
    margin: 0;
}
.sidebar {
    width: 250px;
    background: rgb(3, 32, 82);
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
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
    margin-left: 300px;
    padding: 40px;
    min-height: 100vh;
}
h1 {
    color: rgb(3, 32, 82);
    margin-bottom: 20px;
}
.absensi-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}
.btn {
    padding: 10px 30px;
    margin: 10px;
    font-weight: bold;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    background-color: rgb(3, 32, 82);
    color: white;
}
.btn:hover {
    background-color: rgb(85, 158, 221);
}
.pesan {
    margin-top: 20px;
    font-weight: 600;
    color: #333;
}
select {
    padding: 8px 12px;
    font-size: 1rem;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="sidebar">
  <h2>RSCH - Paramedis</h2>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="input_absensi.php" class="active">Input Absensi</a>
    <a href="riwayat_absensi.php">Riwayat Absensi</a>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="profile.php">Edit Profil</a>
    <a href="../logout.php">Logout</a>
  </nav>
</div>

<div class="main">
    <h1>Input Absensi Petugas</h1>
    <div class="absensi-card">
        <form method="post" autocomplete="off">
            <label for="shift">Pilih Shift:</label><br />
            <select name="shift" id="shift" required>
                <option value="1" <?= $shift_selected == 1 ? 'selected' : '' ?>>Shift 1</option>
                <option value="2" <?= $shift_selected == 2 ? 'selected' : '' ?>>Shift 2</option>
                <option value="3" <?= $shift_selected == 3 ? 'selected' : '' ?>>Shift 3</option>
                <option value="4" <?= $shift_selected == 4 ? 'selected' : '' ?>>Shift 4</option>
                <option value="5" <?= $shift_selected == 5 ? 'selected' : '' ?>>Shift 5</option>
            </select><br />
            <button type="submit" name="action" value="onduty" class="btn">On Duty</button>
            <button type="submit" name="action" value="offduty" class="btn">Off Duty</button>
        </form>
        <?php if ($pesan): ?>
            <p class="pesan"><?= htmlspecialchars($pesan) ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
