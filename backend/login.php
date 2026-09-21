<?php
// Mulai session dengan pengaturan aman
session_start([
    'cookie_lifetime' => 0,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Generate CSRF Token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Perpustakaan Digital</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Efek cahaya latar belakang */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(37, 99, 235, 0.15);
            border-radius: 50%;
            filter: blur(80px);
            top: -100px;
            left: -100px;
            z-index: 0;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .login-header-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.5);
        }

        .form-control {
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            border-radius: 0.75rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 10;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 600;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 14px 20px -3px rgba(59, 130, 246, 0.5);
        }

        .back-link {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #1e293b;
        }
    </style>
</head>
<body>

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
                    <form action="proses_login.php" method="POST">
                        
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
                    <div class="text-center mt-4 pt-2 border-top">
                        <a href="../frontend/index.php" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda Utama
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>