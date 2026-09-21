<?php
require_once __DIR__ . '/../../../app/config/Database.php';
require_once __DIR__ . '/../../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$nama = $_SESSION['nama'] ?? 'Administrator';
$username = $_SESSION['username'] ?? 'admin';
$email = $_SESSION['email'] ?? 'admin@perpustakaan.com';
$role = $_SESSION['role'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>

    <link rel="stylesheet" href="/perpustakaan/backend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/perpustakaan/backend/assets/css/kaiadmin.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .profile-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,.08);
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #6861ce;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .badge-role {
            background: #6861ce;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
        }

        .btn-custom {
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="wrapper">

    <?php include __DIR__ . '/../../layout/sidebar.php'; ?>

    <div class="main-panel">

        <?php include __DIR__ . '/../../layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="page-header">
                    <h3 class="fw-bold mb-3">
                        <i class="fas fa-user-circle"></i>
                        Profil Saya
                    </h3>
                </div>

                <div class="row">

                    <div class="col-md-4">

                        <div class="card profile-card">
                            <div class="card-body text-center">

                                <img
                                    src="/perpustakaan/backend/assets/img/profile.jpg"
                                    class="profile-img mb-3"
                                    onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&size=150';">

                                <h3><?= htmlspecialchars($nama) ?></h3>

                                <span class="badge-role">
                                    <?= ucfirst(htmlspecialchars($role)) ?>
                                </span>

                                <hr>

                                <p>
                                    <i class="fas fa-user"></i>
                                    <?= htmlspecialchars($username) ?>
                                </p>

                                <p>
                                    <i class="fas fa-envelope"></i>
                                    <?= htmlspecialchars($email) ?>
                                </p>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-8">

                        <div class="card profile-card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Informasi Akun
                                </h4>
                            </div>

                            <div class="card-body">

                                <div class="info-item">
                                    <strong>Nama Lengkap</strong>
                                    <br>
                                    <?= htmlspecialchars($nama) ?>
                                </div>

                                <div class="info-item">
                                    <strong>Username</strong>
                                    <br>
                                    <?= htmlspecialchars($username) ?>
                                </div>

                                <div class="info-item">
                                    <strong>Email</strong>
                                    <br>
                                    <?= htmlspecialchars($email) ?>
                                </div>

                                <div class="info-item">
                                    <strong>Role</strong>
                                    <br>
                                    <?= ucfirst(htmlspecialchars($role)) ?>
                                </div>

                                <div class="mt-4">

                                    <button class="btn btn-primary btn-custom">
                                        <i class="fas fa-edit"></i>
                                        Edit Profil
                                    </button>

                                    <button class="btn btn-warning btn-custom">
                                        <i class="fas fa-key"></i>
                                        Ubah Password
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        <?php include __DIR__ . '/../../layout/footer.php'; ?>

    </div>

</div>

<script src="/perpustakaan/backend/assets/js/core/jquery-3.7.1.min.js"></script>
<script src="/perpustakaan/backend/assets/js/core/bootstrap.min.js"></script>
<script src="/perpustakaan/backend/assets/js/kaiadmin.min.js"></script>

</body>
</html> 