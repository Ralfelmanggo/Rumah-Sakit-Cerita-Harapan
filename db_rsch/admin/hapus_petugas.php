<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

require '../includes/config.php';

$id = $_GET['id'] ?? null;
if ($id) {
    // Bisa tambahkan cek khusus agar admin tidak bisa menghapus diri sendiri misalnya
    $stmt = $pdo->prepare("DELETE FROM petugas WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: petugas.php');
exit;
