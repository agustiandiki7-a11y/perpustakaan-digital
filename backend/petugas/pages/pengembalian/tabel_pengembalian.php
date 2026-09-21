<?php

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$db = $database->connect();

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);

$sql = "
    SELECT
        loans.id,
        loans.kode_peminjaman,
        loans.tanggal_pinjam,
        loans.tanggal_jatuh_tempo,
        loans.tanggal_kembali,
        loans.status,
        loans.catatan,
        users.nama AS nama_peminjam,
        users.username AS username_peminjam,
        GROUP_CONCAT(
            DISTINCT books.judul
            ORDER BY books.judul ASC
            SEPARATOR ', '
        ) AS daftar_buku,
        SUM(loan_details.jumlah) AS total_buku,
        SUM(COALESCE(loan_details.denda, 0)) AS total_denda
    FROM loans
    LEFT JOIN users ON users.id = loans.user_id
    LEFT JOIN loan_details ON loan_details.loan_id = loans.id
    LEFT JOIN books ON books.id = loan_details.book_id
    WHERE loans.status IN ('dipinjam', 'dikembalikan', 'terlambat')
    GROUP BY
        loans.id,
        loans.kode_peminjaman,
        loans.tanggal_pinjam,
        loans.tanggal_jatuh_tempo,
        loans.tanggal_kembali,
        loans.status,
        loans.catatan,
        users.nama,
        users.username
    ORDER BY loans.id DESC
";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $pengembalian = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pengembalian = [];
    $error = 'Gagal mengambil data pengembalian.';
}

function badgeStatusPengembalian(string $status): string
{
    switch ($status) {
        case 'dipinjam':
            return '<span class="badge bg-warning text-dark">Dipinjam</span>';

        case 'dikembalikan':
            return '<span class="badge bg-success">Dikembalikan</span>';

        case 'terlambat':
            return '<span class="badge bg-danger">Terlambat</span>';

        default:
            return '<span class="badge bg-secondary">' .
                htmlspecialchars($status, ENT_QUOTES, 'UTF-8') .
                '</span>';
    }
}

// Path absolut otomatis menuju folder backend
$rootPath = dirname(__DIR__, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include $rootPath . '/layout/header.php'; ?>
    <title>Data Pengembalian - Perpustakaan</title>
</head>
<body>
<div class="wrapper">
    <?php include $rootPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php include $rootPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Data Pengembalian</h3>
                        <p class="text-muted mb-0">
                            Kelola data pengembalian buku perpustakaan.
                        </p>
                    </div>

                    <a href="../peminjaman/tabel_peminjaman.php" class="btn btn-primary">
                        <i class="fas fa-exchange-alt me-1"></i>
                        Data Peminjaman
                    </a>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-undo me-2"></i>
                            Daftar Pengembalian
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th width="55">No</th>
                                        <th width="150">Kode</th>
                                        <th width="180">Peminjam</th>
                                        <th>Buku</th>
                                        <th width="120">Tgl. Pinjam</th>
                                        <th width="120">Jatuh Tempo</th>
                                        <th width="120">Tgl. Kembali</th>
                                        <th width="120">Status</th>
                                        <th width="100">Denda</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php if (empty($pengembalian)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="fas fa-undo-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data pengembalian</h5>
                                            <p class="text-muted mb-0">
                                                Data pengembalian akan muncul setelah transaksi peminjaman dibuat.
                                            </p>
                                        </td>
                                    </tr>
                                <?php else: ?>

                                    <?php foreach ($pengembalian as $no => $row): ?>
                                        <tr>
                                            <td><?= $no + 1 ?></td>

                                            <td>
                                                <strong>
                                                    <?= htmlspecialchars(
                                                        $row['kode_peminjaman'] ?? '-',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars(
                                                        $row['nama_peminjam'] ?? 'Tanpa Nama',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </div>
                                                <small class="text-muted">
                                                    @<?= htmlspecialchars(
                                                        $row['username_peminjam'] ?? '-',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </small>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $row['daftar_buku'] ?? '-',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                                <?php if (!empty($row['total_buku'])): ?>
                                                    <small class="text-muted d-block">
                                                        <?= (int) $row['total_buku'] ?> buku
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?= !empty($row['tanggal_pinjam'])
                                                    ? date('d-m-Y', strtotime($row['tanggal_pinjam']))
                                                    : '-' ?>
                                            </td>

                                            <td>
                                                <?= !empty($row['tanggal_jatuh_tempo'])
                                                    ? date('d-m-Y', strtotime($row['tanggal_jatuh_tempo']))
                                                    : '-' ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($row['tanggal_kembali'])): ?>
                                                    <?= date(
                                                        'd-m-Y',
                                                        strtotime($row['tanggal_kembali'])
                                                    ) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        Belum kembali
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?= badgeStatusPengembalian(
                                                    $row['status'] ?? ''
                                                ) ?>
                                            </td>

                                            <td>
                                                <?php
                                                $denda = (float) ($row['total_denda'] ?? 0);
                                                ?>

                                                <?php if ($denda > 0): ?>
                                                    <span class="text-danger fw-semibold">
                                                        Rp <?= number_format(
                                                            $denda,
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-success">
                                                        Rp 0
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a
                                                        href="detail.php?id=<?= (int) $row['id'] ?>"
                                                        class="btn btn-sm btn-info text-white"
                                                        title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <a
                                                        href="edit.php?id=<?= (int) $row['id'] ?>"
                                                        class="btn btn-sm btn-warning"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a
                                                        href="hapus.php?id=<?= (int) $row['id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus data pengembalian ini?');">
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

        <?php include $rootPath . '/layout/footer.php'; ?>
    </div>
</div>

</body>
</html>