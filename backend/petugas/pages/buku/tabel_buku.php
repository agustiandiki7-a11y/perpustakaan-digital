<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->query("
    SELECT 
        books.*, 
        categories.nama_kategori 
    FROM books 
    LEFT JOIN categories ON categories.id = books.category_id 
    ORDER BY books.id DESC
");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <?php include __DIR__ . '/../layout/header.php'; ?>
    <title>Data Buku - Perpustakaan</title>
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
                        <h4 class="page-title">Data Buku</h4>
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
                                <a href="#">Master Data</a>
                            </li>
                            <li class="separator">
                                <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Buku</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Buku Perpustakaan</h4>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <?php if ($success): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($error): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%">No</th>
                                                    <th style="width: 10%">Cover</th>
                                                    <th>Kode / ISBN</th>
                                                    <th>Judul Buku</th>
                                                    <th>Kategori</th>
                                                    <th>Penulis / Penerbit</th>
                                                    <th>Stok</th>
                                                    <th>Status</th>
                                                    <th style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($books)): ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted py-4">Belum ada data buku tersedia.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($books as $index => $book): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td>
                                                                <?php if (!empty($book['cover'])): ?>
                                                                    <img src="../../<?= htmlspecialchars($book['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover" class="img-thumbnail" style="width: 50px; height: 70px; object-fit: cover;" onerror="this.onerror=null; this.src='../../assets/img/placeholder.png';">
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">No Cover</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($book['kode_buku'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
                                                                <small class="text-muted"><?= htmlspecialchars($book['isbn'] ?? '-', ENT_QUOTES, 'UTF-8') ?></small>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars($book['judul'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                                                                <small class="text-muted">Tahun: <?= htmlspecialchars($book['tahun_terbit'] ?? '-', ENT_QUOTES, 'UTF-8') ?></small>
                                                            </td>
                                                            <td><?= htmlspecialchars($book['nama_kategori'] ?? 'Tanpa Kategori', ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td>
                                                                <?= htmlspecialchars($book['penulis'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                                                                <small class="text-muted"><?= htmlspecialchars($book['penerbit'] ?? '-', ENT_QUOTES, 'UTF-8') ?></small>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info">Tersedia: <?= (int)($book['stok_tersedia'] ?? 0) ?></span><br>
                                                                <small class="text-muted">Total: <?= (int)($book['jumlah_stok'] ?? 0) ?></small>
                                                            </td>
                                                            <td>
                                                                <?php if (($book['status'] ?? '') === 'aktif'): ?>
                                                                    <span class="badge bg-success">Aktif</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">Nonaktif</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="form-button-action">
                                                                    <a href="edit.php?id=<?= (int)$book['id'] ?>" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Edit Buku">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <a href="hapus.php?id=<?= (int)$book['id'] ?>" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Hapus Buku" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
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