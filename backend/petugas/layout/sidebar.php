<?php
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';

$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function menuAktif(string $page): string
{
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}

function folderAktif(string $folder): string
{
    global $currentPath;
    return strpos($currentPath, '/backend/' . $folder . '/') !== false ? 'active' : '';
}

function menuBukuAktif(): string
{
    return folderAktif('buku');
}

function menuPeminjamanAktif(): string
{
    return folderAktif('peminjaman');
}

function menuPengembalianAktif(): string
{
    return folderAktif('pengembalian');
}

function menuEbookAktif(): string
{
    return folderAktif('ebook');
}

function menuFavoritAktif(): string
{
    return folderAktif('favorit');
}

function menuKategoriAktif(): string
{
    return folderAktif('kategori');
}

function menuUlasanAktif(): string
{
    return folderAktif('ulasan');
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<div class="sidebar" data-background-color="dark">

    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">

            <?php if ($role === 'admin'): ?>
                <a href="/perpustakaan/backend/admin/index.php" class="logo">
            <?php else: ?>
                <a href="/perpustakaan/backend/petugas/index.php" class="logo">
            <?php endif; ?>

                <span style="color:#fff;font-size:20px;font-weight:700;letter-spacing:.5px;">
                    <i class="fas fa-book-open me-2"></i>
                    Perpustakaan
                </span>
                </a>

                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar" type="button">
                        <i class="gg-menu-right"></i>
                    </button>

                    <button class="btn btn-toggle sidenav-toggler" type="button">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>

                <button class="topbar-toggler more" type="button">
                    <i class="gg-more-vertical-alt"></i>
                </button>

        </div>
    </div>

    <div class="sidebar-wrapper " style="overflow-y: auto; max-height: calc(100vh - 60px);">
        <div class="sidebar-content">

            <ul class="nav nav-secondary">

                <!-- PROFIL PENGGUNA -->
                <li class="nav-item">
                    <div class="d-flex align-items-center" style="padding:15px 15px 12px;margin-bottom:5px;">

                        <div class="avatar avatar-sm" style="width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;color:#fff;">
                            <i class="fas fa-user"></i>
                        </div>

                        <div class="ms-3">
                            <span style="color:#fff;font-size:14px;font-weight:600;display:block;">
                                <?= e($nama) ?>
                            </span>

                            <small style="color:rgba(255,255,255,.65);text-transform:capitalize;">
                                <?= e($role) ?>
                            </small>
                        </div>

                    </div>
                </li>

                <!-- DASHBOARD -->
                <li class="nav-item <?= menuAktif('index.php') ?>">
                    <a href="/perpustakaan/backend/admin/index.php">
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

                <!-- BUKU -->
                <li class="nav-item <?= menuBukuAktif() ?>">
                    <a data-bs-toggle="collapse"
                        href="#menuBuku"
                        role="button"
                        aria-controls="menuBuku"
                        aria-expanded="<?= menuBukuAktif() ? 'true' : 'false' ?>">

                        <i class="fas fa-book"></i>
                        <p>Buku</p>
                        <span class="caret"></span>
                    </a>

                    <div class="collapse <?= menuBukuAktif() ? 'show' : '' ?>" id="menuBuku">
                        <ul class="nav nav-collapse">

                            <li class="<?= menuAktif('tabel_buku.php') ?>">
                                <a href="/perpustakaan/backend/admin/buku/tabel_buku.php">
                                    <span class="sub-item">Data Buku</span>
                                </a>
                            </li>

                            <li class="<?= menuAktif('tambah.php') ?>">
                                <a href="/perpustakaan/backend/admin/buku/tambah.php">
                                    <span class="sub-item">Tambah Buku</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <!-- KATEGORI -->
                <?php if ($role == 'admin'): ?>
                    <li class="nav-item <?= menuKategoriAktif() ?>">
                        <a href="/perpustakaan/backend/admin/kategori/tabel_kategori.php">
                            <i class="fas fa-tags"></i>
                            <p>Kategori</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- PENGGUNA -->
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item <?= menuAktif('tabel_pengguna.php') ?>">
                        <a href="/perpustakaan/backend/admin/pengguna/tabel_pengguna.php">
                            <i class="fas fa-users"></i>
                            <p>Pengguna</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- TRANSAKSI -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Transaksi</h4>
                </li>

                <!-- PEMINJAMAN -->
                <li class="nav-item <?= menuPeminjamanAktif() ?>">
                    <a data-bs-toggle="collapse"
                        href="#menuPeminjaman"
                        role="button"
                        aria-controls="menuPeminjaman"
                        aria-expanded="<?= menuPeminjamanAktif() ? 'true' : 'false' ?>">

                        <i class="fas fa-exchange-alt"></i>
                        <p>Peminjaman</p>
                        <span class="caret"></span>
                    </a>

                    <div class="collapse <?= menuPeminjamanAktif() ? 'show' : '' ?>" id="menuPeminjaman">
                        <ul class="nav nav-collapse">

                            <li class="<?= menuAktif('tabel_peminjaman.php') ?>">
                                <a href="/perpustakaan/backend/admin/peminjaman/tabel_peminjaman.php">
                                    <span class="sub-item">Data Peminjaman</span>
                                </a>
                            </li>

                            <li class="<?= menuAktif('tambah.php') ?>">
                                <a href="/perpustakaan/backend/admin/peminjaman/tambah.php">
                                    <span class="sub-item">Peminjaman Baru</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <!-- PENGEMBALIAN -->
                <li class="nav-item <?= menuPengembalianAktif() ?>">
                    <a href="/perpustakaan/backend/admin/pengembalian/tabel_pengembalian.php">
                        <i class="fas fa-undo"></i>
                        <p>Pengembalian</p>
                    </a>
                </li>

                <!-- PERPUSTAKAAN DIGITAL -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Perpustakaan Digital</h4>
                </li>

                <!-- EBOOK -->
                <?php if ($role == 'admin'): ?>
                    <li class="nav-item <?= menuEbookAktif() ?>">
                        <a href="/perpustakaan/backend/admin/ebook/tabel_ebook.php">
                            <i class="fas fa-file-pdf"></i>
                            <p>File Ebook</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- FAVORIT -->
                <?php if ($role == 'admin'): ?>
                    <li class="nav-item <?= menuFavoritAktif() ?>">
                        <a href="/perpustakaan/backend/admin/favorit/tabel_favorit.php">
                            <i class="fas fa-heart"></i>
                            <p>Favorit</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- ULASAN -->
                <?php if ($role == 'admin'): ?>
                    <li class="nav-item <?= menuUlasanAktif() ?>">
                        <a href="/perpustakaan/backend/admin/ulasan/tabel_ulasan.php">
                            <i class="fas fa-star"></i>
                            <p>Ulasan</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- LAPORAN -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Laporan</h4>
                </li>

                <li class="nav-item <?= menuAktif('tabel_laporan.php') ?>">
                    <a href="/perpustakaan/backend/admin/laporan/tabel_laporan.php">
                        <i class="fas fa-chart-bar"></i>
                        <p>Laporan</p>
                    </a>
                </li>

                <!-- AKUN -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Akun</h4>
                </li>

                <!-- PROFIL -->
                <li class="nav-item <?= menuAktif('tabel_profile.php') ?>">
                    <a href="/perpustakaan/backend/admin/profile/tabel_profile.php">
                        <i class="fas fa-user-circle"></i>
                        <p>Profil Saya</p>
                    </a>
                </li>

                <!-- LOGOUT -->
                <li class="nav-item">
                    <a href="/perpustakaan/backend/logout.php"
                        onclick="return confirm('Yakin ingin keluar dari sistem?');">

                        <i class="fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>

        </div>
    </div>
</div>