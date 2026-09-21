<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

$id = $_GET['id'] ?? null;

if (empty($id) || !is_numeric($id)) {
    header('Location: tabel_ulasan.php');
    exit;
}

$database = new Database();
$pdo = $database->connect();

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.*, 
            us.nama AS nama_pengguna, 
            us.email AS email_pengguna,
            b.judul AS judul_buku,
            b.kode_buku
        FROM ulasan u
        LEFT JOIN users us ON us.id = u.user_id
        LEFT JOIN books b ON b.id = u.book_id
        WHERE u.id = :id
    ");
    $stmt->execute([':id' => $id]);
    $ulasan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ulasan) {
        header('Location: tabel_ulasan.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: tabel_ulasan.php');
    exit;
}

$backendPath = dirname(__DIR__, 2);
$pageTitle = 'Detail Ulasan';
?>

<?php require_once $backendPath . '/layout/header.php'; ?>

<div class="wrapper">
    <?php require_once $backendPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php require_once $backendPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="page-header mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Detail Ulasan Buku</h3>
                        <p class="text-muted mb-0">Informasi lengkap ulasan yang diberikan oleh pengguna</p>
                    </div>
                    <ul class="breadcrumbs ms-auto">
                        <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="tabel_ulasan.php">Data Ulasan</a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Detail</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card card-round shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div class="card-title fw-bold text-primary m-0">
                                    <i class="fas fa-info-circle me-2"></i> Rincian Informasi Ulasan
                                </div>
                            </div>
                            <div class="card-body px-4 py-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%" class="text-muted">Nama Pengguna</th>
                                        <td width="5%">:</td>
                                        <td><strong><?= htmlspecialchars($ulasan['nama_pengguna'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Email Pengguna</th>
                                        <td>:</td>
                                        <td><?= htmlspecialchars($ulasan['email_pengguna'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Judul Buku</th>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-info text-dark"><?= htmlspecialchars($ulasan['kode_buku'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                            <strong><?= htmlspecialchars($ulasan['judul_buku'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Rating Buku</th>
                                        <td>:</td>
                                        <td>
                                            <div class="text-warning">
                                                <?php $rating = (int)($ulasan['rating'] ?? 0); ?>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="<?= $i <= $rating ? 'fas' : 'far' ?> fa-star"></i>
                                                <?php endfor; ?>
                                                <span class="ms-2 text-dark fw-bold"><?= $rating ?> / 5</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Komentar / Ulasan</th>
                                        <td>:</td>
                                        <td class="bg-light p-3 rounded text-break">
                                            <?= nl2br(htmlspecialchars($ulasan['komentar'] ?? 'Tidak ada komentar', ENT_QUOTES, 'UTF-8')) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Waktu Kirim</th>
                                        <td>:</td>
                                        <td><?= !empty($ulasan['created_at']) ? date('d F Y, H:i', strtotime($ulasan['created_at'])) : '-' ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-action bg-light px-4 py-3 text-end rounded-bottom">
                                <a href="tabel_ulasan.php" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php require_once $backendPath . '/layout/footer.php'; ?>
    </div>
</div>
</body>
</html>