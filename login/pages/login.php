
<!DOCTYPE html>
<html lang="id">

<body>
<!-- Head -->
<?php include '../partials/head.php'; ?>
<!-- Head -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="login-card p-4 p-md-5">
                    
                    <!-- Icon Header -->
                    <div class="login-header-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>

                    <!-- Title -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark mb-1">Perpustakaan Digital</h3>
                        <p class="text-muted small">Silakan masuk menggunakan akun terdaftar</p>
                    </div>

                    <!-- Alert Error jika ada -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2 small mb-3" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                            <button type="button" class="btn-close btn-sm py-3" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Form Login (Arahkan ke file proses login kamu, misal: proses_login.php) -->
                    <form action="../function/proses_login.php" method="POST">
                        
                        <!-- CSRF Token Keamanan -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                        <!-- Input Username -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Username</label>
                            <div class="input-group-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Password</label>
                            <div class="input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-sign-in-alt me-2"></i> Masuk ke Sistem
                            </button>
                        </div>

                    </form>

                    <!-- Kembali ke Beranda -->
                   
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>