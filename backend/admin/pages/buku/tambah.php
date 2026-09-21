<?php
$pageTitle = 'Tambah Buku';

$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$pdo = $database->connect();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

try {
    $stmtCategory = $pdo->query("SELECT id, nama_kategori FROM categories ORDER BY nama_kategori ASC");
    $categories = $stmtCategory->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

$layoutPath = dirname(__DIR__, 2);
?>

<?php include $layoutPath . '/layout/header.php'; ?>

<div class="wrapper">
    <?php include $layoutPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php include $layoutPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <!-- Header Halaman -->
                <div class="page-header mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Tambah Buku Fisik</h3>
                        <p class="text-muted mb-0">Formulir pendaftaran katalog buku perpustakaan baru</p>
                    </div>
                    <ul class="breadcrumbs ms-auto">
                        <li class="nav-home">
                            <a href="../index.php"><i class="icon-home"></i></a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="tabel_buku.php">Data Buku</a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="#">Tambah Buku</a></li>
                    </ul>
                </div>

                <!-- Form Card -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-round shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div class="card-title fw-bold text-primary m-0">
                                    <i class="fas fa-book-medical me-2"></i> Form Input Data Buku
                                </div>
                            </div>

                            <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
                                <div class="card-body px-4 py-4">

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="kode_buku" class="form-label fw-bold">
                                                    Kode Buku <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                                                    <input type="text" name="kode_buku" id="kode_buku" class="form-control" placeholder="Contoh: BK-001" required>
                                                </div>
                                                <small class="text-muted">Gunakan kode unik untuk identifikasi fisik.</small>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="judul" class="form-label fw-bold">
                                                    Judul Buku <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-heading"></i></span>
                                                    <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan judul lengkap buku" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="penulis" class="form-label fw-bold">
                                                    Penulis <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-user-pen"></i></span>
                                                    <input type="text" name="penulis" id="penulis" class="form-control" placeholder="Nama penulis / pengarang" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="penerbit" class="form-label fw-bold">
                                                    Penerbit <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="fas fa-building"></i></span>
                                                    <input type="text" name="penerbit" id="penerbit" class="form-control" placeholder="Nama perusahaan penerbit" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="tahun_terbit" class="form-label fw-bold">
                                                            Tahun Terbit <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" name="tahun_terbit" id="tahun_terbit" class="form-control" placeholder="2024" min="1900" max="<?= date('Y') ?>" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="jumlah_stok" class="form-label fw-bold">
                                                            Jumlah Stok <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" name="jumlah_stok" id="jumlah_stok" class="form-control" min="1" value="1" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="category_id" class="form-label fw-bold">
                                                    Kategori Buku <span class="text-danger">*</span>
                                                </label>
                                                <select name="category_id" id="category_id" class="form-select" required>
                                                    <option value="">-- Pilih Kategori Buku --</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?= (int)$cat['id'] ?>">
                                                            <?= htmlspecialchars($cat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="cover" class="form-label fw-bold">
                                                    Cover Buku <span class="text-muted fw-normal">(Opsional)</span>
                                                </label>
                                                <input type="file" name="cover" id="cover" class="form-control" accept=".jpg, .jpeg, .png">
                                                <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG. Maksimal ukuran 2 MB.</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-action bg-light px-4 py-3 text-end rounded-bottom">
                                    <a href="tabel_buku.php" class="btn btn-secondary px-4 me-2">
                                        <i class="fas fa-arrow-left me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="fas fa-save me-1"></i> Simpan Buku
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include $layoutPath . '/layout/footer.php'; ?>
    </div>
</div>
</body>
</html>