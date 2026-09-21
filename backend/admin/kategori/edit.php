<?php
require_once __DIR__ . '/../../../app/config/Database.php';
require_once __DIR__ . '/../../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = 'ID kategori tidak valid!';
    header('Location: tabel_kategori.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    $_SESSION['error'] = 'Data kategori tidak ditemukan di database!';
    header('Location: tabel_kategori.php');
    exit;
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <?php include __DIR__ . '/../../layout/header.php'; ?>
    <title>Edit Kategori - Backend Perpustakaan</title>
</head>

<body>
    <div class="wrapper">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        <div class="main-panel">
            <?php include __DIR__ . '/../../layout/navbar.php'; ?>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">Edit Kategori</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="tabel_kategori.php">Data Kategori</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Edit</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Form Edit Kategori Buku</div>
                                </div>
                                <form action="proses-edit.php" method="POST">
                                    <div class="card-body">
                                        <?php if ($error): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>

                                        <input type="hidden" name="id" value="<?= $category['id'] ?>">

                                        <div class="form-group">
                                            <label for="nama_kategori">Nama Kategori <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($category['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($category['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status Kategori <span class="text-danger">*</span></label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="aktif" <?= (($category['status'] ?? 'aktif') === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                                <option value="nonaktif" <?= (($category['status'] ?? 'aktif') === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" class="btn btn-primary">Perbarui Kategori</button>
                                        <a href="tabel_kategori.php" class="btn btn-danger">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include __DIR__ . '/../../layout/footer.php'; ?>
        </div>
    </div>
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>

</html>