<?php
require __DIR__ . '/connection.php';
require __DIR__ . '/csrf.php';
$csrfToken = csrf_token();

try {
    $ustadzResult = $conn->query('SELECT nip, nama_lengkap, bidang_keahlian, tgl_lahir FROM tb_ustadz ORDER BY nip');
    if ($ustadzResult === false) {
        error_log('Gagal mengambil data dari tabel tb_ustadz: ' . $conn->error);
    }
} catch (mysqli_sql_exception $exception) {
    $ustadzResult = false;
    error_log('Gagal mengambil data dari tabel tb_ustadz: ' . $exception->getMessage());
}

$ustadzQueryError = $ustadzResult === false;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Al Mahir</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Saklar menu HP (checkbox tersembunyi, pengganti JavaScript) -->
    <input type="checkbox" id="menuToggle" class="menu-toggle" aria-label="Buka menu">

    <!-- TOP BAR (khusus HP/Tablet) -->
    <header class="mobile-topbar">
        <label for="menuToggle" class="hamburger-btn" aria-label="Buka menu">
            <i class="fa-solid fa-bars"></i>
        </label>
        <span class="mobile-title">Al Mahir</span>
    </header>

    <!-- Overlay gelap: klik untuk menutup menu di HP -->
    <label for="menuToggle" class="sidebar-overlay" aria-label="Tutup menu"></label>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="logo-brand">
                <div class="brand-mark" role="img" aria-label="Logo Al Mahir">AM</div>
            </div>
            <div class="logo-text">Al Mahir</div>

            <div class="sidebar-search">
                <input type="text" placeholder="Cari menu...">
            </div>

            <ul class="sidebar-nav">
               <li><a href="index.php" class="active"><i class="fa-solid fa-gauge"></i>Dashboard</a></li>
                <li><a href="data_santri.php"><i class="fa-solid fa-user-graduate"></i>Data Santri</a></li>
                <li><a href="data_ustadz.php"><i class="fa-solid fa-chalkboard-user"></i>Data Ustadz</a></li>
                <li><a href="data_pelajaran.php"><i class="fa-solid fa-chalkboard"></i>Mata Pelajaran</a></li>
                <li><a href="data_buku.php"><i class="fa-solid fa-flag"></i>Data Buku</a></li>
                <li><a href="data_almahir.php"><i class="fa-solid fa-school"></i>Al Mahir</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="user-avatar">AD</div>
            <div class="user-info">
                <h4>Admin</h4>
                <p>azzamibnu730@gmail.com</p>
                <a href="login.html" class="logout-btn">Log Out</a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div id="tabel-santri" class="content-section">
            <div class="card-header-teal">
                <h1>Data Ustadz</h1>
                <a href="add.php?entity=ustadz" class="btn-add-santri">
                    <i class="fa-solid fa-plus"></i> Add Ustadz
                </a>
            </div>
            <?php if (($_GET['delete'] ?? '') === 'deleted'): ?>
                <p class="delete-notice">Data ustadz berhasil dihapus.</p>
            <?php elseif (($_GET['delete'] ?? '') === 'not_found'): ?>
                <p class="delete-notice">Data ustadz tidak ditemukan atau sudah dihapus.</p>
            <?php endif; ?>
            <?php if (($_GET['edit'] ?? '') === 'saved'): ?>
                <p class="delete-notice">Data ustadz berhasil diperbarui.</p>
            <?php endif; ?>
            <?php if (($_GET['add'] ?? '') === 'saved'): ?>
                <p class="delete-notice">Data ustadz berhasil ditambahkan.</p>
            <?php endif; ?>
            <div class="w3-container table-wrap">
                <table class="tabel-santri">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Ustadz</th>
                            <th>Pelajaran</th>
                            <th>Umur</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ustadzQueryError): ?>
                            <tr><td colspan="5">Data ustadz tidak dapat dimuat. Pastikan tabel tb_ustadz tersedia.</td></tr>
                        <?php elseif ($ustadzResult->num_rows === 0): ?>
                            <tr><td colspan="5">Belum ada data ustadz.</td></tr>
                        <?php else: ?>
                            <?php $nomor = 1; ?>
                            <?php while ($ustadz = $ustadzResult->fetch_assoc()): ?>
                                <?php
                                $tanggalLahir = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $ustadz['tgl_lahir']);
                                $umur = $tanggalLahir
                                    ? (string) $tanggalLahir->diff(new DateTimeImmutable('today'))->y . ' Thn'
                                    : '-';
                                ?>
                                <tr>
                                    <td><?= $nomor++ ?>.</td>
                                    <td><?= htmlspecialchars($ustadz['nama_lengkap'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($ustadz['bidang_keahlian'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($umur, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <a class="btn-edit" href="edit.php?entity=ustadz&amp;id=<?= rawurlencode((string) $ustadz['nip']) ?>">Edit</a>
                                        <form action="delete.php" method="post" onsubmit="return confirm('Yakin ingin menghapus data ustadz ini?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="entity" value="ustadz">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars((string) $ustadz['nip'], ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="btn-delete">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
     </main>
</body>
</html>