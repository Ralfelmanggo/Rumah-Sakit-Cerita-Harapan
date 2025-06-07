<?php
session_start();
include 'includes/koneksi.php';

if (isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = mysqli_prepare($koneksi, "SELECT id, username, password, role FROM users WHERE username = ?");
if (!$stmt) {
    die("Prepare statement gagal: " . mysqli_error($koneksi));
}
mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $id, $user, $hashed_password, $role);
        mysqli_stmt_fetch($stmt);

        if (password_verify($password, $hashed_password)) {
            // Login sukses
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $user;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: admin/dashboard.php");
            } else if ($role === 'paramedis') {
                header("Location: paramedis/dashboard.php");
            } else {
                // Role lain, redirect ke halaman umum
                header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
        header("Location: login.php");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = "Silakan isi username dan password!";
    header("Location: login.php");
    exit();
}
