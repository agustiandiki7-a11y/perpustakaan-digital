<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

// Ambil ID dari URL
$id = $_GET['id'] ?? 0;

if (!$id) {
    header("Location: tabel_peminjaman.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Koneksi database menggunakan class Database
|--------------------------------------------------------------------------
*/
$database = new Database();
$db = $database->connect();

/*
|--------------------------------------------------------------------------
| Ambil data detail peminjaman berdasarkan ID
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        l.id,
        l.kode_peminjaman,
        l.tanggal_pengajuan,
        l.tanggal_pinjam,
        l.tanggal_jatuh_tempo,
        l.tanggal_kembali,
        l.status,
        l.catatan,

        u.nama AS nama_peminjam,
        u.username,
        u.email,

        GROUP_CONCAT(
            CONCAT(b.judul, ' (Jumlah: ', ld.jumlah, ')')
            ORDER BY b.judul
            SEPARATOR '<br>'
        ) AS daftar_buku

    FROM loans l
    INNER JOIN users u ON u.id = l.user_id
    LEFT JOIN loan_details ld ON ld.loan_id = l.id
    LEFT JOIN books b ON b.id = ld.book_id
    WHERE l.id = ?
    GROUP BY l.id
";

$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data peminjaman tidak ditemukan!'); window.location.href='tabel_peminjaman.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <?php include __DIR__ . '/../layout/header.php'; ?>
</head>

<body>

<div class="wrapper">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-panel">
        <!-- Navbar -->
        <?php include __DIR__ . '/../layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Detail Peminjaman</h3>
                        <p class="text-muted mb-0">Informasi lengkap transaksi peminjaman buku.</p>
                    </div>
                    <div>
                        <a href="tabel_peminjaman.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Kode Transaksi: <?= htmlspecialchars($data['kode_peminjaman'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless align-middle">
                                    <tr>
                                        <th width="35%">Nama Peminjam</th>
                                        <td>: <?= htmlspecialchars($data['nama_peminjam'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> (@<?= htmlspecialchars($data['username'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>)</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>: <?= htmlspecialchars($data['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Daftar Buku</th>
                                        <td>: <div><?= $data['daftar_buku'] ?? '<span class="text-muted">Tidak ada buku</span>'; ?></div></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pengajuan</th>
                                        <td>: <?= !empty($data['tanggal_pengajuan']) ? date('d-m-Y', strtotime($data['tanggal_pengajuan'])) : '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pinjam</th>
                                        <td>: <?= !empty($data['tanggal_pinjam']) ? date('d-m-Y', strtotime($data['tanggal_pinjam'])) : '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Jatuh Tempo</th>
                                        <td>: <?= !empty($data['tanggal_jatuh_tempo']) ? date('d-m-Y', strtotime($data['tanggal_jatuh_tempo'])) : '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Kembali</th>
                                        <td>: <?= !empty($data['tanggal_kembali']) ? date('d-m-Y', strtotime($data['tanggal_kembali'])) : '<span class="text-muted">Belum dikembalikan</span>'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>: <span class="badge bg-info text-dark text-uppercase"><?= htmlspecialchars($data['status'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    </tr>
                                    <tr>
                                        <th>Catatan</th>
                                        <td>: <?= htmlspecialchars($data['catatan'] ?? 'Tidak ada catatan', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                </table>

                                <div class="mt-4 text-end">
                                    <a href="tabel_peminjaman.php" class="btn btn-primary">Tutup / Kembali</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <?php include __DIR__ . '/../layout/footer.php'; ?>
    </div>
</div>

</body>
</html>