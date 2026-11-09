<?php
require __DIR__ . '/connection.php';
require __DIR__ . '/csrf.php';
$csrfToken = csrf_token();

try {
    $bukuResult = $conn->query('SELECT isbn, judul_buku, kategori_buku, pengarang_buku, pembuat_buku, harga FROM tb_buku ORDER BY isbn');
    if ($bukuResult === false) {
        error_log('Gagal mengambil data dari tabel tb_buku: ' . $conn->error);
    }
} catch (mysqli_sql_exception $exception) {
    $bukuResult = false;
    error_log('Gagal mengambil data dari tabel tb_buku: ' . $exception->getMessage());
}

$bukuQueryError = $bukuResult === false;
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
                <li><a href="index.php"><i class="fa-solid fa-gauge"></i>Dashboard</a></li>
                <li><a href="data_santri.php"><i class="fa-solid fa-user-graduate"></i>Data Santri</a></li>
                <li><a href="data_ustadz.php"><i class="fa-solid fa-chalkboard-user"></i>Data Ustadz</a></li>
                <li><a href="data_pelajaran.php"><i class="fa-solid fa-chalkboard"></i>Mata Pelajaran</a></li>
                <li><a href="data_buku.php" class="active" aria-current="page"><i class="fa-solid fa-book"></i>Data Buku</a></li>
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
                <h1>Data Buku</h1>
                <a href="add.php?entity=buku" class="btn-add-santri">
                    <i class="fa-solid fa-plus"></i> Tambah Buku
                </a>
            </div>
            <?php if (($_GET['delete'] ?? '') === 'deleted'): ?>
                <p class="delete-notice">Data buku berhasil dihapus.</p>
            <?php elseif (($_GET['delete'] ?? '') === 'not_found'): ?>
                <p class="delete-notice">Data buku tidak ditemukan atau sudah dihapus.</p>
            <?php endif; ?>
            <?php if (($_GET['edit'] ?? '') === 'saved'): ?>
                <p class="delete-notice">Data buku berhasil diperbarui.</p>
            <?php endif; ?>
            <?php if (($_GET['add'] ?? '') === 'saved'): ?>
                <p class="delete-notice">Data buku berhasil ditambahkan.</p>
            <?php endif; ?>
            <div class="w3-container table-wrap">
                <table class="tabel-santri">
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Judul Buku</th>
                            <th>Kategori Buku</th>
                            <th>Pengarang Buku</th>
                            <th>Pembuat Buku</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($bukuQueryError): ?>
                            <tr><td colspan="7">Data buku tidak dapat dimuat. Pastikan tabel tb_buku tersedia.</td></tr>
                        <?php elseif ($bukuResult->num_rows === 0): ?>
                            <tr><td colspan="7">Belum ada data buku.</td></tr>
                        <?php else: ?>
                            <?php while ($buku = $bukuResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) $buku['isbn'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($buku['judul_buku'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($buku['kategori_buku'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($buku['pengarang_buku'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($buku['pembuat_buku'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>Rp <?= number_format((int) $buku['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <a class="btn-edit" href="edit.php?entity=buku&amp;id=<?= rawurlencode((string) $buku['isbn']) ?>">Edit</a>
                                        <form action="delete.php" method="post" onsubmit="return confirm('Yakin ingin menghapus data buku ini?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="entity" value="buku">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars((string) $buku['isbn'], ENT_QUOTES, 'UTF-8') ?>">
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