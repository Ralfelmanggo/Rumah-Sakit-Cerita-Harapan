<?php
$host = 'localhost';  // atau sesuai XAMPP kamu
$user = 'root';       // username mysql default XAMPP
$pass = '';           // password kosong default XAMPP
$db = 'db_rsch';      // database kamu

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
