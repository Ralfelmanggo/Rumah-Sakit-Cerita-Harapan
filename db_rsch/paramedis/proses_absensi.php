<?php
session_start();
include '../includes/koneksi.php';

$id_petugas = $_POST['id_petugas'];
$shift = $_POST['shift'];
$tanggal = $_POST['tanggal'];
$jam_masuk = date("H:i");

$stmt = $koneksi->prepare("INSERT INTO absensi (id_petugas, tanggal, shift, on_duty, status) VALUES (?, ?, ?, ?, ?)");
$status = "Aktif";
$stmt->bind_param("issss", $id_petugas, $tanggal, $shift, $jam_masuk, $status);

if ($stmt->execute()) {
  header("Location: input_absensi.php?success=1");
} else {
  echo "Gagal mencatat absensi: " . $stmt->error;
}
