<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (empty($id) || !is_numeric($id)) {
    header('Location: tabel_ulasan.php');
    exit;
}

$database = new Database();
$pdo = $database->connect();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

// Proses Simpan Perubahan (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating   = (int)($_POST['rating'] ?? 0);
    $komentar = trim($_POST['komentar'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = 'Rating harus diisi antara angka 1 sampai 5!';
        header("Location: edit.php?id=$id");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE ulasan SET rating = :rating, komentar = :komentar WHERE id = :id");
        $stmt->execute([
            ':rating'   => $rating,
            ':komentar' => empty($komentar) ? null : $komentar,
            ':id'       => $id
        ]);

        $_SESSION['success'] = 'Ulasan berhasil diperbarui!';
        header('Location: tabel_ulasan.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal memperbarui ulasan: ' . $e->getMessage();
        header("Location: edit.php?id=$id");
        exit;
    }
}

// Ambil Data Ulasan Berdasarkan ID
try {
    $stmt = $pdo->prepare("
        SELECT u.*, us.nama AS nama_pengguna, b.judul AS judul_buku 
        FROM ulasan u
        LEFT JOIN users us ON us.id = u.user_id
        LEFT JOIN books b ON b.id = u.book_id
        WHERE u.id = :id
    ");
    $stmt->execute([':id' => $id]);
    $ulasan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ulasan) {
        header('Location: tabel_ulasan.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: tabel_ulasan.php');
    exit;
}

$backendPath = dirname(__DIR__, 2);
$pageTitle = 'Edit Ulasan';
?>

<?php require_once $backendPath . '/layout/header.php'; ?>

<div class="wrapper">
    <?php require_once $backendPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php require_once $backendPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="page-header mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Edit Ulasan Buku</h3>
                        <p class="text-muted mb-0">Ubah rating atau komentar ulasan pengguna</p>
                    </div>
                    <ul class="breadcrumbs ms-auto">
                        <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="tabel_ulasan.php">Data Ulasan</a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Edit</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card card-round shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div class="card-title fw-bold text-primary m-0">
                                    <i class="fas fa-edit me-2"></i> Form Ubah Data Ulasan
                                </div>
                            </div>

                            <form action="edit.php" method="POST">
                                <input type="hidden" name="id" value="<?= (int)$ulasan['id'] ?>">

                                <div class="card-body px-4 py-4">

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pengguna</label>
                                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($ulasan['nama_pengguna'] ?? '-', ENT_QUOTES, 'UTF-8') ?>" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Judul Buku</label>
                                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($ulasan['judul_buku'] ?? '-', ENT_QUOTES, 'UTF-8') ?>" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="rating" class="form-label fw-bold">Rating (1 - 5) <span class="text-danger">*</span></label>
                                        <select name="rating" id="rating" class="form-select" required>
                                            <option value="5" <?= ((int)$ulasan['rating'] === 5) ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 - Sangat Baik)</option>
                                            <option value="4" <?= ((int)$ulasan['rating'] === 4) ? 'selected' : '' ?>>⭐⭐⭐⭐ (4 - Baik)</option>
                                            <option value="3" <?= ((int)$ulasan['rating'] === 3) ? 'selected' : '' ?>>⭐⭐⭐ (3 - Cukup)</option>
                                            <option value="2" <?= ((int)$ulasan['rating'] === 2) ? 'selected' : '' ?>>⭐⭐ (2 - Kurang)</option>
                                            <option value="1" <?= ((int)$ulasan['rating'] === 1) ? 'selected' : '' ?>>⭐ (1 - Sangat Kurang)</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="komentar" class="form-label fw-bold">Komentar / Ulasan</label>
                                        <textarea name="komentar" id="komentar" class="form-control" rows="4" placeholder="Tulis komentar ulasan..."><?= htmlspecialchars($ulasan['komentar'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                    </div>

                                </div>

                                <div class="card-action bg-light px-4 py-3 text-end rounded-bottom">
                                    <a href="tabel_ulasan.php" class="btn btn-secondary px-4 me-2">
                                        <i class="fas fa-arrow-left me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="fas fa-save me-1"></i> Perbarui Ulasan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php require_once $backendPath . '/layout/footer.php'; ?>
    </div>
</div>
</body>
</html>