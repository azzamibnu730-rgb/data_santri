-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Okt 2026 pada 03.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_almahir`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_buku`
--

CREATE TABLE `tb_buku` (
  `isbn` int(20) NOT NULL,
  `judul_buku` varchar(150) NOT NULL,
  `kategori_buku` varchar(50) NOT NULL,
  `pengarang_buku` varchar(100) NOT NULL,
  `pembuat_buku` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_buku`
--

INSERT INTO `tb_buku` (`isbn`, `judul_buku`, `kategori_buku`, `pengarang_buku`, `pembuat_buku`, `harga`) VALUES
(1, 'Ilmu Pengetahuan Alam Kelas 7', 'IPA', 'Agus Sutrisno', 'Erlangga', 65000),
(2, 'Biologi Dasar', 'IPA', 'Rina Wulandari', 'Gramedia', 72000),
(3, 'Fisika untuk SMP', 'IPA', 'Budi Santoso', 'Yudhistira', 68000),
(4, 'Kimia Dasar', 'IPA', 'Dewi Lestari', 'Erlangga', 75000),
(5, 'Ilmu Pengetahuan Sosial Kelas 7', 'IPS', 'Hendra Wijaya', 'Erlangga', 62000),
(6, 'Geografi Indonesia', 'IPS', 'Siti Nurhaliza', 'Gramedia', 70000),
(7, 'Ekonomi Dasar', 'IPS', 'Ahmad Fauzi', 'Yudhistira', 64000),
(8, 'Sosiologi Remaja', 'IPS', 'Lina Marlina', 'Tiga Serangkai', 66000),
(9, 'Matematika Kelas 7', 'Matematika', 'Joko Prasetyo', 'Erlangga', 70000),
(10, 'Matematika Kelas 8', 'Matematika', 'Joko Prasetyo', 'Erlangga', 72000),
(11, 'Aljabar Mudah', 'Matematika', 'Rudi Hartono', 'Yudhistira', 60000),
(12, 'Geometri Dasar', 'Matematika', 'Ani Rahmawati', 'Tiga Serangkai', 63000),
(13, 'Bahasa Indonesia Kelas 7', 'Bahasa', 'Mulyani', 'Erlangga', 58000),
(14, 'Tata Bahasa Indonesia', 'Bahasa', 'Sri Wahyuni', 'Balai Pustaka', 55000),
(15, 'Bahasa Arab Jilid 1', 'Bahasa', 'Ustadz Abdullah', 'Pustaka Al-Kautsar', 60000),
(16, 'Bahasa Arab Jilid 2', 'Bahasa', 'Ustadz Abdullah', 'Pustaka Al-Kautsar', 62000),
(17, 'Bahasa Inggris Dasar', 'Bahasa', 'Dian Permata', 'Gramedia', 67000),
(18, 'Fiqih Ibadah', 'Agama', 'Ustadz Hasan', 'Pustaka Al-Kautsar', 59000),
(19, 'Aqidah Akhlak', 'Agama', 'Ustadz Yusuf', 'Pustaka Imam Syafii', 56000),
(20, 'Tafsir Juz Amma', 'Agama', 'Ustadz Ibrahim', 'Gema Insani', 65000),
(21, 'Hadits Arbain', 'Agama', 'Ustadz Malik', 'Darul Haq', 54000),
(22, 'Tajwid Praktis', 'Agama', 'Ustadzah Aisyah', 'Gema Insani', 50000),
(23, 'Sejarah Kebudayaan Islam', 'Sejarah', 'Zainal Arifin', 'Tiga Serangkai', 68000),
(24, 'Sejarah Indonesia', 'Sejarah', 'Bambang Susilo', 'Balai Pustaka', 70000),
(25, 'Kisah Para Nabi', 'Sejarah', 'Ustadz Rahmat', 'Pustaka Al-Kautsar', 75000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_mapel`
--

CREATE TABLE `tb_mapel` (
  `no_mapel` int(11) NOT NULL,
  `name_mapel` varchar(100) NOT NULL,
  `jp_mapel` int(11) NOT NULL,
  `kategori_mapel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_mapel`
--

INSERT INTO `tb_mapel` (`no_mapel`, `name_mapel`, `jp_mapel`, `kategori_mapel`) VALUES
(1, 'IPA', 4, 'Umum'),
(2, 'IPS', 3, 'Umum'),
(3, 'Matematika', 5, 'Umum'),
(4, 'Bahasa Indonesia', 4, 'Umum'),
(5, 'Bahasa Arab', 4, 'Agama'),
(6, 'Fiqih', 3, 'Agama'),
(7, 'Tahfidz Al-Qur\'an', 6, 'Agama');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_santri`
--

CREATE TABLE `tb_santri` (
  `no_induk` varchar(20) NOT NULL,
  `nama_santri` varchar(50) NOT NULL,
  `kelamin` char(1) NOT NULL DEFAULT 'L',
  `tempat_lahir` varchar(50) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `nama_wali` varchar(50) NOT NULL,
  `telp_wali` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_santri`
--

INSERT INTO `tb_santri` (`no_induk`, `nama_santri`, `kelamin`, `tempat_lahir`, `tgl_lahir`, `alamat`, `no_telp`, `nama_wali`, `telp_wali`) VALUES
('1', 'Ijal', 'L', 'Purwokerto', '2010-05-11', 'Des Bumi sari Kec Natar Kab Lampung Selatan Prov Lampung', '+6287755128131', '-', '+621272481133'),
('2', 'Kaysan', 'L', 'Lumajang', '2010-02-27', 'Des Bumi sari Kec Natar Kab Lampung Selatan Prov Lampung', '+6287755128131', '-', '+621272481133'),
('3', 'Rozan', 'L', 'Sukoharjo', '2011-02-27', 'Des Bumi sari Kec Natar Kab Lampung Selatan Prov Lampung', '+6287755128131', '-', '+621272481133'),
('4', 'Abdussalam Azzam', 'L', 'Lampung', '2011-02-27', 'Des Bumi sari Kec Natar Kab Lampung Selatan Prov Lampung', '+6287755128131', 'Bpk Zulkarnain', '+621272481133'),
('5', 'Subkhi', 'L', 'Berebes', '2011-02-27', 'Des Bumi sari Kec Natar Kab Lampung Selatan Prov Lampung', '+6287755128131', '-', '+621272481133'),
('S006', 'Rafi Abdurrahman', 'L', 'Surakarta', '2010-02-11', 'Jl. Kalilarangan No. 4, Surakarta', '081234560006', 'Abdurrahman Yusuf', '081334560006'),
('S007', 'Nurul Hidayah', 'P', 'Klaten', '2010-06-23', 'Jl. Pandanaran No. 9, Klaten', '081234560007', 'Hidayat Maulana', '081334560007'),
('S008', 'Farhan Maulidi', 'L', 'Sukoharjo', '2009-04-15', 'Jl. Jenderal Sudirman No. 17, Sukoharjo', '081234560008', 'Maulidi Rahmat', '081334560008'),
('S009', 'Alya Nuraini', 'P', 'Boyolali', '2011-01-30', 'Jl. Kenari No. 6, Boyolali', '081234560009', 'Nurdin Saputra', '081334560009'),
('S010', 'Dimas Prasetyo', 'L', 'Karanganyar', '2010-08-05', 'Jl. Kartini No. 12, Karanganyar', '081234560010', 'Prasetyo Widodo', '081334560010'),
('S011', 'Siti Nurhaliza', 'P', 'Wonogiri', '2010-10-19', 'Jl. Diponegoro No. 3, Wonogiri', '081234560011', 'Halim Santoso', '081334560011'),
('S012', 'Reza Fahmi', 'L', 'Sragen', '2009-12-27', 'Jl. Raya Sukowati No. 21, Sragen', '081234560012', 'Fahmi Anwar', '081334560012'),
('S013', 'Dewi Lestari', 'P', 'Surakarta', '2011-03-14', 'Jl. Gajah Mada No. 8, Surakarta', '081234560013', 'Lestari Budiman', '081334560013'),
('S014', 'Hafidz Al Ghifari', 'L', 'Semarang', '2010-05-09', 'Jl. Pemuda No. 29, Semarang', '081234560014', 'Ghifari Hakim', '081334560014'),
('S015', 'Mutiara Zahidah', 'P', 'Salatiga', '2010-09-02', 'Jl. Osamaliki No. 5, Salatiga', '081234560015', 'Zahid Ramli', '081334560015'),
('S016', 'Irfan Hakim', 'L', 'Klaten', '2009-07-21', 'Jl. Merbabu No. 14, Klaten', '081234560016', 'Hakim Subekti', '081334560016'),
('S017', 'Annisa Rahmawati', 'P', 'Sukoharjo', '2011-02-08', 'Jl. Mawar No. 11, Sukoharjo', '081234560017', 'Rahmat Hidayat', '081334560017'),
('S018', 'Bagas Wijaksono', 'L', 'Boyolali', '2010-11-26', 'Jl. Merapi No. 7, Boyolali', '081234560018', 'Wijaksono Hadi', '081334560018'),
('S019', 'Intan Permatasari', 'P', 'Karanganyar', '2010-04-17', 'Jl. Lawu No. 20, Karanganyar', '081234560019', 'Permata Susilo', '081334560019'),
('S020', 'Rizal Ramadhani', 'L', 'Wonogiri', '2009-09-13', 'Jl. Pramuka No. 15, Wonogiri', '081234560020', 'Ramadhani Yusuf', '081334560020');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_ustadz`
--

CREATE TABLE `tb_ustadz` (
  `nip` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nama_panggilan` varchar(50) NOT NULL,
  `kelamin` char(1) NOT NULL DEFAULT 'L',
  `tgl_lahir` date NOT NULL,
  `pdk_terakhir` varchar(60) NOT NULL,
  `bidang_keahlian` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_telp` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_ustadz`
--

INSERT INTO `tb_ustadz` (`nip`, `nama_lengkap`, `nama_panggilan`, `kelamin`, `tgl_lahir`, `pdk_terakhir`, `bidang_keahlian`, `alamat`, `no_telp`, `status`) VALUES
('1', 'Ust. Ahmad Fauzi, S.Pd.I', 'Ust. Fauzi', 'L', '1985-03-12', 'S1 Pendidikan Agama Islam', 'Fiqih, Nahwu', 'Jl. Melati No. 10', '0812-1111-1111', 'Tetap'),
('100007', 'Ahmad Syafi\'i', 'Ust. Syafi\'i', 'L', '1985-03-14', 'S1', 'Fiqih', 'Jl. Slamet Riyadi No. 10, Surakarta', '081210000007', 'Aktif'),
('100008', 'Siti Maryam Hasanah', 'Ustadzah Maryam', 'L', '1990-07-22', 'S1', 'Tahfidz', 'Jl. Melati No. 5, Klaten', '081210000008', 'Aktif'),
('100009', 'Muhammad Abdul Karim', 'Ust. Karim', 'L', '1982-11-05', 'S2', 'Tafsir', 'Jl. Veteran No. 18, Sukoharjo', '081210000009', 'Aktif'),
('100010', 'Nur Fadilah Rahmawati', 'Ustadzah Fadilah', 'L', '1992-01-30', 'S1', 'Bahasa Arab', 'Jl. Merapi No. 3, Boyolali', '081210000010', 'Aktif'),
('100011', 'Hasan Basri Munawar', 'Ust. Basri', 'L', '1979-09-12', 'S2', 'Hadits', 'Jl. Lawu No. 22, Karanganyar', '081210000011', 'Aktif'),
('100012', 'Khadijah Azzahra', 'Ustadzah Khadijah', 'L', '1994-05-19', 'D3', 'Tajwid', 'Jl. Pramuka No. 9, Wonogiri', '081210000012', 'Aktif'),
('100013', 'Yusuf Ibrahim Hakim', 'Ust. Yusuf', 'L', '1988-12-08', 'S1', 'Aqidah Akhlak', 'Jl. Raya Sukowati No. 14, Sragen', '081210000013', 'Aktif'),
('100014', 'Aminah Zulaikha', 'Ustadzah Aminah', 'L', '1991-04-27', 'S1', 'Nahwu Sharaf', 'Jl. Kenari No. 7, Surakarta', '081210000014', 'Cuti'),
('100015', 'Umar Faruq Santoso', 'Ust. Faruq', 'L', '1984-08-16', 'S1', 'Tahfidz', 'Jl. Pemuda No. 21, Semarang', '081210000015', 'Aktif'),
('100016', 'Laila Munawaroh', 'Ustadzah Laila', 'L', '1993-10-03', 'S1', 'Bahasa Inggris', 'Jl. Osamaliki No. 6, Salatiga', '081210000016', 'Aktif'),
('100017', 'Ali Ridho Pratama', 'Ust. Ridho', 'L', '1987-02-25', 'S1', 'Sejarah Islam', 'Jl. Merbabu No. 11, Klaten', '081210000017', 'Aktif'),
('100018', 'Salma Humaira Putri', 'Ustadzah Salma', 'L', '1995-06-11', 'SMA/Pesantren', 'Tahfidz', 'Jl. Anggrek No. 4, Karanganyar', '081210000018', 'Aktif'),
('100019', 'Zaid Alfarizi Hidayat', 'Ust. Zaid', 'L', '1980-12-29', 'S2', 'Ushul Fiqih', 'Jl. Sudirman No. 16, Wonogiri', '081210000019', 'Tidak Aktif'),
('100020', 'Fatimah Nur Azizah', 'Ustadzah Fatimah', 'L', '1989-03-07', 'S1', 'Akhlak Tasawuf', 'Jl. Kartini No. 2, Sragen', '081210000020', 'Aktif'),
('2', 'Ust. Muhammad Rizal, M.Pd', 'Ust. Rizal', 'L', '1980-01-03', 'S2 Manajemen Pendidikan', 'Hadis, Akhlak', 'Jl. Mawar No. 5', '0813-2222-2222', 'Tetap'),
('3', 'Ust. Abdul Hakim', 'Ust. Hakim', 'L', '1990-08-20', 'MA / Pesantren', 'Tajwid, Tahfidz', 'Jl. Kenanga No. 2', '0857-3333-3333', 'Tidak Tetap'),
('4', 'Ust. Hasan Basri, S.Ag', 'Ust. Hasan', 'L', '1987-05-15', 'S1 Tafsir', 'Tafsir, Bahasa Arab', 'Jl. Anggrek No. 8', '0821-4444-4444', 'Tetap'),
('5', 'Ust. Zainal Abidin, Lc', 'Ust. Zainal', 'L', '1983-06-07', 'S1 Syariah Universitas Al-Azhar', 'Ushul Fiqih, Siyasah', 'Jl. Dahlia No. 12', '0852-5555-5555', 'Tetap'),
('6', 'Ust. Nur Hidayat', 'Ust. Nur', 'L', '1995-10-10', 'MA / Pesantren', 'Nahwu, Sharaf', 'Jl. Flamboyan No. 3', '0878-6666-6666', 'Tidak Tetap');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_buku`
--
ALTER TABLE `tb_buku`
  ADD PRIMARY KEY (`isbn`);

--
-- Indeks untuk tabel `tb_mapel`
--
ALTER TABLE `tb_mapel`
  ADD PRIMARY KEY (`no_mapel`);

--
-- Indeks untuk tabel `tb_santri`
--
ALTER TABLE `tb_santri`
  ADD PRIMARY KEY (`no_induk`);

--
-- Indeks untuk tabel `tb_ustadz`
--
ALTER TABLE `tb_ustadz`
  ADD PRIMARY KEY (`nip`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_buku`
--
ALTER TABLE `tb_buku`
  MODIFY `isbn` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `tb_mapel`
--
ALTER TABLE `tb_mapel`
  MODIFY `no_mapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
