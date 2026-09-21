<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $pdo = $db->getConnection();

    // Ambil dan bersihkan input form
    $category_id   = trim($_POST['category_id'] ?? '');
    $kode_buku     = trim($_POST['kode_buku'] ?? '');
    $isbn          = trim($_POST['isbn'] ?? '');
    $judul         = trim($_POST['judul'] ?? '');
    $penulis       = trim($_POST['penulis'] ?? '');
    $penerbit      = trim($_POST['penerbit'] ?? '');
    $tahun_terbit  = trim($_POST['tahun_terbit'] ?? '');
    $jumlah_stok   = (int) ($_POST['jumlah_stok'] ?? 0);
    $lokasi_rak    = trim($_POST['lokasi_rak'] ?? '');
    $deskripsi     = trim($_POST['deskripsi'] ?? '');
    $status        = trim($_POST['status'] ?? 'aktif');

    // Validasi sederhana data wajib
    if (empty($category_id) || empty($kode_buku) || empty($judul) || empty($penulis) || $jumlah_stok <= 0) {
        $_SESSION['error'] = 'Form wajib bertanda bintang (*) harus diisi dengan benar!';
        header('Location: tambah.php');
        exit;
    }

    // Buat slug otomatis dari judul buku
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    // Pastikan slug unik dengan menambahkan angka acak kecil jika perlu, atau biarkan standar
    $slug .= '-' . time();

    // Tangani Upload Cover Buku
    $coverPath = null;
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['cover']['tmp_name'];
        $fileName      = $_FILES['cover']['name'];
        $fileSize      = $_FILES['cover']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Batksi ukuran file (misal maksimal 2MB)
            if ($fileSize <= 2 * 1024 * 1024) {
                $newFileName = 'cover_' . time() . '_' . uniqid() . '.' . $fileExtension;
                
                // Path relatif penyimpanan dari folder backend/buku/ ke assets/uploads/cover/
                // Sesuaikan direktori upload target
                $uploadFileDir = __DIR__ . '/../../assets/uploads/cover/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Simpan path relatif ke database
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
        // Saat buku baru ditambahkan, stok_tersedia nilainya sama dengan jumlah_stok
        $stok_tersedia = $jumlah_stok;
        $tersedia_online = 1; // Default tersedia online atau sesuaikan kebutuhan kolom

        $sql = "INSERT INTO books (category_id, kode_buku, isbn, judul, slug, penulis, penerbit, tahun_terbit, jumlah_stok, stok_tersedia, lokasi_rak, deskripsi, cover, tersedia_online, status) 
                VALUES (:category_id, :kode_buku, :isbn, :judul, :slug, :penulis, :penerbit, :tahun_terbit, :jumlah_stok, :stok_tersedia, :lokasi_rak, :deskripsi, :cover, :tersedia_online, :status)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':category_id'     => $category_id,
            ':kode_buku'       => $kode_buku,
            ':isbn'            => empty($isbn) ? null : $isbn,
            ':judul'           => $judul,
            ':slug'            => $slug,
            ':penulis'         => $penulis,
            ':penerbit'        => empty($penerbit) ? null : $penerbit,
            ':tahun_terbit'    => empty($tahun_terbit) ? null : $tahun_terbit,
            ':jumlah_stok'     => $jumlah_stok,
            ':stok_tersedia'   => $stok_tersedia,
            ':lokasi_rak'      => empty($lokasi_rak) ? null : $lokasi_rak,
            ':deskripsi'       => empty($deskripsi) ? null : $deskripsi,
            ':cover'           => $coverPath,
            ':tersedia_online' => $tersedia_online,
            ':status'          => $status
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