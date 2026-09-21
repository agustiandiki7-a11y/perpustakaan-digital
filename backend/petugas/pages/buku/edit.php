<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

// Ambil ID dari URL dan validasi sebagai integer
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'ID buku tidak valid!';
    header('Location: tabel_buku.php');
    exit;
}

// Ambil data buku berdasarkan ID menggunakan prepared statement
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    $_SESSION['error'] = 'Data buku tidak ditemukan!';
    header('Location: tabel_buku.php');
    exit;
}

// Ambil data kategori untuk pilihan dropdown
$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY nama_kategori ASC");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/../layout/header.php'; ?>
    <title>Edit Buku - Perpustakaan</title>
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
                        <h4 class="page-title">Edit Buku</h4>
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
                                <a href="#">Edit Buku</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Form Edit Data Buku</div>
                                </div>
                                <form action="proses-edit.php" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">

                                        <?php if ($error): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Hidden ID untuk proses update -->
                                        <input type="hidden" name="id" value="<?= $book['id'] ?>">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kode_buku">Kode Buku <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="kode_buku" name="kode_buku" value="<?= htmlspecialchars($book['kode_buku'], ENT_QUOTES, 'UTF-8') ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="isbn">ISBN</label>
                                                    <input type="text" class="form-control" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="judul">Judul Buku <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="judul" name="judul" value="<?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="category_id" name="category_id" required>
                                                        <option value="">-- Pilih Kategori --</option>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?= $cat['id'] ?>" <?= ($book['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($cat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="penulis">Penulis <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="penulis" name="penulis" value="<?= htmlspecialchars($book['penulis'], ENT_QUOTES, 'UTF-8') ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="penerbit">Penerbit</label>
                                                    <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?= htmlspecialchars($book['penerbit'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tahun_terbit">Tahun Terbit</label>
                                                    <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" value="<?= htmlspecialchars($book['tahun_terbit'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jumlah_stok">Jumlah Stok <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="jumlah_stok" name="jumlah_stok" min="1" value="<?= (int) $book['jumlah_stok'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lokasi_rak">Lokasi Rak</label>
                                                    <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak" value="<?= htmlspecialchars($book['lokasi_rak'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($book['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cover">Cover Buku Baru (Opsional)</label>
                                                    <?php if (!empty($book['cover'])): ?>
                                                        <div class="mb-2">
                                                            <img src="../../<?= htmlspecialchars($book['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Current Cover" class="img-thumbnail" style="width: 60px; height: 80px; object-fit: cover;">
                                                            <br><small class="text-muted">Cover saat ini</small>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
                                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah cover.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status">Status Buku <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="status" name="status" required>
                                                        <option value="aktif" <?= ($book['status'] === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                                        <option value="nonaktif" <?= ($book['status'] === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-action">
                                        <button type="submit" class="btn btn-primary">Perbarui Data</button>
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