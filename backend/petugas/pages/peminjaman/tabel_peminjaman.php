<?php

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();

/*
|--------------------------------------------------------------------------
| Hak akses
|--------------------------------------------------------------------------
| Hanya admin dan petugas yang boleh mengelola data peminjaman.
*/
cekRole(['admin', 'petugas']);

/*
|--------------------------------------------------------------------------
| Koneksi database
|--------------------------------------------------------------------------
*/
$database = new Database();
$db = $database->connect();

/*
|--------------------------------------------------------------------------
| CSRF Token
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

/*
|--------------------------------------------------------------------------
| Ambil data peminjaman
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

        u.id AS user_id,
        u.nama AS nama_peminjam,
        u.username,

        GROUP_CONCAT(
            CONCAT(b.judul, ' (', ld.jumlah, ')')
            ORDER BY b.judul
            SEPARATOR ', '
        ) AS daftar_buku

    FROM loans l

    INNER JOIN users u
        ON u.id = l.user_id

    LEFT JOIN loan_details ld
        ON ld.loan_id = l.id

    LEFT JOIN books b
        ON b.id = ld.book_id

    GROUP BY
        l.id,
        l.kode_peminjaman,
        l.tanggal_pengajuan,
        l.tanggal_pinjam,
        l.tanggal_jatuh_tempo,
        l.tanggal_kembali,
        l.status,
        l.catatan,
        u.id,
        u.nama,
        u.username

    ORDER BY l.id DESC
";

$stmt = $db->prepare($sql);
$stmt->execute();

$peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Fungsi badge status
|--------------------------------------------------------------------------
*/
function badgeStatus(string $status): string
{
    $status = strtolower($status);

    switch ($status) {
        case 'menunggu':
            return '<span class="badge bg-warning text-dark">Menunggu</span>';
        case 'disetujui':
            return '<span class="badge bg-info text-dark">Disetujui</span>';
        case 'dipinjam':
            return '<span class="badge bg-primary">Dipinjam</span>';
        case 'dikembalikan':
            return '<span class="badge bg-success">Dikembalikan</span>';
        case 'terlambat':
            return '<span class="badge bg-danger">Terlambat</span>';
        case 'ditolak':
            return '<span class="badge bg-secondary">Ditolak</span>';
        case 'dibatalkan':
            return '<span class="badge bg-dark">Dibatalkan</span>';
        default:
            return '<span class="badge bg-secondary">'
                . htmlspecialchars($status, ENT_QUOTES, 'UTF-8')
                . '</span>';
    }
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

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Data Peminjaman</h3>
                        <p class="text-muted mb-0">Kelola transaksi peminjaman buku perpustakaan.</p>
                    </div>
                    <div>
                        <a href="/perpustakaan/backend/peminjaman/tambah.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Peminjaman Baru
                        </a>
                    </div>
                </div>

                <!-- Notifikasi / Pesan Sukses -->
                <?php if (isset($_GET['pesan'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                            if ($_GET['pesan'] == 'sukses') echo "Data peminjaman berhasil diproses.";
                            elseif ($_GET['pesan'] == 'dibatalkan') echo "Peminjaman berhasil dibatalkan.";
                            else echo "Aksi berhasil dilakukan.";
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Daftar Peminjaman</div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Kode</th>
                                        <th>Peminjam</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Buku</th>
                                        <th width="130">Status</th>
                                        <th width="220">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($peminjaman)): ?>
                                    <tr>
                                        <!-- PERBAIKAN: Colspan disamakan dengan jumlah kolom (8 kolom) -->
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-book-open fa-2x text-muted mb-3"></i>
                                            <div class="text-muted">Belum ada data peminjaman.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($peminjaman as $no => $row): ?>
                                        <tr>
                                            <!-- No -->
                                            <td><?= $no + 1 ?></td>

                                            <!-- Kode -->
                                            <td>
                                                <strong><?= htmlspecialchars($row['kode_peminjaman'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
                                            </td>

                                            <!-- Peminjam -->
                                            <td>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars($row['nama_peminjam'] ?? 'Tanpa Nama', ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                                <small class="text-muted">
                                                    @<?= htmlspecialchars($row['username'] ?? 'username', ENT_QUOTES, 'UTF-8') ?>
                                                </small>
                                            </td>

                                            <!-- Tanggal Pinjam -->
                                            <td>
                                                <?= !empty($row['tanggal_pinjam']) ? date('d-m-Y', strtotime($row['tanggal_pinjam'])) : '<span class="text-muted">-</span>' ?>
                                            </td>

                                            <!-- Jatuh Tempo -->
                                            <td>
                                                <?= !empty($row['tanggal_jatuh_tempo']) ? date('d-m-Y', strtotime($row['tanggal_jatuh_tempo'])) : '<span class="text-muted">-</span>' ?>
                                            </td>

                                            <!-- Buku -->
                                            <td>
                                                <?= !empty($row['daftar_buku']) ? htmlspecialchars($row['daftar_buku'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">Tidak ada buku</span>' ?>
                                            </td>

                                            <!-- Status -->
                                            <td>
                                                <?= badgeStatus($row['status'] ?? 'menunggu') ?>
                                            </td>

                                            <!-- Aksi -->
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <!-- Detail -->
                                                    <a href="detail.php?id=<?= (int) $row['id'] ?>" class="btn btn-sm btn-info text-white" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <?php if (strtolower($row['status']) === 'menunggu'): ?>
                                                        <!-- Setujui -->
                                                        <form action="proses-status.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                            <input type="hidden" name="status" value="dipinjam">
                                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>

                                                        <!-- Batalkan -->
                                                        <!-- PERBAIKAN: Mengarah ke proses-status.php dengan status 'dibatalkan' agar seragam dengan fungsi proses status -->
                                                        <form action="proses-status.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                            <input type="hidden" name="status" value="dibatalkan">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Batalkan" onclick="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>

                                                    <?php elseif (strtolower($row['status']) === 'dipinjam'): ?>
                                                        <!-- Kembalikan -->
                                                        <form action="proses-status.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                            <input type="hidden" name="status" value="dikembalikan">
                                                            <button type="submit" class="btn btn-sm btn-warning" title="Kembalikan" onclick="return confirm('Tandai buku sebagai sudah dikembalikan?')">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
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

        <!-- Footer -->
        <?php include __DIR__ . '/../layout/footer.php'; ?>

    </div>

</div>

</body>
</html>