<?php

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tabel_ebook.php');
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$bookId = isset($_POST['book_id']) ? (int) $_POST['book_id'] : 0;
$namaFile = trim($_POST['nama_file'] ?? '');

if ($id <= 0) {
    $_SESSION['error'] = 'ID ebook tidak valid.';
    header('Location: tabel_ebook.php');
    exit;
}

if ($bookId <= 0) {
    $_SESSION['error'] = 'Buku belum dipilih.';
    header("Location: edit.php?id=$id");
    exit;
}

if ($namaFile === '') {
    $_SESSION['error'] = 'Nama file wajib diisi.';
    header("Location: edit.php?id=$id");
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM book_files
    WHERE id = ?
");

$stmt->execute([$id]);

$ebook = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ebook) {
    $_SESSION['error'] = 'Data ebook tidak ditemukan.';
    header('Location: tabel_ebook.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT id
    FROM books
    WHERE id = ?
    AND status = 'aktif'
");

$stmt->execute([$bookId]);

if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Data buku tidak ditemukan.';
    header("Location: edit.php?id=$id");
    exit;
}

$newFilePath = null;
$newDatabasePath = null;
$newExtension = null;
$newFileSize = null;

if (
    isset($_FILES['file_ebook']) &&
    $_FILES['file_ebook']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $file = $_FILES['file_ebook'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Gagal mengunggah file baru.';
        header("Location: edit.php?id=$id");
        exit;
    }

    $maxSize = 20 * 1024 * 1024;

    if ($file['size'] > $maxSize) {
        $_SESSION['error'] = 'Ukuran file maksimal 20 MB.';
        header("Location: edit.php?id=$id");
        exit;
    }

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    $allowedExtensions = ['pdf', 'epub'];

    if (!in_array($extension, $allowedExtensions, true)) {
        $_SESSION['error'] = 'Format file hanya boleh PDF atau EPUB.';
        header("Location: edit.php?id=$id");
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    $allowedMime = [
        'pdf' => [
            'application/pdf'
        ],
        'epub' => [
            'application/epub+zip',
            'application/zip'
        ]
    ];

    if (!in_array($mimeType, $allowedMime[$extension], true)) {
        $_SESSION['error'] = 'Isi file tidak sesuai dengan format file.';
        header("Location: edit.php?id=$id");
        exit;
    }

    $uploadDir = __DIR__ . '/../../assets/uploads/ebook/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFileName = uniqid('ebook_', true) . '.' . $extension;

    $newFilePath = $uploadDir . $newFileName;
    $newDatabasePath = 'assets/uploads/ebook/' . $newFileName;
    $newExtension = strtoupper($extension);
    $newFileSize = $file['size'];

    if (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
        $_SESSION['error'] = 'File baru gagal disimpan.';
        header("Location: edit.php?id=$id");
        exit;
    }
}

try {
    $pdo->beginTransaction();

    if ($newFilePath !== null) {
        $stmt = $pdo->prepare("
            UPDATE book_files
            SET
                book_id = ?,
                nama_file = ?,
                file_path = ?,
                tipe_file = ?,
                ukuran_file = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $bookId,
            $namaFile,
            $newDatabasePath,
            $newExtension,
            $newFileSize,
            $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE book_files
            SET
                book_id = ?,
                nama_file = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $bookId,
            $namaFile,
            $id
        ]);
    }

    $pdo->commit();

    if ($newFilePath !== null) {
        $oldFile = __DIR__ . '/../../' . $ebook['file_path'];

        if (is_file($oldFile)) {
            unlink($oldFile);
        }
    }

    $_SESSION['success'] = 'Data ebook berhasil diperbarui.';
    header('Location: tabel_ebook.php');
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ($newFilePath !== null && is_file($newFilePath)) {
        unlink($newFilePath);
    }

    $_SESSION['error'] = 'Gagal memperbarui data ebook.';
    header("Location: edit.php?id=$id");
    exit;
}