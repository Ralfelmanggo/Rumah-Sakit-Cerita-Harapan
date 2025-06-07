<?php
include 'includes/koneksi.php';

$username = 'admin';
$password_baru = 'admin123'; // password baru yang kamu inginkan

$hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE username = ?");
mysqli_stmt_bind_param($stmt, "ss", $hash_baru, $username);

if (mysqli_stmt_execute($stmt)) {
    echo "Password admin berhasil direset ke 'admin123'.";
} else {
    echo "Gagal reset password admin: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
