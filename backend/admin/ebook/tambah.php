<?php
$pageTitle = 'Tambah Ebook';

$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$pdo = $database->connect();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

// Ambil daftar buku yang aktif untuk pilihan dropdown
try {
    $stmt = $pdo->query("
        SELECT id, kode_buku, judul, penulis 
        FROM books 
        WHERE status = 'aktif' 
        ORDER BY judul ASC
    ");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $books = [];
}

$backendPath = dirname(__DIR__, 2);
?>

<?php include $backendPath . '/layout/header.php'; ?>

<div class="wrapper">
    <?php include $backendPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php include $backendPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="page-header">
                    <h3 class="fw-bold mb-3">Manajemen Ebook</h3>
                    <ul class="breadcrumbs mb-3">
                        <li class="nav-home">
                            <a href="../index.php"><i class="icon-home"></i></a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="tabel_ebook.php">Data Ebook</a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Tambah Ebook</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-round">
                            <div class="card-header">
                                <div class="card-title fw-bold">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i> Form Unggah Ebook Baru
                                </div>
                            </div>

                            <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
                                <div class="card-body">

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group mb-3">
                                        <label for="book_id" class="form-label fw-bold">Pilih Buku <span class="text-danger">*</span></label>
                                        <select name="book_id" id="book_id" class="form-select" required>
                                            <option value="">-- Pilih Buku Induk --</option>
                                            <?php foreach ($books as $b): ?>
                                                <option value="<?= (int)$b['id'] ?>">
                                                    <?= htmlspecialchars($b['kode_buku'] . ' - ' . $b['judul'] . ' (' . $b['penulis'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Pilih data buku yang akan dilampirkan file digitalnya.</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="file_ebook" class="form-label fw-bold">File Dokumen Ebook <span class="text-danger">*</span></label>
                                        <input type="file" name="file_ebook" id="file_ebook" class="form-control" accept=".pdf,.epub" required>
                                        <small class="text-muted">Format file yang diperbolehkan: <strong>PDF</strong> atau <strong>EPUB</strong>. Maksimal ukuran file 20 MB.</small>
                                    </div>

                                </div>

                                <div class="card-action text-end">
                                    <a href="tabel_ebook.php" class="btn btn-secondary me-2">
                                        <i class="fas fa-arrow-left me-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Simpan Ebook
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include $backendPath . '/layout/footer.php'; ?>
    </div>
</div>
</body>
</html>