<?php
require __DIR__ . '/connection.php';
require __DIR__ . '/csrf.php';
$csrfToken = csrf_token();

try {
    $mapelResult = $conn->query('SELECT no_mapel, name_mapel, jp_mapel, kategori_mapel FROM tb_mapel ORDER BY no_mapel');
    if ($mapelResult === false) {
        error_log('Gagal mengambil data dari tabel tb_mapel: ' . $conn->error);
    }
} catch (mysqli_sql_exception $exception) {
    $mapelResult = false;
    error_log('Gagal mengambil data dari tabel tb_mapel: ' . $exception->getMessage());
}

$mapelQueryError = $mapelResult === false;
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
    <h1>Mata Pelajaran</h1>
    <a href="add.php?entity=mapel" class="btn-add-santri">
        <i class="fa-solid fa-plus"></i> Tambah Pelajaran
    </a>
</div>
<?php if (($_GET['delete'] ?? '') === 'deleted'): ?>
    <p class="delete-notice">Data mata pelajaran berhasil dihapus.</p>
<?php elseif (($_GET['delete'] ?? '') === 'not_found'): ?>
    <p class="delete-notice">Data mata pelajaran tidak ditemukan atau sudah dihapus.</p>
<?php endif; ?>
<?php if (($_GET['edit'] ?? '') === 'saved'): ?>
    <p class="delete-notice">Data mata pelajaran berhasil diperbarui.</p>
<?php endif; ?>
<?php if (($_GET['add'] ?? '') === 'saved'): ?>
    <p class="delete-notice">Data mata pelajaran berhasil ditambahkan.</p>
<?php endif; ?>

<div class="w3-container table-wrap">
    <table class="tabel-santri">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Pelajaran</th>
                <th>JP</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($mapelQueryError): ?>
                <tr><td colspan="5">Data mata pelajaran tidak dapat dimuat. Pastikan tabel tb_mapel tersedia.</td></tr>
            <?php elseif ($mapelResult->num_rows === 0): ?>
                <tr><td colspan="5">Belum ada data mata pelajaran.</td></tr>
            <?php else: ?>
                <?php $nomor = 1; ?>
                <?php while ($mapel = $mapelResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $nomor++ ?>.</td>
                        <td><?= htmlspecialchars($mapel['name_mapel'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $mapel['jp_mapel'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($mapel['kategori_mapel'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="btn-edit" href="edit.php?entity=mapel&amp;id=<?= rawurlencode((string) $mapel['no_mapel']) ?>">Edit</a>
                            <form action="delete.php" method="post" onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="entity" value="mapel">
                                <input type="hidden" name="id" value="<?= htmlspecialchars((string) $mapel['no_mapel'], ENT_QUOTES, 'UTF-8') ?>">
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