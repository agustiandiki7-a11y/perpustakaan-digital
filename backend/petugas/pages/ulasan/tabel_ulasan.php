<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->query("
        SELECT
            u.id,
            u.rating,
            u.komentar,
            u.created_at,
            us.nama AS nama_pengguna,
            b.judul AS judul_buku
        FROM ulasan u
        LEFT JOIN users us ON us.id = u.user_id
        LEFT JOIN books b ON b.id = u.book_id
        ORDER BY u.created_at DESC
    ");

    $ulasan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ulasan = [];
    $error = $e->getMessage();
}

$totalUlasan = count($ulasan);

$totalRating = 0;
$jumlahRating = 0;

foreach ($ulasan as $item) {
    if (isset($item['rating']) && is_numeric($item['rating'])) {
        $totalRating += (int) $item['rating'];
        $jumlahRating++;
    }
}

$rataRating = $jumlahRating > 0
    ? number_format($totalRating / $jumlahRating, 1)
    : '0.0';
?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-panel">

    <?php require_once __DIR__ . '/../layout/navbar.php'; ?>

    <div class="container">

        <div class="page-inner">

            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">

                <div>
                    <h3 class="fw-bold mb-2">Ulasan</h3>
                    <h6 class="op-7 mb-0">Kelola ulasan buku dari pengguna</h6>
                </div>

            </div>

            <?php if (isset($error)): ?>

                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Gagal mengambil data ulasan.
                    <br>
                    <small><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></small>
                </div>

            <?php endif; ?>

            <div class="row">

                <div class="col-sm-6 col-md-4">
                    <div class="card card-stats card-round">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                </div>

                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Ulasan</p>
                                        <h4 class="card-title"><?= $totalUlasan ?></h4>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="card card-stats card-round">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>

                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Rata-rata Rating</p>
                                        <h4 class="card-title">
                                            <?= $rataRating ?>
                                            <small>/ 5</small>
                                        </h4>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <div class="card card-round">

                <div class="card-header">

                    <div class="card-head-row">

                        <div class="card-title">
                            <i class="fas fa-star me-2"></i>
                            Data Ulasan
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>

                                <tr>
                                    <th>No</th>
                                    <th>Pengguna</th>
                                    <th>Buku</th>
                                    <th>Rating</th>
                                    <th>Ulasan</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($ulasan)): ?>

                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-comments fa-2x mb-2"></i>
                                                <br>
                                                Belum ada data ulasan.
                                            </div>
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($ulasan as $no => $item): ?>

                                        <tr>

                                            <td>
                                                <?= $no + 1 ?>
                                            </td>

                                            <td>

                                                <div class="d-flex align-items-center">

                                                    <div
                                                        class="avatar avatar-sm"
                                                        style="width:36px;height:36px;border-radius:50%;background:#f1f1f1;display:flex;align-items:center;justify-content:center;">

                                                        <i class="fas fa-user text-secondary"></i>

                                                    </div>

                                                    <span class="ms-2">
                                                        <?= htmlspecialchars(
                                                            $item['nama_pengguna'] ?? 'Pengguna',
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </span>

                                                </div>

                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $item['judul_buku'] ?? 'Buku tidak ditemukan',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>

                                                <?php
                                                $rating = (int) ($item['rating'] ?? 0);
                                                ?>

                                                <div class="text-warning">

                                                    <?php for ($i = 1; $i <= 5; $i++): ?>

                                                        <?php if ($i <= $rating): ?>

                                                            <i class="fas fa-star"></i>

                                                        <?php else: ?>

                                                            <i class="far fa-star"></i>

                                                        <?php endif; ?>

                                                    <?php endfor; ?>

                                                </div>

                                                <small class="text-muted">
                                                    <?= $rating ?>/5
                                                </small>

                                            </td>

                                            <td style="max-width:300px;">

                                                <?php
                                                $komentar = $item['komentar'] ?? '';
                                                ?>

                                                <?= htmlspecialchars(
                                                    $komentar ?: 'Tidak ada komentar',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </td>

                                            <td>
                                                <?= !empty($item['created_at'])
                                                    ? date('d-m-Y H:i', strtotime($item['created_at']))
                                                    : '-'
                                                ?>
                                            </td>

                                            <td>

                                                <a
                                                    href="hapus.php?id=<?= (int) $item['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus ulasan ini?');">

                                                    <i class="fas fa-trash"></i>

                                                </a>

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

    <?php require_once __DIR__ . '/../layout/footer.php'; ?>

</div>