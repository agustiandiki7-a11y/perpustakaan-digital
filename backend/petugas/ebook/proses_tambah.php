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

$bookId = isset($_POST['book_id']) ? (int) $_POST['book_id'] : 0;

if ($bookId <= 0) {
    $_SESSION['error'] = 'Buku belum dipilih.';
    header('Location: tambah.php');
    exit;
}

if (!isset($_FILES['file_ebook'])) {
    $_SESSION['error'] = 'File ebook belum dipilih.';
    header('Location: tambah.php');
    exit;
}

$file = $_FILES['file_ebook'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Gagal mengunggah file ebook.';
    header('Location: tambah.php');
    exit;
}

$maxSize = 20 * 1024 * 1024;

if ($file['size'] > $maxSize) {
    $_SESSION['error'] = 'Ukuran file maksimal 20 MB.';
    header('Location: tambah.php');
    exit;
}

$extension = strtolower(
    pathinfo($file['name'], PATHINFO_EXTENSION)
);

$allowedExtensions = ['pdf', 'epub'];

if (!in_array($extension, $allowedExtensions, true)) {
    $_SESSION['error'] = 'Format file hanya boleh PDF atau EPUB.';
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, judul
    FROM books
    WHERE id = ?
    AND status = 'aktif'
");

$stmt->execute([$bookId]);

$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    $_SESSION['error'] = 'Data buku tidak ditemukan.';
    header('Location: tambah.php');
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
    header('Location: tambah.php');
    exit;
}

$uploadDir = __DIR__ . '/../../assets/uploads/ebook/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$originalName = basename($file['name']);
$fileName = uniqid('ebook_', true) . '.' . $extension;
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    $_SESSION['error'] = 'File gagal disimpan ke server.';
    header('Location: tambah.php');
    exit;
}

$databasePath = 'assets/uploads/ebook/' . $fileName;

try {
    $stmt = $pdo->prepare("
        INSERT INTO book_files
        (
            book_id,
            nama_file,
            file_path,
            tipe_file,
            ukuran_file
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $bookId,
        $originalName,
        $databasePath,
        strtoupper($extension),
        $file['size']
    ]);

    $_SESSION['success'] = 'Ebook berhasil ditambahkan.';
    header('Location: tabel_ebook.php');
    exit;
} catch (PDOException $e) {
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $_SESSION['error'] = 'Gagal menyimpan data ebook.';
    header('Location: tambah.php');
    exit;
}