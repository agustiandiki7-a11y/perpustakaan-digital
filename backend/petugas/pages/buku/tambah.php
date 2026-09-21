<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

// Mengambil data kategori untuk pilihan dropdown form
$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY nama_kategori ASC");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/../layout/header.php'; ?>
    <title>Tambah Buku - Perpustakaan</title>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <!-- Navbar -->
            <?php include __DIR__ . '/../layout/navbar.php'; ?>
            <!-- End Navbar -->

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Tambah Buku</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home">
                                <a href="../index.php">
                                    <i class="icon-home"></i>
                                </a>
                            </li>
                            <li class="separator">
                                <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="tabel_buku.php">Data Buku</a>
                            </li>
                            <li class="separator">
                                <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Tambah Buku</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Form Tambah Buku Baru</div>
                                </div>
                                <form action="proses-tambah.php" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">

                                        <?php if ($error): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kode_buku">Kode Buku <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="kode_buku" name="kode_buku" placeholder="Contoh: BK006" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="isbn">ISBN</label>
                                                    <input type="text" class="form-control" id="isbn" name="isbn" placeholder="Nomor ISBN (Opsional)">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="judul">Judul Buku <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul buku" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="category_id" name="category_id" required>
                                                        <option value="">-- Pilih Kategori --</option>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="penulis">Penulis <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="penulis" name="penulis" placeholder="Nama penulis buku" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="penerbit">Penerbit</label>
                                                    <input type="text" class="form-control" id="penerbit" name="penerbit" placeholder="Nama penerbit">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tahun_terbit">Tahun Terbit</label>
                                                    <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" placeholder="Contoh: 2024">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jumlah_stok">Jumlah Stok <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="jumlah_stok" name="jumlah_stok" min="1" value="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lokasi_rak">Lokasi Rak</label>
                                                    <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak" placeholder="Contoh: Rak A1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Sinopsis atau keterangan buku..."></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cover">Cover Buku (Gambar)</label>
                                                    <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
                                                    <small class="text-muted">Format yang diizinkan: JPG, JPEG, PNG.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status">Status Buku <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="status" name="status" required>
                                                        <option value="aktif">Aktif</option>
                                                        <option value="nonaktif">Nonaktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-action">
                                        <button type="submit" class="btn btn-success">Simpan Data</button>
                                        <a href="tabel_buku.php" class="btn btn-danger">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include __DIR__ . '/../layout/footer.php'; ?>
            <!-- End Footer -->
        </div>
    </div>

    <!-- Core JS Scripts -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>
</html>