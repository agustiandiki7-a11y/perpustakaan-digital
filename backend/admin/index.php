<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

// ======================================================
// KONEKSI DATABASE
// ======================================================
$database = new Database();
$db = $database->connect();

// ======================================================
// STATISTIK DASHBOARD
// ======================================================
try {
    // Total buku aktif
    $totalBuku = $db->query("SELECT COUNT(*) FROM books WHERE status = 'aktif'")->fetchColumn();

    // Total pengguna aktif
    $totalPengguna = $db->query("SELECT COUNT(*) FROM users WHERE status = 'aktif'")->fetchColumn();

    // Total seluruh peminjaman
    $totalPeminjaman = $db->query("SELECT COUNT(*) FROM loans")->fetchColumn();

    // Total peminjaman menunggu
    $totalMenunggu = $db->query("SELECT COUNT(*) FROM loans WHERE status = 'menunggu'")->fetchColumn();
} catch (PDOException $e) {
    $totalBuku = 0;
    $totalPengguna = 0;
    $totalPeminjaman = 0;
    $totalMenunggu = 0;
}

// Data user yang sedang login
$user = userLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Admin - Perpustakaan Digital</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport">
    
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon">

    <!-- Fonts & Icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons"
                ],
                urls: ["../assets/css/fonts.min.css"]
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/plugins.min.css">
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css">
    <link rel="stylesheet" href="../assets/css/demo.css">

    <style>
        .hero-card {
            background: linear-gradient(135deg, #1e1e2f 0%, #2b2b40 100%);
            border-radius: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .hero-card::after {
            content: '';
            position: absolute;
            right: -30px;
            bottom: -30px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }
        .stat-card {
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.08)!important;
        }
    </style>
</head>
<body>

    <div class="wrapper">

        <!-- SIDEBAR -->
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <div class="main-panel">

            <!-- HEADER / NAVBAR CONTAINER -->
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.php" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20">
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                        </div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>

                <!-- NAVBAR -->
                <?php include __DIR__ . '/../layout/navbar.php'; ?>
            </div>

            <!-- MAIN CONTENT CONTAINER -->
            <div class="container">
                <!-- Tambahkan pt-4 agar tidak tertutup navbar header -->
                <div class="page-inner pt-4">

                    <!-- HERO BANNER -->
                    <div class="hero-card shadow-lg mb-4 text-white p-4 p-md-5">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 fw-semibold">Dashboard Administrator</span>
                                <h1 class="fw-bold mb-2 display-6">Selamat Datang, <?= htmlspecialchars($user['nama'] ?? 'Admin') ?>! 👋</h1>
                                <p class="opacity-75 mb-4 fs-6">
                                    Kelola seluruh koleksi buku, data pengguna, transaksi peminjaman, serta laporan perpustakaan secara real-time dan terpusat.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="buku/tabel_buku.php" class="btn btn-light btn-round fw-semibold px-4">
                                        <i class="fas fa-book me-2"></i> Kelola Buku
                                    </a>
                                    <a href="peminjaman/tambah.php" class="btn btn-warning btn-round fw-semibold px-4 text-dark">
                                        <i class="fas fa-plus me-2"></i> Peminjaman Baru
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 text-center d-none d-md-block">
                                <i class="fas fa-book-reader fa-7x opacity-25 text-white"></i>
                            </div>
                        </div>
                    </div>

                    <!-- STATISTIK CARDS -->
                    <div class="row">
                        <!-- Total Buku -->
                        <div class="col-sm-6 col-xl-3 mb-4">
                            <div class="card stat-card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 bg-primary-subtle text-primary rounded-4 p-3 me-3">
                                            <i class="fas fa-book fa-2x"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1 small fw-semibold">Total Buku Aktif</p>
                                            <h3 class="fw-bold mb-0"><?= number_format($totalBuku) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengguna -->
                        <div class="col-sm-6 col-xl-3 mb-4">
                            <div class="card stat-card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 bg-success-subtle text-success rounded-4 p-3 me-3">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1 small fw-semibold">Pengguna Aktif</p>
                                            <h3 class="fw-bold mb-0"><?= number_format($totalPengguna) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Peminjaman -->
                        <div class="col-sm-6 col-xl-3 mb-4">
                            <div class="card stat-card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 bg-warning-subtle text-warning rounded-4 p-3 me-3">
                                            <i class="fas fa-book-reader fa-2x"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1 small fw-semibold">Total Peminjaman</p>
                                            <h3 class="fw-bold mb-0"><?= number_format($totalPeminjaman) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menunggu -->
                        <div class="col-sm-6 col-xl-3 mb-4">
                            <div class="card stat-card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 bg-danger-subtle text-danger rounded-4 p-3 me-3">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1 small fw-semibold">Menunggu Verifikasi</p>
                                            <h3 class="fw-bold mb-0"><?= number_format($totalMenunggu) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MENU CEPAT / QUICK ACTIONS -->
                    <div class="row mt-2">
                        <div class="col-md-4 mb-4">
                            <div class="card stat-card shadow-sm h-100 text-center p-3">
                                <div class="card-body">
                                    <div class="mb-3 text-primary">
                                        <i class="fas fa-book fa-3x"></i>
                                    </div>
                                    <h4 class="fw-bold">Data Buku</h4>
                                    <p class="text-muted small mb-4">Kelola katalog buku, kategori, stok, dan ketersediaan koleksi perpustakaan.</p>
                                    <a href="buku/tabel_buku.php" class="btn btn-outline-primary btn-round px-4">Buka Menu</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card stat-card shadow-sm h-100 text-center p-3">
                                <div class="card-body">
                                    <div class="mb-3 text-success">
                                        <i class="fas fa-users fa-3x"></i>
                                    </div>
                                    <h4 class="fw-bold">Manajemen Pengguna</h4>
                                    <p class="text-muted small mb-4">Kelola hak akses data admin, petugas, dan anggota terdaftar.</p>
                                    <a href="pengguna/tabel_pengguna.php" class="btn btn-outline-success btn-round px-4">Buka Menu</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card stat-card shadow-sm h-100 text-center p-3">
                                <div class="card-body">
                                    <div class="mb-3 text-warning">
                                        <i class="fas fa-exchange-alt fa-3x"></i>
                                    </div>
                                    <h4 class="fw-bold">Transaksi Peminjaman</h4>
                                    <p class="text-muted small mb-4">Pantau riwayat peminjaman, tenggat waktu, dan proses pengembalian buku.</p>
                                    <a href="peminjaman/tabel_peminjaman.php" class="btn btn-outline-warning btn-round px-4">Buka Menu</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GRAFIK STATISTIK -->
                    <div class="card stat-card shadow-sm mt-2">
                        <div class="card-header bg-white border-0 pt-4">
                            <h4 class="fw-bold mb-0">📈 Grafik Aktivitas Perpustakaan</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="statisticsChart" height="100"></canvas>
                        </div>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <?php include __DIR__ . '/../layout/footer.php'; ?>

        </div>
    </div>

    <!-- CORE JS -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- PLUGINS -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <!-- INISIALISASI GRAFIK -->
    <script>
        var ctx = document.getElementById('statisticsChart').getContext('2d');
        var statisticsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
                datasets: [{
                    label: "Peminjaman Buku",
                    borderColor: '#177dff',
                    pointBorderColor: '#FFF',
                    pointBackgroundColor: '#177dff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 4,
                    pointHoverBorderWidth: 1,
                    pointRadius: 3,
                    backgroundColor: 'rgba(23, 125, 255, 0.1)',
                    fill: true,
                    borderWidth: 2,
                    data: [12, 19, 15, 25, 22, 30, 18]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: { position: 'bottom' },
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true } }]
                }
            }
        });
    </script>
</body>
</html>