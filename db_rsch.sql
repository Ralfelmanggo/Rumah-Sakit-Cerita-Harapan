-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jun 2025 pada 10.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_rsch`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `shift` int(4) NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `total_jam` float(4,2) DEFAULT 0.00,
  `keterangan` varchar(50) DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `id_user`, `tanggal`, `shift`, `jam_masuk`, `jam_keluar`, `total_jam`, `keterangan`) VALUES
(51, 3, '2025-06-07', 1, '12:03:05', '12:03:40', 0.01, 'Off Duty'),
(52, 3, '2025-06-07', 2, '12:27:28', '13:53:04', 1.43, 'Off Duty');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bonus`
--

CREATE TABLE `bonus` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `bonus_operasi` int(11) DEFAULT 0,
  `bonus_farmasi` int(11) DEFAULT 0,
  `bonus_praktek_spesialis` int(11) DEFAULT 0,
  `bonus_asisten_dokter` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bonus_petugas`
--

CREATE TABLE `bonus_petugas` (
  `id` int(11) NOT NULL,
  `petugas_id` int(11) NOT NULL,
  `jenis_bonus` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bonus_petugas`
--

INSERT INTO `bonus_petugas` (`id`, `petugas_id`, `jenis_bonus`, `jumlah`, `bulan`, `tahun`) VALUES
(17, 3, 'bonus_operasi', 500000, 6, 2025),
(18, 3, 'bonus_farmasi', 75000, 6, 2025),
(19, 3, 'bonus_praktek_spesialis', 250000, 6, 2025),
(20, 3, 'bonus_asisten_dokter', 50000, 6, 2025),
(21, 4, 'bonus_operasi', 0, 6, 2025),
(22, 4, 'bonus_farmasi', 0, 6, 2025),
(23, 4, 'bonus_praktek_spesialis', 0, 6, 2025),
(24, 4, 'bonus_asisten_dokter', 0, 6, 2025);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bonus_setting`
--

CREATE TABLE `bonus_setting` (
  `id` int(11) NOT NULL,
  `jenis_bonus` varchar(50) NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bonus_setting`
--

INSERT INTO `bonus_setting` (`id`, `jenis_bonus`, `nominal`) VALUES
(1, 'bonus_operasi', 100000),
(2, 'bonus_farmasi', 75000),
(3, 'bonus_praktek_spesialis', 125000),
(4, 'bonus_asisten_dokter', 60000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `gaji_setting`
--

CREATE TABLE `gaji_setting` (
  `id` int(11) NOT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `tarif_per_jam` int(11) DEFAULT NULL,
  `bonus` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_gaji`
--

CREATE TABLE `riwayat_gaji` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `total_jam` decimal(10,2) NOT NULL,
  `tarif_per_jam` int(11) NOT NULL,
  `gaji_pokok` bigint(20) NOT NULL,
  `bonus_operasi` bigint(20) NOT NULL,
  `bonus_farmasi` bigint(20) NOT NULL,
  `bonus_praktek_spesialis` bigint(20) NOT NULL,
  `bonus_asisten_dokter` bigint(20) NOT NULL,
  `total_bonus` bigint(20) NOT NULL,
  `total_gaji` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `role` enum('admin','paramedis','superuser') NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `steam_hex` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_petugas` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `jabatan`, `role`, `no_telepon`, `steam_hex`, `created_at`, `id_petugas`) VALUES
(1, 'admin', '$2y$10$XAraqcRHP57XpnVmszM4GeHaCwGVGhTMF3/.gufYYc2I6vulp9m4S', 'Administrator RSCH', 'Management', 'admin', '-', '-', '2025-06-06 10:22:28', '-'),
(3, 'Rafael', '$2y$10$MzMmvT273NCV7gYfd8QvyeHlG8qKAub9.XqaiRSNKbUuPWfex9VGW', 'Rafael Luciano Esquivel', 'CEO', 'paramedis', '75', 'steam:11000014f83f39b', '2025-06-06 11:37:29', 'MED01'),
(4, 'Zellina', '$2y$10$4g1Mab/30dLt6j/2j0JXq.IPF7O7o2iodwqUH7zhQ7iAOlh7AY8eW', 'Zellina', 'Sekben', 'paramedis', '', '', '2025-06-06 16:54:17', 'MED02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_shift` (`id_user`,`tanggal`,`shift`);

--
-- Indeks untuk tabel `bonus`
--
ALTER TABLE `bonus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `bonus_petugas`
--
ALTER TABLE `bonus_petugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `petugas_id` (`petugas_id`);

--
-- Indeks untuk tabel `bonus_setting`
--
ALTER TABLE `bonus_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gaji_setting`
--
ALTER TABLE `gaji_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `riwayat_gaji`
--
ALTER TABLE `riwayat_gaji`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_bulan_tahun` (`user_id`,`bulan`,`tahun`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `bonus`
--
ALTER TABLE `bonus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `bonus_petugas`
--
ALTER TABLE `bonus_petugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `bonus_setting`
--
ALTER TABLE `bonus_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `gaji_setting`
--
ALTER TABLE `gaji_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `riwayat_gaji`
--
ALTER TABLE `riwayat_gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_fk_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bonus`
--
ALTER TABLE `bonus`
  ADD CONSTRAINT `bonus_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `bonus_petugas`
--
ALTER TABLE `bonus_petugas`
  ADD CONSTRAINT `bonus_petugas_ibfk_1` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
