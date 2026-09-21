<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

// Mengambil data kategori dan menghitung jumlah buku per kategori secara aman menggunakan LEFT JOIN
$stmt = $pdo->query("
    SELECT 
        categories.*, 
        COUNT(books.id) AS jumlah_buku 
    FROM categories 
    LEFT JOIN books ON books.category_id = categories.id 
    GROUP BY categories.id 
    ORDER BY categories.id DESC
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/../layout/header.php'; ?>
    <title>Data Kategori - Backend Perpustakaan</title>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar Kaiadmin -->
        <?php include __DIR__ . '/../layout/sidebar.php'; ?>

        <div class="main-panel">
            <!-- Navbar Kaiadmin -->
            <?php include __DIR__ . '/../layout/navbar.php'; ?>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Data Kategori</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home">
                                <a href="../index.php"><i class="icon-home"></i></a>
                            </li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Master Data</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Kategori</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Kategori Buku</h4>
                                        <a href="tambah.php" class="btn btn-primary btn-round ms-auto">
                                            <i class="fa fa-plus"></i> Tambah Kategori
                                        </a>
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
                                                    <th>Nama Kategori</th>
                                                    <th>Slug</th>
                                                    <th>Deskripsi</th>
                                                    <th class="text-center">Jumlah Buku</th>
                                                    <th class="text-center">Status</th>
                                                    <th style="width: 10%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($categories)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-4">Belum ada data kategori.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($categories as $index => $cat): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td><strong><?= htmlspecialchars($cat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                                            <td><code><?= htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                                            <td><?= htmlspecialchars($cat['deskripsi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info text-white"><?= (int)$cat['jumlah_buku'] ?> Buku</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if (($cat['status'] ?? 'aktif') === 'aktif'): ?>
                                                                    <span class="badge bg-success">Aktif</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="form-button-action">
                                                                    <a href="edit.php?id=<?= (int)$cat['id'] ?>" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Edit Kategori">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <a href="hapus.php?id=<?= (int)$cat['id'] ?>" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Hapus Kategori" onclick="return confirm('Yakin ingin menghapus kategori ini?');">
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

            <!-- Footer Kaiadmin -->
            <?php include __DIR__ . '/../layout/footer.php'; ?>
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