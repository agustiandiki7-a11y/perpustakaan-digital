<?php

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);


$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id || $id <= 0) {

    header('Location: pengembalian.php');
    exit;
}


$database = new Database();
$db = $database->connect();


$sql = "
    SELECT
        l.*,

        u.nama AS nama_peminjam,
        u.username,
        u.email,
        u.no_hp,

        GROUP_CONCAT(
            CONCAT(
                b.judul,
                ' (Jumlah: ',
                ld.jumlah,
                ')'
            )
            ORDER BY b.judul
            SEPARATOR '<br>'
        ) AS daftar_buku

    FROM loans l

    INNER JOIN users u
        ON u.id = l.user_id

    LEFT JOIN loan_details ld
        ON ld.loan_id = l.id

    LEFT JOIN books b
        ON b.id = ld.book_id

    WHERE l.id = ?

    GROUP BY l.id
";


$stmt = $db->prepare($sql);
$stmt->execute([$id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$data) {

    $_SESSION['error'] =
        'Data pengembalian tidak ditemukan.';

    header('Location: pengembalian.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <?php include __DIR__ . '/../layout/header.php'; ?>

    <title>Detail Pengembalian - Perpustakaan</title>

</head>

<body>

<div class="wrapper">

    <?php include __DIR__ . '/../layout/sidebar.php'; ?>


    <div class="main-panel">

        <?php include __DIR__ . '/../layout/navbar.php'; ?>


        <div class="container">

            <div class="page-inner">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h3 class="fw-bold mb-1">
                            Detail Pengembalian
                        </h3>

                        <p class="text-muted mb-0">
                            Informasi lengkap transaksi pengembalian.
                        </p>

                    </div>

                    <a
                        href="pengembalian.php"
                        class="btn btn-secondary btn-sm">

                        <i class="fas fa-arrow-left me-1"></i>

                        Kembali

                    </a>

                </div>


                <div class="row justify-content-center">

                    <div class="col-md-8">

                        <div class="card shadow-sm">

                            <div class="card-header bg-primary text-white">

                                <h5 class="mb-0">

                                    Kode Transaksi:
                                    <?= htmlspecialchars(
                                        $data['kode_peminjaman'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </h5>

                            </div>


                            <div class="card-body">

                                <table class="table table-borderless">

                                    <tr>

                                        <th width="35%">
                                            Peminjam
                                        </th>

                                        <td>

                                            :
                                            <?= htmlspecialchars(
                                                $data['nama_peminjam'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                            <small class="text-muted">

                                                (@<?= htmlspecialchars(
                                                    $data['username'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>)

                                            </small>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Email
                                        </th>

                                        <td>

                                            :
                                            <?= htmlspecialchars(
                                                $data['email'] ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            No. HP
                                        </th>

                                        <td>

                                            :
                                            <?= htmlspecialchars(
                                                $data['no_hp'] ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Buku
                                        </th>

                                        <td>

                                            :
                                            <?= $data['daftar_buku'] ?? '-' ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Tanggal Pinjam
                                        </th>

                                        <td>

                                            :
                                            <?= !empty($data['tanggal_pinjam'])
                                                ? date(
                                                    'd-m-Y',
                                                    strtotime($data['tanggal_pinjam'])
                                                )
                                                : '-'
                                            ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Jatuh Tempo
                                        </th>

                                        <td>

                                            :
                                            <?= !empty($data['tanggal_jatuh_tempo'])
                                                ? date(
                                                    'd-m-Y',
                                                    strtotime($data['tanggal_jatuh_tempo'])
                                                )
                                                : '-'
                                            ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Tanggal Kembali
                                        </th>

                                        <td>

                                            :

                                            <?php if (!empty($data['tanggal_kembali'])): ?>

                                                <?= date(
                                                    'd-m-Y',
                                                    strtotime($data['tanggal_kembali'])
                                                ) ?>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Belum dikembalikan
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Status
                                        </th>

                                        <td>

                                            :
                                            <span class="badge bg-success">

                                                <?= htmlspecialchars(
                                                    $data['status'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Catatan
                                        </th>

                                        <td>

                                            :
                                            <?= htmlspecialchars(
                                                $data['catatan'] ?? 'Tidak ada catatan',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                    </tr>

                                </table>


                                <div class="mt-4 d-flex justify-content-end gap-2">

                                    <a
                                        href="edit.php?id=<?= (int) $data['id'] ?>"
                                        class="btn btn-warning">

                                        <i class="fas fa-edit me-1"></i>

                                        Edit

                                    </a>


                                    <a
                                        href="pengembalian.php"
                                        class="btn btn-secondary">

                                        Kembali

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <?php include __DIR__ . '/../layout/footer.php'; ?>

    </div>

</div>

</body>

</html>