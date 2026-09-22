<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $pdo = $database->connect();

    $category_id   = trim($_POST['category_id'] ?? '');
    $kode_buku     = trim($_POST['kode_buku'] ?? '');
    $judul         = trim($_POST['judul'] ?? '');
    $penulis       = trim($_POST['penulis'] ?? '');
    $penerbit      = trim($_POST['penerbit'] ?? '');
    $tahun_terbit  = trim($_POST['tahun_terbit'] ?? '');
    $jumlah_stok   = (int) ($_POST['jumlah_stok'] ?? 0);
    $status        = 'aktif';

    if (empty($category_id) || empty($kode_buku) || empty($judul) || empty($penulis) || $jumlah_stok <= 0) {
        $_SESSION['error'] = 'Form wajib bertanda bintang (*) harus diisi dengan benar!';
        header('Location: tambah.php');
        exit;
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul))) . '-' . time();

    // Penanganan Upload Cover Aman
    $coverPath = null;
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['cover']['tmp_name'];
        $fileName      = $_FILES['cover']['name'];
        $fileSize      = $_FILES['cover']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize <= 2 * 1024 * 1024) {
                $newFileName = 'cover_' . time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadFileDir = $rootPath . '/assets/uploads/cover/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $coverPath = 'assets/uploads/cover/' . $newFileName;
                } else {
                    $_SESSION['error'] = 'Gagal mengunggah file cover ke server.';
                    header('Location: tambah.php');
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Ukuran file cover terlalu besar (Maksimal 2MB).';
                header('Location: tambah.php');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Format file cover tidak diizinkan. Gunakan JPG, JPEG, atau PNG.';
            header('Location: tambah.php');
            exit;
        }
    }

    try {
        $stok_tersedia = $jumlah_stok;

        $sql = "INSERT INTO books (category_id, kode_buku, judul, slug, penulis, penerbit, tahun_terbit, jumlah_stok, stok_tersedia, cover, status) 
                VALUES (:category_id, :kode_buku, :judul, :slug, :penulis, :penerbit, :tahun_terbit, :jumlah_stok, :stok_tersedia, :cover, :status)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':category_id'   => $category_id,
            ':kode_buku'     => $kode_buku,
            ':judul'         => $judul,
            ':slug'          => $slug,
            ':penulis'       => $penulis,
            ':penerbit'      => empty($penerbit) ? null : $penerbit,
            ':tahun_terbit'  => empty($tahun_terbit) ? null : $tahun_terbit,
            ':jumlah_stok'   => $jumlah_stok,
            ':stok_tersedia' => $stok_tersedia,
            ':cover'         => $coverPath,
            ':status'        => $status
        ]);

        $_SESSION['success'] = 'Data buku berhasil ditambahkan!';
        header('Location: tabel_buku.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
        header('Location: tambah.php');
        exit;
    }
} else {
    header('Location: tambah.php');
    exit;
}