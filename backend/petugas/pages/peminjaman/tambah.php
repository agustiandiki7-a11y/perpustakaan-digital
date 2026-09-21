<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

/*
|--------------------------------------------------------------------------
| Koneksi database menggunakan class Database
|--------------------------------------------------------------------------
*/
$database = new Database();
$db = $database->connect();

// Ambil data buku untuk dropdown
$buku = $db->query("SELECT id, judul FROM books ORDER BY judul ASC")->fetchAll(PDO::FETCH_ASSOC);

// Ambil data user/peminjam untuk dropdown
$users = $db->query("SELECT id, nama, username FROM users ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
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
                        <h3 class="fw-bold mb-1">Tambah Peminjaman</h3>
                        <p class="text-muted mb-0">Buat transaksi peminjaman buku baru.</p>
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
                                <h5 class="mb-0">Form Peminjaman Buku Baru</h5>
                            </div>
                            <div class="card-body">
                                <form action="proses-tambah.php" method="POST">
                                    
                                    <div class="mb-3">
                                        <label for="user_id" class="form-label fw-bold">Peminjam</label>
                                        <select name="user_id" id="user_id" class="form-control" required>
                                            <option value="">-- Pilih Peminjam --</option>
                                            <?php foreach ($users as $u): ?>
                                                <option value="<?= $u['id']; ?>">
                                                    <?= htmlspecialchars($u['nama'], ENT_QUOTES, 'UTF-8'); ?> (@<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="book_id" class="form-label fw-bold">Buku</label>
                                        <select name="book_id" id="book_id" class="form-control" required>
                                            <option value="">-- Pilih Buku --</option>
                                            <?php foreach ($buku as $b): ?>
                                                <option value="<?= $b['id']; ?>">
                                                    <?= htmlspecialchars($b['judul'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tanggal_pinjam" class="form-label fw-bold">Tanggal Pinjam</label>
                                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tanggal_jatuh_tempo" class="form-label fw-bold">Tanggal Jatuh Tempo (Batas Kembali)</label>
                                        <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="catatan" class="form-label fw-bold">Catatan (Opsional)</label>
                                        <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="tabel_peminjaman.php" class="btn btn-secondary">Batal</a>
                                        <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
                                    </div>

                                </form>
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