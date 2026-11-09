<?php
require __DIR__ . '/connection.php';

function fetchDashboardRows(mysqli $connection, string $table, string $sql): array|false
{
    try {
        $result = $connection->query($sql);
        if ($result === false) {
            error_log('Gagal mengambil data dashboard dari tabel ' . $table . ': ' . $connection->error);
            return false;
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $exception) {
        error_log('Gagal mengambil data dashboard dari tabel ' . $table . ': ' . $exception->getMessage());
        return false;
    }
}

function groupDashboardRows(array $rows, string $field): array
{
    $counts = [];
    foreach ($rows as $row) {
        $label = trim((string) ($row[$field] ?? ''));
        $label = $label !== '' ? $label : 'Tidak diketahui';
        $counts[$label] = ($counts[$label] ?? 0) + 1;
    }

    arsort($counts);
    return $counts;
}

function topDashboardCounts(array $counts, int $limit = 6): array
{
    if (count($counts) <= $limit) {
        return $counts;
    }

    $topCounts = array_slice($counts, 0, $limit, true);
    $topCounts['Lainnya'] = array_sum(array_slice($counts, $limit, null, true));
    return $topCounts;
}

function renderDashboardChart(string $title, string $description, array $counts, string $unit = 'data', bool $currency = false): void
{
    echo '<article class="dashboard-card chart-card">';
    echo '<div class="chart-heading"><div><h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>';
    echo '<p>' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</p></div></div>';

    if ($counts === []) {
        echo '<p class="chart-empty">Belum ada data untuk ditampilkan.</p></article>';
        return;
    }

    $maximum = max($counts);
    echo '<ul class="chart-bars">';
    foreach ($counts as $label => $value) {
        $percentage = $maximum > 0 ? (int) round(($value / $maximum) * 100) : 0;
        $formattedValue = $currency
            ? 'Rp ' . number_format((float) $value, 0, ',', '.')
            : number_format((float) $value, 0, ',', '.') . ' ' . $unit;

        echo '<li class="chart-bar-item">';
        echo '<div class="chart-bar-label"><span>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</span>';
        echo '<strong>' . htmlspecialchars($formattedValue, ENT_QUOTES, 'UTF-8') . '</strong></div>';
        echo '<div class="chart-track" role="img" aria-label="' . htmlspecialchars((string) $label . ': ' . $formattedValue, ENT_QUOTES, 'UTF-8') . '">';
        echo '<span class="chart-bar-fill" style="width: ' . $percentage . '%"></span></div></li>';
    }
    echo '</ul></article>';
}

$queries = [
    'santri' => [
        'table' => 'tb_santri',
        'sql' => 'SELECT kelamin, tempat_lahir FROM tb_santri',
    ],
    'ustadz' => [
        'table' => 'tb_ustadz',
        'sql' => 'SELECT status, bidang_keahlian, pdk_terakhir FROM tb_ustadz',
    ],
    'mapel' => [
        'table' => 'tb_mapel',
        'sql' => 'SELECT name_mapel, jp_mapel, kategori_mapel FROM tb_mapel',
    ],
    'buku' => [
        'table' => 'tb_buku',
        'sql' => 'SELECT judul_buku, kategori_buku, harga FROM tb_buku',
    ],
];

$data = [];
$queryErrors = [];
foreach ($queries as $key => $query) {
    $data[$key] = fetchDashboardRows($conn, $query['table'], $query['sql']);
    if ($data[$key] === false) {
        $queryErrors[] = $query['table'];
        $data[$key] = [];
    }
}

$santriGender = [];
foreach ($data['santri'] as $santri) {
    $gender = strtoupper(trim((string) ($santri['kelamin'] ?? '')));
    $label = $gender === 'L' ? 'Laki-laki' : ($gender === 'P' ? 'Perempuan' : 'Lainnya');
    $santriGender[$label] = ($santriGender[$label] ?? 0) + 1;
}
$santriGender = topDashboardCounts($santriGender);

$mapelHours = [];
foreach ($data['mapel'] as $mapel) {
    $name = trim((string) ($mapel['name_mapel'] ?? ''));
    $name = $name !== '' ? $name : 'Tidak diketahui';
    $mapelHours[$name] = ($mapelHours[$name] ?? 0) + (int) ($mapel['jp_mapel'] ?? 0);
}
arsort($mapelHours);

$bookPricesByCategory = [];
foreach ($data['buku'] as $book) {
    $category = trim((string) ($book['kategori_buku'] ?? ''));
    $category = $category !== '' ? $category : 'Tidak diketahui';
    $bookPricesByCategory[$category]['total'] = ($bookPricesByCategory[$category]['total'] ?? 0) + (float) ($book['harga'] ?? 0);
    $bookPricesByCategory[$category]['count'] = ($bookPricesByCategory[$category]['count'] ?? 0) + 1;
}
$bookAveragePrices = [];
foreach ($bookPricesByCategory as $category => $summary) {
    $bookAveragePrices[$category] = $summary['count'] > 0
        ? $summary['total'] / $summary['count']
        : 0;
}
arsort($bookAveragePrices);

$bookPrices = array_column($data['buku'], 'harga');
$totalBookValue = array_sum(array_map('intval', $bookPrices));
$totalLessonHours = array_sum(array_map(static fn (array $mapel): int => (int) ($mapel['jp_mapel'] ?? 0), $data['mapel']));
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
    <input type="checkbox" id="menuToggle" class="menu-toggle" aria-label="Buka menu">
    <header class="mobile-topbar">
        <label for="menuToggle" class="hamburger-btn" aria-label="Buka menu">
            <i class="fa-solid fa-bars"></i>
        </label>
        <span class="mobile-title">Al Mahir</span>
    </header>
    <label for="menuToggle" class="sidebar-overlay" aria-label="Tutup menu"></label>

    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="logo-brand">
                <div class="brand-mark" role="img" aria-label="Logo Al Mahir">AM</div>
            </div>
            <div class="logo-text">Al Mahir</div>
            <div class="sidebar-search">
                <input type="text" placeholder="Cari menu..." aria-label="Cari menu">
            </div>
            <ul class="sidebar-nav">
                <li><a href="index.php" class="active" aria-current="page"><i class="fa-solid fa-gauge"></i>Dashboard</a></li>
                <li><a href="data_santri.php"><i class="fa-solid fa-user-graduate"></i>Data Santri</a></li>
                <li><a href="data_ustadz.php"><i class="fa-solid fa-chalkboard-user"></i>Data Ustadz</a></li>
                <li><a href="data_pelajaran.php"><i class="fa-solid fa-chalkboard"></i>Mata Pelajaran</a></li>
                <li><a href="data_buku.php"><i class="fa-solid fa-book"></i>Data Buku</a></li>
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

    <main class="main-content">
        <section class="content-section dashboard-section">
            <header class="dashboard-heading">
                <div>
                    <p class="dashboard-eyebrow">Ringkasan pondok</p>
                    <h1>Dashboard Al Mahir</h1>
                    <p>Visualisasi data terkini dari seluruh tabel database.</p>
                </div>
                <span class="dashboard-date"><?= htmlspecialchars(date('d M Y'), ENT_QUOTES, 'UTF-8') ?></span>
            </header>

            <?php if ($queryErrors !== []): ?>
                <div class="dashboard-error" role="alert">
                    Data dari <?= htmlspecialchars(implode(', ', $queryErrors), ENT_QUOTES, 'UTF-8') ?> tidak dapat dimuat. Periksa tabel dan koneksi database.
                </div>
            <?php endif; ?>

            <div class="dashboard-stats">
                <article class="stat-card stat-santri">
                    <span class="stat-icon"><i class="fa-solid fa-user-graduate"></i></span>
                    <div><p>Total Santri</p><strong><?= number_format(count($data['santri']), 0, ',', '.') ?></strong><a href="data_santri.php">Lihat data <i class="fa-solid fa-arrow-right"></i></a></div>
                </article>
                <article class="stat-card stat-ustadz">
                    <span class="stat-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
                    <div><p>Total Ustadz</p><strong><?= number_format(count($data['ustadz']), 0, ',', '.') ?></strong><a href="data_ustadz.php">Lihat data <i class="fa-solid fa-arrow-right"></i></a></div>
                </article>
                <article class="stat-card stat-mapel">
                    <span class="stat-icon"><i class="fa-solid fa-book-open"></i></span>
                    <div><p>Mata Pelajaran</p><strong><?= number_format(count($data['mapel']), 0, ',', '.') ?></strong><a href="data_pelajaran.php">Lihat data <i class="fa-solid fa-arrow-right"></i></a></div>
                </article>
                <article class="stat-card stat-buku">
                    <span class="stat-icon"><i class="fa-solid fa-book"></i></span>
                    <div><p>Koleksi Buku</p><strong><?= number_format(count($data['buku']), 0, ',', '.') ?></strong><a href="data_buku.php">Lihat data <i class="fa-solid fa-arrow-right"></i></a></div>
                </article>
            </div>

            <div class="dashboard-section-title">
                <div><h2>Analisis Data</h2><p>Grafik dirangkum langsung dari seluruh isi tiap tabel.</p></div>
            </div>
            <div class="dashboard-charts">
                <?php
                renderDashboardChart('Santri berdasarkan jenis kelamin', 'Komposisi santri terdaftar', $santriGender, 'santri');
                renderDashboardChart('Asal daerah santri', 'Jumlah santri menurut tempat lahir', topDashboardCounts(groupDashboardRows($data['santri'], 'tempat_lahir')), 'santri');
                renderDashboardChart('Status ustadz', 'Sebaran status pengajar', groupDashboardRows($data['ustadz'], 'status'), 'ustadz');
                renderDashboardChart('Bidang keahlian ustadz', 'Jumlah pengajar per bidang keahlian', topDashboardCounts(groupDashboardRows($data['ustadz'], 'bidang_keahlian')), 'ustadz');
                renderDashboardChart('Mata pelajaran per kategori', 'Jumlah mata pelajaran pada tiap kategori', groupDashboardRows($data['mapel'], 'kategori_mapel'), 'mapel');
                renderDashboardChart('Jam pelajaran per mata pelajaran', 'Total JP dari semua mata pelajaran', $mapelHours, 'JP');
                renderDashboardChart('Buku per kategori', 'Jumlah koleksi buku berdasarkan kategori', topDashboardCounts(groupDashboardRows($data['buku'], 'kategori_buku')), 'buku');
                renderDashboardChart('Rata-rata harga buku', 'Harga rata-rata per kategori buku', $bookAveragePrices, '', true);
            ?>
            </div>

            <div class="dashboard-footnote">
                <span><i class="fa-solid fa-clock"></i> Total jam pelajaran: <strong><?= number_format($totalLessonHours, 0, ',', '.') ?> JP</strong></span>
                <span><i class="fa-solid fa-coins"></i> Nilai harga seluruh koleksi: <strong>Rp <?= number_format($totalBookValue, 0, ',', '.') ?></strong></span>
            </div>
        </section>
    </main>
</body>
</html>
