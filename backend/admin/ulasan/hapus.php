<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

$id = $_GET['id'] ?? null;

if (empty($id) || !is_numeric($id)) {
    $_SESSION['error'] = 'ID ulasan tidak valid.';
    header('Location: tabel_ulasan.php');
    exit;
}

$database = new Database();
$pdo = $database->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM ulasan WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $_SESSION['success'] = 'Ulasan berhasil dihapus!';
    header('Location: tabel_ulasan.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal menghapus ulasan: ' . $e->getMessage();
    header('Location: tabel_ulasan.php');
    exit;
}