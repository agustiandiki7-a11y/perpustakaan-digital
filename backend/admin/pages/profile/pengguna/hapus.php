<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

$id = $_GET['id'] ?? null;

if (empty($id) || !is_numeric($id)) {
    $_SESSION['error'] = 'ID pengguna tidak valid.';
    header('Location: tabel_pengguna.php');
    exit;
}

if ((int)$id === (int)($_SESSION['user_id'] ?? 0)) {
    $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri!';
    header('Location: tabel_pengguna.php');
    exit;
}

$database = new Database();
$pdo = $database->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $_SESSION['success'] = 'Akun pengguna berhasil dihapus!';
    header('Location: tabel_pengguna.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal menghapus pengguna: ' . $e->getMessage();
    header('Location: tabel_pengguna.php');
    exit;
}