<?php
// register_admin.php
include 'koneksi.php'; // koneksi database

// Data admin baru
$username = 'admin';
$password_plain = 'admin123'; // Password asli
$nama_lengkap = 'Administrator RSCH';
$role = 'admin';

// Generate hash password
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// Cek apakah username sudah ada
$stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "Username sudah terdaftar!";
} else {
    mysqli_stmt_close($stmt);

    // Insert data admin baru
    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hashed, $nama_lengkap, $role);

    if (mysqli_stmt_execute($stmt)) {
        echo "Admin berhasil didaftarkan!";
    } else {
        echo "Gagal mendaftarkan admin: " . mysqli_error($koneksi);
    }
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
