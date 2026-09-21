<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$pdo = $database->connect();

try {
    $stmt = $pdo->query("SELECT id, nama, email, role, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
}

$backendPath = dirname(__DIR__, 2);
$pageTitle = 'Manajemen Pengguna';
?>

<?php require_once $backendPath . '/layout/header.php'; ?>

<div class="wrapper">
    <?php require_once $backendPath . '/layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php require_once $backendPath . '/layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Manajemen Pengguna</h3>
                        <p class="text-muted mb-0">Kelola data akun anggota / peminjam perpustakaan</p>
                    </div>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Tambah Pengguna
                    </a>
                </div>

                <div class="card card-round shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title mb-1">Daftar Akun Peminjam</h4>
                        <p class="card-category mb-0">Semua akun peminjam yang terdaftar di dalam sistem</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th width="120">Role</th>
                                        <th width="150">Bergabung</th>
                                        <th width="130">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $no => $usr): ?>
                                            <tr>
                                                <td><?= $no + 1 ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2" style="width:32px;height:32px;border-radius:50%;background:#f1f1f1;display:flex;align-items:center;justify-content:center;">
                                                            <i class="fas fa-user text-secondary"></i>
                                                        </div>
                                                        <strong><?= htmlspecialchars($usr['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($usr['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <span class="badge bg-info text-dark text-uppercase"><?= htmlspecialchars($usr['role'], ENT_QUOTES, 'UTF-8') ?></span>
                                                </td>
                                                <td><?= !empty($usr['created_at']) ? date('d-m-Y', strtotime($usr['created_at'])) : '-' ?></td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="edit.php?id=<?= (int)$usr['id'] ?>" class="btn btn-sm btn-warning" title="Edit Akun">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="hapus.php?id=<?= (int)$usr['id'] ?>" class="btn btn-sm btn-danger" title="Hapus Akun" onclick="return confirm('Yakin ingin menghapus pengguna ini?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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