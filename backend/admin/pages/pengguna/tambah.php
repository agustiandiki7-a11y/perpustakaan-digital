<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$backendPath = dirname(__DIR__, 2);
$pageTitle = 'Tambah Pengguna';
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
                        <h3 class="fw-bold mb-1">Tambah Pengguna Baru</h3>
                        <p class="text-muted mb-0">Daftarkan akun anggota atau peminjam baru ke dalam sistem</p>
                    </div>
                    <ul class="breadcrumbs ms-auto">
                        <li class="nav-home"><a href="../index.php"><i class="icon-home"></i></a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="tabel_pengguna.php">Data Pengguna</a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Tambah</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card card-round shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div class="card-title fw-bold text-primary m-0">
                                    <i class="fas fa-user-plus me-2"></i> Form Pendaftaran Peminjam
                                </div>
                            </div>

                            <form action="proses_tambah.php" method="POST">
                                <div class="card-body px-4 py-4">

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                                        <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username unik" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="contoh@domain.com" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                                    </div>

                                    <input type="hidden" name="role" value="peminjam">

                                </div>

                                <div class="card-action bg-light px-4 py-3 text-end rounded-bottom">
                                    <a href="tabel_pengguna.php" class="btn btn-secondary px-4 me-2">
                                        <i class="fas fa-arrow-left me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="fas fa-save me-1"></i> Simpan Akun
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