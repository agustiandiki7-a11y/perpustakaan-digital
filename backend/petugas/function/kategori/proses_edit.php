<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $deskripsi     = trim($_POST['deskripsi'] ?? '');
    $status        = trim($_POST['status'] ?? 'aktif');

    if ($id <= 0 || empty($nama_kategori)) {
        $_SESSION['error'] = 'Data tidak valid atau nama kategori kosong!';
        header('Location: tabel_kategori.php');
        exit;
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama_kategori)));

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // Cek duplikat nama/slug kecuali untuk ID yang sedang diedit
        $stmtCek = $pdo->prepare("SELECT id FROM categories WHERE (nama_kategori = ? OR slug = ?) AND id != ?");
        $stmtCek->execute([$nama_kategori, $slug, $id]);
        if ($stmtCek->rowCount() > 0) {
            $_SESSION['error'] = 'Nama kategori atau slug sudah digunakan oleh kategori lain!';
            header('Location: edit.php?id=' . $id);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE categories SET nama_kategori = ?, slug = ?, deskripsi = ?, status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$nama_kategori, $slug, empty($deskripsi) ? null : $deskripsi, $status, $id]);

        $_SESSION['success'] = 'Kategori berhasil diperbarui!';
        header('Location: tabel_kategori.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Kesalahan database: ' . $e->getMessage();
        header('Location: edit.php?id=' . $id);
        exit;
    }
} else {
    header('Location: tabel_kategori.php');
    exit;
}