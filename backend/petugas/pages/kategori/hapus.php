<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // 1. Cek apakah kategori masih dipakai oleh buku di tabel books
        $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM books WHERE category_id = ?");
        $stmtCek->execute([$id]);
        $jumlahBuku = $stmtCek->fetchColumn();

        if ($jumlahBuku > 0) {
            $_SESSION['error'] = 'Kategori tidak dapat dihapus karena masih digunakan oleh buku.';
        } else {
            // 2. Jika aman, lakukan hapus
            $stmtHapus = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmtHapus->execute([$id]);
            $_SESSION['success'] = 'Kategori berhasil dihapus!';
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus kategori: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID kategori tidak valid!';
}

header('Location: tabel_kategori.php');
exit;