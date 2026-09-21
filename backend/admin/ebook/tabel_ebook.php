<?php
$pageTitle = 'Data Ebook';

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
            book_files.id,
            book_files.book_id,
            book_files.nama_file,
            book_files.file_path,
            book_files.tipe_file,
            book_files.ukuran_file,
            book_files.created_at,
            books.kode_buku,
            books.judul,
            books.penulis,
            categories.nama_kategori
        FROM book_files
        INNER JOIN books
            ON books.id = book_files.book_id
        LEFT JOIN categories
            ON categories.id = books.category_id
        ORDER BY book_files.id DESC
    ");

    $ebooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $ebooks = [];
}

function escapeHtml($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function formatUkuran($bytes): string {
    if (!is_numeric($bytes) || $bytes <= 0) {
        return '-';
    }

    $bytes = (int)$bytes;

    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    }

    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    }

    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }

    return $bytes . ' Bytes';
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
                        <h3 class="fw-bold mb-1">Data Ebook</h3>
                        <p class="text-muted mb-0">Kelola lampiran file ebook digital perpustakaan</p>
                    </div>

                    <a href="tambah.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Ebook
                    </a>
                </div>

                <div class="card card-round">
                    <div class="card-header">
                        <h4 class="card-title mb-1">Daftar Ebook</h4>
                        <p class="card-category mb-0">Semua file digital ebook yang tersedia di sistem</p>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Kode Buku</th>
                                        <th>Judul Buku</th>
                                        <th>Penulis</th>
                                        <th>Kategori</th>
                                        <th>Nama File</th>
                                        <th>Ukuran</th>
                                        <th>Tanggal</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ebooks)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Belum ada ebook</h5>
                                                <p class="text-muted mb-0">Silakan tambahkan ebook terlebih dahulu.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ebooks as $no => $ebook): ?>
                                            <tr>
                                                <td><?= $no + 1 ?></td>
                                                <td><span class="badge bg-info"><?= escapeHtml($ebook['kode_buku']) ?></span></td>
                                                <td><strong><?= escapeHtml($ebook['judul']) ?></strong></td>
                                                <td><?= escapeHtml($ebook['penulis']) ?></td>
                                                <td>
                                                    <?php if (!empty($ebook['nama_kategori'])): ?>
                                                        <span class="badge bg-secondary"><?= escapeHtml($ebook['nama_kategori']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                                        <span><?= escapeHtml($ebook['nama_file']) ?></span>
                                                    </div>
                                                </td>
                                                <td><?= formatUkuran($ebook['ukuran_file']) ?></td>
                                                <td><?= !empty($ebook['created_at']) ? date('d-m-Y H:i', strtotime($ebook['created_at'])) : '-' ?></td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="detail.php?id=<?= (int)$ebook['id'] ?>" class="btn btn-sm btn-info text-white" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit.php?id=<?= (int)$ebook['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="hapus.php?id=<?= (int)$ebook['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus ebook ini?');">
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