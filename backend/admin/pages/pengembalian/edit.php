<?php

require_once __DIR__ . '/../../../app/config/Database.php';
require_once __DIR__ . '/../../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {

    $_SESSION['error'] = 'ID pengembalian tidak valid.';

    header('Location: pengembalian.php');
    exit;
}


$database = new Database();
$db = $database->connect();


/*
|--------------------------------------------------------------------------
| Ambil data transaksi
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        l.*,
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

    WHERE l.id = ?

    GROUP BY l.id
";

$stmt = $db->prepare($sql);
$stmt->execute([$id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$data) {

    $_SESSION['error'] = 'Data pengembalian tidak ditemukan.';

    header('Location: pengembalian.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Status yang tersedia
|--------------------------------------------------------------------------
*/
$statusList = [
    'dipinjam',
    'dikembalikan',
    'terlambat'
];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <?php include __DIR__ . '/../../layout/header.php'; ?>

    <title>Edit Pengembalian - Perpustakaan</title>

</head>

<body>

<div class="wrapper">

    <?php include __DIR__ . '/../../layout/sidebar.php'; ?>


    <div class="main-panel">

        <?php include __DIR__ . '/../../layout/navbar.php'; ?>


        <div class="container">

            <div class="page-inner">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h3 class="fw-bold mb-1">
                            Edit Pengembalian
                        </h3>

                        <p class="text-muted mb-0">
                            Perbarui data pengembalian buku.
                        </p>

                    </div>

                    <a
                        href="tabel_pengembalian.php"
                        class="btn btn-secondary btn-sm">

                        <i class="fas fa-arrow-left me-1"></i>

                        Kembali

                    </a>

                </div>


                <div class="row justify-content-center">

                    <div class="col-md-8">

                        <div class="card shadow-sm">

                            <div class="card-header">

                                <h5 class="mb-0">
                                    Edit Data Pengembalian
                                </h5>

                            </div>


                            <div class="card-body">

                                <form
                                    action="proses_edit.php"
                                    method="POST">


                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $data['id'] ?>">


                                    <!-- KODE -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Kode Peminjaman
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
      ``                                      value="<?= htmlspecialchars(
                                                $data['kode_peminjaman'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            readonly>

                                    </div>


                                    <!-- PEMINJAM -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Peminjam
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $data['nama_peminjam'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?> (@<?= htmlspecialchars(
                                                $data['username'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>)"
                                            readonly>

                                    </div>


                                    <!-- BUKU -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Buku
                                        </label>

                                        <textarea
                                            class="form-control"
                                            rows="3"
                                            readonly><?= htmlspecialchars(
                                                $data['daftar_buku'] ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?></textarea>

                                    </div>


                                    <!-- TANGGAL PINJAM -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Tanggal Pinjam
                                        </label>

                                        <input
                                            type="date"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $data['tanggal_pinjam'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            readonly>

                                    </div>


                                    <!-- JATUH TEMPO -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Jatuh Tempo
                                        </label>

                                        <input
                                            type="date"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $data['tanggal_jatuh_tempo'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            readonly>

                                    </div>


                                    <!-- TANGGAL KEMBALI -->
                                    <div class="mb-3">

                                        <label
                                            for="tanggal_kembali"
                                            class="form-label fw-bold">

                                            Tanggal Kembali

                                        </label>

                                        <input
                                            type="date"
                                            name="tanggal_kembali"
                                            id="tanggal_kembali"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $data['tanggal_kembali'] ?? date('Y-m-d'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            required>

                                    </div>


                                    <!-- STATUS -->
                                    <div class="mb-3">

                                        <label
                                            for="status"
                                            class="form-label fw-bold">

                                            Status

                                        </label>

                                        <select
                                            name="status"
                                            id="status"
                                            class="form-control"
                                            required>

                                            <?php foreach ($statusList as $status): ?>

                                                <option
                                                    value="<?= $status ?>"
                                                    <?= strtolower($data['status']) === $status
                                                        ? 'selected'
                                                        : ''
                                                    ?>>

                                                    <?= ucfirst($status) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </div>


                                    <!-- CATATAN -->
                                    <div class="mb-3">

                                        <label
                                            for="catatan"
                                            class="form-label fw-bold">

                                            Catatan

                                        </label>

                                        <textarea
                                            name="catatan"
                                            id="catatan"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Catatan pengembalian..."><?= htmlspecialchars(
                                                $data['catatan'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?></textarea>

                                    </div>


                                    <div class="d-flex justify-content-between">

                                        <a href="tabel_pengembalian.php"class="btn btn-secondary">
                                            Batal
                                        </a>
                                        <button type="submit"class="btn btn-primary">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include __DIR__ . '/../../layout/footer.php'; ?>
    </div>
</div>
</body>
</html>