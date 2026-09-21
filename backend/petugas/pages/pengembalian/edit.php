<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$db = $database->connect();

// Ambil ID Peminjaman dari URL
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $_SESSION['error'] = 'ID Peminjaman tidak valid.';
    header('Location: tabel_pengembalian.php');
    exit;
}

// Proses form ketika disubmit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_kembali = $_POST['tanggal_kembali'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'dikembalikan';
    $catatan = trim($_POST['catatan'] ?? '');
    $denda_manual = (float) ($_POST['denda_manual'] ?? 0); // Jika petugas ingin input manual

    try {
        $db->beginTransaction();

        // 1. Ambil data peminjaman & jatuh tempo asli
        $stmtCheck = $db->prepare("SELECT tanggal_jatuh_tempo FROM loans WHERE id = :id");
        $stmtCheck->execute([':id' => $id]);
        $loanData = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$loanData) {
            throw new Exception("Data peminjaman tidak ditemukan.");
        }

        $jatuhTempo = new DateTime($loanData['tanggal_jatuh_tempo']);
        $tglKembali = new DateTime($tanggal_kembali);

        // 2. Hitung keterlambatan otomatis (dalam hari)
        $denda_otomatis = 0;
        if ($tglKembali > $jatuhTempo) {
            $selisihHari = $tglKembali->diff($jatuhTempo)->days;
            $tarifDendaPerHari = 1000; // Atur tarif denda per hari (Contoh: Rp 1.000 / hari)
            $denda_otomatis = $selisihHari * $tarifDendaPerHari;
            $status = 'terlambat'; // Ubah status otomatis jadi terlambat jika lewat tempo
        }

        // Pilih denda mana yang akan dipakai (prioritaskan input manual jika diisi, atau otomatis)
        $total_denda = ($denda_manual > 0) ? $denda_manual : $denda_otomatis;

        // 3. Update tabel loans
        $stmtLoan = $db->prepare("
            UPDATE loans 
            SET tanggal_kembali = :tanggal_kembali,
                status = :status,
                catatan = :catatan
            WHERE id = :id
        ");
        $stmtLoan->execute([
            ':tanggal_kembali' => $tanggal_kembali,
            ':status' => $status,
            ':catatan' => $catatan,
            ':id' => $id
        ]);

        // 4. Update denda ke tabel loan_details (di-spread atau ke item pertama)
        $stmtDetail = $db->prepare("
            UPDATE loan_details 
            SET denda = :denda 
            WHERE loan_id = :loan_id
        ");
        $stmtDetail->execute([
            ':denda' => $total_denda,
            ':loan_id' => $id
        ]);

        // 5. Jika status berubah menjadi dikembalikan, kembalikan stok buku
        if ($status === 'dikembalikan') {
            // Ambil daftar buku yang dipinjam beserta jumlahnya
            $stmtBooks = $db->prepare("SELECT book_id, jumlah FROM loan_details WHERE loan_id = :loan_id");
            $stmtBooks->execute([':loan_id' => $id]);
            $items = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $stmtStock = $db->prepare("UPDATE books SET stok = stok + :jumlah WHERE id = :book_id");
                $stmtStock->execute([
                    ':jumlah' => $item['jumlah'],
                    ':book_id' => $item['book_id']
                ]);
            }
        }

        $db->commit();
        $_SESSION['success'] = 'Data pengembalian dan denda berhasil diperbarui!';
        header('Location: tabel_pengembalian.php');
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        $error = 'Gagal memperbarui data: ' . $e->getMessage();
    }
}

// Ambil data transaksi untuk ditampilkan ke form
$stmt = $db->prepare("
    SELECT 
        loans.*, 
        users.nama AS nama_peminjam, 
        users.username AS username_peminjam,
        GROUP_CONCAT(books.judul SEPARATOR ', ') AS daftar_buku,
        SUM(COALESCE(loan_details.denda, 0)) AS total_denda
    FROM loans
    LEFT JOIN users ON users.id = loans.user_id
    LEFT JOIN loan_details ON loan_details.loan_id = loans.id
    LEFT JOIN books ON books.id = loan_details.book_id
    WHERE loans.id = :id
    GROUP BY loans.id
");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    $_SESSION['error'] = 'Data transaksi tidak ditemukan.';
    header('Location: tabel_pengembalian.php');
    exit;
}

// Path absolut otomatis menuju folder backend
$rootPath = dirname(__DIR__, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include $rootPath . '/layout/header.php'; ?>
    <title>Proses Pengembalian & Denda - Perpustakaan</title>
</head>
<body>
<div class="wrapper">
    <?php include $rootPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php include $rootPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="page-header">
                    <h3 class="fw-bold mb-3">Kelola Pengembalian & Denda</h3>
                    <ul class="breadcrumbs mb-3">
                        <li class="nav-home">
                            <a href="../index.php"><i class="icon-home"></i></a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="tabel_pengembalian.php">Pengembalian</a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Proses</li>
                    </ul>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Form Validasi Pengembalian Buku</div>
                            </div>
                            <form action="" method="POST">
                                <div class="card-body">
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Kode Peminjaman</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['kode_peminjaman']) ?>" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted">Nama Peminjam</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_peminjam']) ?> (@<?= htmlspecialchars($data['username_peminjam']) ?>)" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted">Daftar Buku yang Dipinjam</label>
                                        <textarea class="form-control" rows="2" readonly><<?= htmlspecialchars($data['daftar_buku']) ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">Tanggal Pinjam</label>
                                            <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($data['tanggal_pinjam'])) ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">Jatuh Tempo</label>
                                            <input type="text" class="form-control text-danger fw-bold" value="<?= date('d-m-Y', strtotime($data['tanggal_jatuh_tempo'])) ?>" readonly>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="mb-3">
                                        <label for="tanggal_kembali" class="form-label fw-bold">Tanggal Aktual Pengembalian <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" value="<?= $data['tanggal_kembali'] ?? date('Y-m-d') ?>" required>
                                        <small class="text-muted">Sistem akan otomatis menghitung denda jika tanggal kembali melewati jatuh tempo (Tarif: Rp 1.000/hari).</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label fw-bold">Status Transaksi <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="dipinjam" <?= ($data['status'] == 'dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
                                            <option value="dikembalikan" <?= ($data['status'] == 'dikembalikan') ? 'selected' : '' ?>>Dikembalikan (Tepat Waktu)</option>
                                            <option value="terlambat" <?= ($data['status'] == 'terlambat') ? 'selected' : '' ?>>Terlambat</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="denda_manual" class="form-label fw-bold">Nominal Denda (Opsional / Override)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="denda_manual" name="denda_manual" value="<?= (float)$data['total_denda'] ?>" placeholder="0">
                                        </div>
                                        <small class="text-muted">Kosongkan atau biarkan jika ingin sistem menghitung otomatis dari keterlambatan.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="catatan" class="form-label">Catatan Petugas</label>
                                        <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan kondisi buku (misal: ada halaman yang robek / aman)"><?= htmlspecialchars($data['catatan'] ?? '') ?></textarea>
                                    </div>

                                </div>
                                <div class="card-action text-end">
                                    <a href="tabel_pengembalian.php" class="btn btn-secondary me-2">Batal</a>
                                    <button type="submit" class="btn btn-success">Simpan Perubahan & Proses</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-stats">
                            <div class="card-body">
                                <h4 class="card-title text-info fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Denda</h4>
                                <p class="text-muted small">
                                    Denda dihitung otomatis berdasarkan selisih hari antara <b>Tanggal Jatuh Tempo</b> dan <b>Tanggal Kembali</b>.
                                </p>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Denda Saat Ini:</span>
                                    <span class="fw-bold text-danger">Rp <?= number_format($data['total_denda'], 0, ',', '.') ?></span>
                                </div>
                            </div>
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