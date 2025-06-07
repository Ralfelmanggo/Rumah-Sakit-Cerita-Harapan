<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <h1>Selamat datang, <?= $_SESSION['username']; ?>!</h1>
  <p>Role: <?= $_SESSION['role']; ?></p>
  <a href="logout.php">Logout</a>
</body>
</html>
