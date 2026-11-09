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
         
        <section id="konten-almahir" class="content-section">
            <div class="w3-container w3-teal">
                <h1>Al-Mahir</h1>
            </div>
            <div class="w3-container table-wrap">
                <p>pondok sangat bagus</p>
            </div>
        </section>
    </main>

</body>
</html>