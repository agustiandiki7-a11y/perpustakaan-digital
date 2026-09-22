<?php

require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['petugas']);

$nama = $_SESSION['nama'] ?? 'Petugas';

function e($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

?>

<div class="sidebar sidebar-style-2" data-background-color="dark">

    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">

            <a href="/perpustakaan/backend/petugas/index.php" class="logo">
                <span style="color:#fff;font-size:20px;font-weight:700;">
                    <i class="fas fa-book-open me-2"></i>
                    Perpustakaan
                </span>
            </a>

            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>

                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>

        </div>
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">

            <ul class="nav nav-secondary">

                <!-- USER -->
                <li class="nav-item">
                    <div class="d-flex align-items-center p-3">

                        <div class="avatar avatar-sm">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>

                        <div class="ms-3">
                            <span class="text-white fw-bold d-block">
                                <?= e($nama) ?>
                            </span>

                            <small class="text-secondary">
                                Petugas
                            </small>
                        </div>

                    </div>
                </li>

                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a href="/perpustakaan/backend/petugas/index.php">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- MASTER DATA -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Master Data</h4>
                </li>

                <li class="nav-item">
                    <a href="/perpustakaan/backend/buku/tabel_buku.php">
                        <i class="fas fa-book"></i>
                        <p>Data Buku</p>
                    </a>
                </li>

                <!-- TRANSAKSI -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Transaksi</h4>
                </li>

                <li class="nav-item">
                    <a href="/perpustakaan/backend/peminjaman/tabel_peminjaman.php">
                        <i class="fas fa-book-reader"></i>
                        <p>Peminjaman</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/perpustakaan/backend/peminjaman/tambah.php">
                        <i class="fas fa-plus-circle"></i>
                        <p>Tambah Peminjaman</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/perpustakaan/backend/pengembalian/tabel_pengembalian.php">
                        <i class="fas fa-undo-alt"></i>
                        <p>Pengembalian</p>
                    </a>
                </li>

                <!-- AKUN -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Akun</h4>
                </li>

                <li class="nav-item">
                    <a href="/perpustakaan/backend/profile/profile.php">
                        <i class="fas fa-user-circle"></i>
                        <p>Profil Saya</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/perpustakaan/backend/logout.php"
                       onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>

        </div>
    </div>

</div>