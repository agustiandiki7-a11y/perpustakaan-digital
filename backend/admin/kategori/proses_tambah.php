<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $deskripsi     = trim($_POST['deskripsi'] ?? '');
    $status        = trim($_POST['status'] ?? 'aktif');

    if (empty($nama_kategori)) {
        $_SESSION['error'] = 'Nama kategori wajib diisi!';
        header('Location: tambah.php');
        exit;
    }

    // Buat slug otomatis dari nama kategori
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama_kategori)));

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // Cek apakah nama kategori atau slug sudah ada (duplikat)
        $stmtCek = $pdo->prepare("SELECT id FROM categories WHERE nama_kategori = ? OR slug = ?");
        $stmtCek->execute([$nama_kategori, $slug]);
        if ($stmtCek->rowCount() > 0) {
            $_SESSION['error'] = 'Nama kategori atau slug tersebut sudah terdaftar. Gunakan nama lain!';
            header('Location: tambah.php');
            exit;
        }

        // Insert ke database
        $stmt = $pdo->prepare("INSERT INTO categories (nama_kategori, slug, deskripsi, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$nama_kategori, $slug, empty($deskripsi) ? null : $deskripsi, $status]);

        $_SESSION['success'] = 'Kategori baru berhasil ditambahkan!';
        header('Location: tabel_kategori.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Kesalahan database: ' . $e->getMessage();
        header('Location: tambah.php');
        exit;
    }
} else {
    header('Location: tabel_kategori.php');
    exit;
}