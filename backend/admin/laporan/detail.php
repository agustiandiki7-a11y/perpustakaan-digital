<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

function laporanDetailEsc($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $sql = "
        SELECT
            l.id,
            l.tanggal_pinjam,
            l.tanggal_kembali,
            l.status,
            l.denda,
            u.nama AS nama_peminjam,
            u.username,
            u.email,
            u.no_hp,
            u.alamat,
            b.kode_buku,
            b.judul,
            b.penulis,
            b.penerbit,
            b.tahun_terbit
        FROM loans l
        INNER JOIN users u ON l.user_id = u.id
        INNER JOIN books b ON l.book_id = b.id
        WHERE l.id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die('Gagal mengambil detail laporan.');
}

$status = strtolower(trim($data['status'] ?? ''));

switch ($status) {
    case 'menunggu':
        $badge = 'warning';
        $statusText = 'Menunggu';
        break;
    case 'dipinjam':
        $badge = 'primary';
        $statusText = 'Dipinjam';
        break;
    case 'dikembalikan':
        $badge = 'success';
        $statusText = 'Dikembalikan';
        break;
    case 'terlambat':
        $badge = 'danger';
        $statusText = 'Terlambat';
        break;
    case 'dibatalkan':
        $badge = 'secondary';
        $statusText = 'Dibatalkan';
        break;
    default:
        $badge = 'secondary';
        $statusText = ucfirst($status);
        break;
}

$denda = (float)($data['denda'] ?? 0);

$tanggalPinjam = !empty($data['tanggal_pinjam'])
    ? date('d-m-Y', strtotime($data['tanggal_pinjam']))
    : '-';

$tanggalKembali = !empty($data['tanggal_kembali'])
    ? date('d-m-Y', strtotime($data['tanggal_kembali']))
    : '-';
?>

<div class="page-header">
    <h3 class="fw-bold mb-3">Detail Laporan</h3>

    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="../index.php">
                <i class="icon-home"></i>
            </a>
        </li>

        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>

        <li class="nav-item">
            <a href="index.php">Laporan</a>
        </li>

        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>

        <li class="nav-item">
            <a href="#">Detail</a>
        </li>
    </ul>
</div>

<div class="row">

    <div class="col-md-12">

        <div class="card">

            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div>
                        <h4 class="card-title mb-1">
                            Detail Peminjaman
                        </h4>

                        <p class="text-muted mb-0">
                            Informasi lengkap transaksi
                        </p>
                    </div>

                    <div class="d-flex gap-2">

                        <a href="cetak_detail.php?id=<?= (int)$data['id']; ?>"
                           target="_blank"
                           class="btn btn-primary">
                            <i class="fa fa-print me-1"></i>
                            Cetak
                        </a>

                        <a href="index.php"
                           class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Kembali
                        </a>

                    </div>

                </div>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="fa fa-user me-2"></i>
                                    Data Peminjam
                                </h4>
                            </div>

                            <div class="card-body">

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Nama</div>
                                    <div class="col-sm-8 fw-semibold">
                                        <?= laporanDetailEsc($data['nama_peminjam']); ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Username</div>
                                    <div class="col-sm-8">
                                        <?= laporanDetailEsc($data['username']); ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Email</div>
                                    <div class="col-sm-8">
                                        <?= !empty($data['email'])
                                            ? laporanDetailEsc($data['email'])
                                            : '-'; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">No. HP</div>
                                    <div class="col-sm-8">
                                        <?= !empty($data['no_hp'])
                                            ? laporanDetailEsc($data['no_hp'])
                                            : '-'; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4 text-muted">Alamat</div>
                                    <div class="col-sm-8">
                                        <?= !empty($data['alamat'])
                                            ? nl2br(laporanDetailEsc($data['alamat']))
                                            : '-'; ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="fa fa-book me-2"></i>
                                    Data Buku
                                </h4>
                            </div>

                            <div class="card-body">

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Kode Buku</div>
                                    <div class="col-sm-8 fw-semibold">
                                        <?= laporanDetailEsc($data['kode_buku']); ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Judul</div>
                                    <div class="col-sm-8 fw-semibold">
                                        <?= laporanDetailEsc($data['judul']); ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Penulis</div>
                                    <div class="col-sm-8">
                                        <?= !empty($data['penulis'])
                                            ? laporanDetailEsc($data['penulis'])
                                            : '-'; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Penerbit</div>
                                    <div class="col-sm-8">
                                        <?= !empty($data['penerbit'])
                                            ? laporanDetailEsc($data['penerbit'])
                                            : '-'; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4 text-muted">Tahun</div>
                                    <div class="col-sm-8">
                                        <?= !empty($data['tahun_terbit'])
                                            ? laporanDetailEsc($data['tahun_terbit'])
                                            : '-'; ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                <div class="card mt-3">

                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fa fa-exchange-alt me-2"></i>
                            Informasi Peminjaman
                        </h4>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <small class="text-muted d-block">
                                        ID Transaksi
                                    </small>
                                    <h5 class="mb-0">
                                        #<?= (int)$data['id']; ?>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <small class="text-muted d-block">
                                        Tanggal Pinjam
                                    </small>
                                    <h5 class="mb-0">
                                        <?= laporanDetailEsc($tanggalPinjam); ?>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <small class="text-muted d-block">
                                        Tanggal Kembali
                                    </small>
                                    <h5 class="mb-0">
                                        <?= laporanDetailEsc($tanggalKembali); ?>
                                    </h5>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted d-block mb-2">
                                        Status
                                    </small>

                                    <span class="badge bg-<?= laporanDetailEsc($badge); ?> fs-6">
                                        <?= laporanDetailEsc($statusText); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted d-block mb-2">
                                        Denda
                                    </small>

                                    <h5 class="<?= $denda > 0 ? 'text-danger' : 'text-success'; ?> mb-0">
                                        Rp <?= number_format($denda, 0, ',', '.'); ?>
                                    </h5>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>