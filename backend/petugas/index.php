<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['petugas', 'admin']);

// ======================================================
// KONEKSI DATABASE
// ======================================================
$database = new Database();
$db = $database->connect();

// ======================================================
// STATISTIK DASHBOARD
// ======================================================
$totalBuku = $db->query("
    SELECT COUNT(*) 
    FROM books 
    WHERE status = 'aktif'
")->fetchColumn();

$totalPengguna = $db->query("
    SELECT COUNT(*) 
    FROM users 
    WHERE status = 'aktif'
")->fetchColumn();

$totalPeminjaman = $db->query("
    SELECT COUNT(*) 
    FROM loans
")->fetchColumn();

$totalMenunggu = $db->query("
    SELECT COUNT(*) 
    FROM loans 
    WHERE status = 'menunggu'
")->fetchColumn();

$user = userLogin();

// Path absolut menuju folder backend (naik 1 level dari folder petugas)
$rootPath = dirname(__DIR__, 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include $rootPath . '/layout/header.php'; ?>
    <title>Dashboard Petugas - Perpustakaan Digital</title>
</head>
<body>
<div class="wrapper">

    <!-- SIDEBAR -->
    <?php include $rootPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">

        <!-- NAVBAR -->
        <?php include $rootPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <!-- TITLE -->
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                    <div>
                        <h3 class="fw-bold mb-3">Dashboard Petugas</h3>
                        <h6 class="op-7 mb-2">Selamat datang, <?= htmlspecialchars($user['nama'] ?? 'Petugas') ?>!</h6>
                    </div>
                </div>

                <!-- STATISTIC CARDS -->
                <div class="row">
                    <!-- TOTAL BUKU -->
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Total Buku</p>
                                            <h4 class="card-title"><?= (int) $totalBuku ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL PENGGUNA -->
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-info bubble-shadow-small">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Pengguna Aktif</p>
                                            <h4 class="card-title"><?= (int) $totalPengguna ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL PEMINJAMAN -->
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="fas fa-book-reader"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Total Peminjaman</p>
                                            <h4 class="card-title"><?= (int) $totalPeminjaman ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MENUNGGU -->
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                            <i class="far fa-clock"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Menunggu</p>
                                            <h4 class="card-title"><?= (int) $totalMenunggu ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- FOOTER -->
        <?php include $rootPath . '/layout/footer.php'; ?>

    </div>
</div>

</body>
</html>