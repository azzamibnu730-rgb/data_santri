<?php
/*
 * Sidebar + topbar HP, dipakai di semua halaman.
 * Cara pakai di tiap halaman (sebelum include):
 *     $halaman = 'santri';   // dashboard | santri | ustadz | pelajaran | laporan | almahir
 */
$halaman = $halaman ?? '';

$menu = [
    'dashboard' => ['index.php',          'fa-gauge',            'Dashboard'],
    'santri'    => ['data_santri.php',    'fa-user-graduate',    'Data Santri'],
    'ustadz'    => ['data_ustadz.php',    'fa-chalkboard-user',  'Data Ustadz'],
    'pelajaran' => ['data_pelajaran.php','fa-chalkboard',       'Mata Pelajaran'],
    'buku'      => ['data_buku.php',     'fa-book',             'Data Buku'],
    'almahir'   => ['data_almahir.php',  'fa-school',           'Al Mahir'],
];
?>
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
            <?php foreach ($menu as $kunci => [$link, $ikon, $label]): ?>
                <li>
                    <a href="<?= $link ?>"<?= $halaman === $kunci ? ' class="active" aria-current="page"' : '' ?>>
                        <i class="fa-solid <?= $ikon ?>"></i><?= $label ?>
                    </a>
                </li>
            <?php endforeach; ?>
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