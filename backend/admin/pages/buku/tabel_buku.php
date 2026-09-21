<?php
$pageTitle = 'Data Buku';

$backendPath = dirname(__DIR__, 2);
$rootPath = dirname(__DIR__, 3);

require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$pdo = $database->connect();

try {
    $stmt = $pdo->query("
        SELECT 
            books.*, 
            categories.nama_kategori 
        FROM books 
        LEFT JOIN categories ON categories.id = books.category_id 
        ORDER BY books.id DESC
    ");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $books = [];
}

function escapeHtml($val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<?php include $backendPath . '/layout/header.php'; ?>

<div class="wrapper">
    <?php include $backendPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php include $backendPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Data Buku Fisik</h3>
                        <p class="text-muted mb-0">Kelola katalog buku perpustakaan</p>
                    </div>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Buku
                    </a>
                </div>

                <div class="card card-round">
                    <div class="card-header">
                        <h4 class="card-title mb-1">Daftar Buku</h4>
                        <p class="card-category mb-0">Semua buku fisik yang terdaftar di sistem</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Kode Buku</th>
                                        <th>Judul Buku</th>
                                        <th>Penulis</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th width="130">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($books)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data buku.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($books as $no => $buku): ?>
                                            <tr>
                                                <td><?= $no + 1 ?></td>
                                                <td><span class="badge bg-info"><?= escapeHtml($buku['kode_buku']) ?></span></td>
                                                <td><strong><?= escapeHtml($buku['judul']) ?></strong></td>
                                                <td><?= escapeHtml($buku['penulis']) ?></td>
                                                <td><span class="badge bg-secondary"><?= escapeHtml($buku['nama_kategori'] ?? '-') ?></span></td>
                                                <td><?= (int)$buku['stok_tersedia'] ?> / <?= (int)$buku['jumlah_stok'] ?></td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="edit.php?id=<?= (int)$buku['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="hapus.php?id=<?= (int)$buku['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus buku ini?');">
                                                            <i class="fas fa-trash"></i>
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

        <?php include $backendPath . '/layout/footer.php'; ?>
    </div>
</div>
</body>
</html> 