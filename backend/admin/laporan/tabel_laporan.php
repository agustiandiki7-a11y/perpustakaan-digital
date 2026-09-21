<?php
require_once __DIR__ . '/../../../app/config/Database.php';
require_once __DIR__ . '/../../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();



try {
    $sql = "
SELECT
    l.id,
    l.kode_peminjaman,
    l.tanggal_pinjam,
    l.tanggal_kembali,
    l.status,
    l.denda,
    u.nama,
    u.username
FROM loans l
INNER JOIN users u ON l.user_id = u.id
ORDER BY l.id DESC
";

    $stmt = $pdo->query($sql);
    $laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $laporan = [];
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laporan Perpustakaan</title>

    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/plugins.min.css">
    <link rel="stylesheet" href="../../assets/css/kaiadmin.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

    <div class="wrapper">

        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <?php include __DIR__ . '/../../layout/navbar.php'; ?>

        <div class="main-panel">
            <div class="container">
                <div class="page-inner">

                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Laporan Perpustakaan</h3>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title">Laporan Peminjaman</h4>

                                <div>
                                    <a href="cetak.php" target="_blank" class="btn btn-primary">
                                        Cetak
                                    </a>

                                    <a href="export.php" class="btn btn-success">
                                        Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?= e($error) ?>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table id="tabelLaporan" class="table table-striped table-hover">

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Buku</th>
                                            <th>Judul Buku</th>
                                            <th>Peminjam</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Kembali</th>
                                            <th>Status</th>
                                            <th>Denda</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php $no = 1; ?>

                                        <?php foreach ($laporan as $row): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= e($row['kode_peminjaman']) ?></td>
                                                <td>-</td>
                                                <td><?= e($row['nama']) ?></td>
                                                <td><?= e($row['tanggal_pinjam']) ?></td>
                                                <td><?= e($row['tanggal_kembali']) ?></td>
                                                <td><?= e($row['status']) ?></td>
                                                <td>Rp <?= number_format((float)$row['denda'], 0, ',', '.') ?></td>
                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <?php include __DIR__ . '/../../layout/footer.php'; ?>

        </div>
    </div>

    <script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugin/datatables/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabelLaporan').DataTable();
        });
    </script>

</body>

</html>