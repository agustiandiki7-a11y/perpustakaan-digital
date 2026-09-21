<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $pdo = $db->getConnection();

    // Ambil dan validasi ID buku
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($id <= 0) {
        $_SESSION['error'] = 'ID buku tidak valid!';
        header('Location: tabel_buku.php');
        exit;
    }

    // Ambil data buku lama dari database untuk pengecekan stok & cover lama
    $stmtCek = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmtCek->execute([$id]);
    $oldBook = $stmtCek->fetch(PDO::FETCH_ASSOC);

    if (!$oldBook) {
        $_SESSION['error'] = 'Data buku tidak ditemukan!';
        header('Location: tabel_buku.php');
        exit;
    }

    // Ambil dan bersihkan input form
    $category_id   = trim($_POST['category_id'] ?? '');
    $kode_buku     = trim($_POST['kode_buku'] ?? '');
    $isbn          = trim($_POST['isbn'] ?? '');
    $judul         = trim($_POST['judul'] ?? '');
    $penulis       = trim($_POST['penulis'] ?? '');
    $penerbit      = trim($_POST['penerbit'] ?? '');
    $tahun_terbit  = trim($_POST['tahun_terbit'] ?? '');
    $jumlah_stok_baru = (int) ($_POST['jumlah_stok'] ?? 0);
    $lokasi_rak    = trim($_POST['lokasi_rak'] ?? '');
    $deskripsi     = trim($_POST['deskripsi'] ?? '');
    $status        = trim($_POST['status'] ?? 'aktif');

    // Validasi data wajib
    if (empty($category_id) || empty($kode_buku) || empty($judul) || empty($penulis) || $jumlah_stok_baru < 0) {
        $_SESSION['error'] = 'Form wajib bertanda bintang (*) harus diisi dengan benar!';
        header('Location: edit.php?id=' . $id);
        exit;
    }

    // Hitung penyesuaian stok tersedia berdasarkan selisih perubahan jumlah stok total
    $selisih_stok = $jumlah_stok_baru - (int)$oldBook['jumlah_stok'];
    $stok_tersedia_baru = (int)$oldBook['stok_tersedia'] + $selisih_stok;
    
    // Pastikan stok tersedia tidak negatif
    if ($stok_tersedia_baru < 0) {
        $stok_tersedia_baru = 0;
    }

    // Buat slug baru jika judul berubah (atau biarkan/perbarui)
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    $slug .= '-' . $id;

    // Tangani Upload Cover Baru (Jika ada)
    $coverPath = $oldBook['cover']; // Default gunakan cover lama
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['cover']['tmp_name'];
        $fileName      = $_FILES['cover']['name'];
        $fileSize      = $_FILES['cover']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize <= 2 * 1024 * 1024) { // Maksimal 2MB
                $newFileName = 'cover_' . time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/../../assets/uploads/cover/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $coverPath = 'assets/uploads/cover/' . $newFileName;

                    // Hapus file cover lama jika ada di server untuk menghemat penyimpanan
                    if (!empty($oldBook['cover']) && file_exists(__DIR__ . '/../../' . $oldBook['cover'])) {
                        @unlink(__DIR__ . '/../../' . $oldBook['cover']);
                    }
                } else {
                    $_SESSION['error'] = 'Gagal mengunggah file cover baru ke server.';
                    header('Location: edit.php?id=' . $id);
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Ukuran file cover terlalu besar (Maksimal 2MB).';
                header('Location: edit.php?id=' . $id);
                exit;
            }
        } else {
            $_SESSION['error'] = 'Format file cover tidak diizinkan. Gunakan JPG, JPEG, atau PNG.';
            header('Location: edit.php?id=' . $id);
            exit;
        }
    }

    try {
        $sql = "UPDATE books SET 
                    category_id = :category_id, 
                    kode_buku = :kode_buku, 
                    isbn = :isbn, 
                    judul = :judul, 
                    slug = :slug, 
                    penulis = :penulis, 
                    penerbit = :penerbit, 
                    tahun_terbit = :tahun_terbit, 
                    jumlah_stok = :jumlah_stok, 
                    stok_tersedia = :stok_tersedia, 
                    lokasi_rak = :lokasi_rak, 
                    deskripsi = :deskripsi, 
                    cover = :cover, 
                    status = :status 
                WHERE id = :id";
        
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
            ':jumlah_stok'     => $jumlah_stok_baru,
            ':stok_tersedia'   => $stok_tersedia_baru,
            ':lokasi_rak'      => empty($lokasi_rak) ? null : $lokasi_rak,
            ':deskripsi'       => empty($deskripsi) ? null : $deskripsi,
            ':cover'           => $coverPath,
            ':status'          => $status,
            ':id'              => $id
        ]);

        $_SESSION['success'] = 'Data buku berhasil diperbarui!';
        header('Location: tabel_buku.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
        header('Location: edit.php?id=' . $id);
        exit;
    }
} else {
    header('Location: tabel_buku.php');
    exit;
}