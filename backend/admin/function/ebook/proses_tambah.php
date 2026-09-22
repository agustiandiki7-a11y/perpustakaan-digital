<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {$database = new Database();
    $pdo =$database->connect();

    // Ambil ID buku induk dari form
    $book_id = trim($_POST['book_id'] ?? '');

    // Validasi pilihan buku
    if (empty($book_id) || !is_numeric($book_id)) {$_SESSION['error'] = 'Silakan pilih buku induk terlebih dahulu!';
        header('Location: tambah.php');
        exit;
    }

    // Validasi dan Tangani Upload File Ebook
    if (isset($_FILES['file_ebook']) &&$_FILES['file_ebook']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath      =$_FILES['file_ebook']['tmp_name'];
        $fileNameOriginal =$_FILES['file_ebook']['name'];
        $fileSize         =$_FILES['file_ebook']['size'];
        $fileExtension    = strtolower(pathinfo($fileNameOriginal, PATHINFO_EXTENSION));

        // Ekstensi yang diizinkan untuk ebook
        $allowedExtensions = ['pdf', 'epub'];$maxFileSize       = 20 * 1024 * 1024; // Batas maksimal 20 MB

        // Validasi ekstensi file
        if (!in_array($fileExtension, $allowedExtensions)) {$_SESSION['error'] = 'Format file tidak diizinkan. Hanya file berformat PDF atau EPUB yang diperbolehkan.';
            header('Location: tambah.php');
            exit;
        }

        // Validasi ukuran file
        if ($fileSize > $maxFileSize) {$_SESSION['error'] = 'Ukuran file ebook terlalu besar. Maksimal ukuran file adalah 20 MB.';
            header('Location: tambah.php');
            exit;
        }

        // Buat nama file baru yang unik untuk menghindari nama file ganda/bentrok
        $newFileName   = 'ebook_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadFileDir =$rootPath . '/assets/uploads/ebooks/';

        // Buat direktori penyimpanan jika belum ada
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $dest_path = $uploadFileDir .$newFileName;

        // Pindahkan file dari temp ke folder tujuan
        if (move_uploaded_file($fileTmpPath,$dest_path)) {
            // Path relatif yang akan disimpan ke database
            $filePathDb = 'assets/uploads/ebooks/' .$newFileName;
            $tipeFile   = strtoupper($fileExtension);

            try {
                // Simpan data file ebook ke dalam tabel database `book_files` menggunakan Prepared Statements
                $sql = "INSERT INTO book_files (book_id, nama_file, file_path, tipe_file, ukuran_file) 
                        VALUES (:book_id, :nama_file, :file_path, :tipe_file, :ukuran_file)";
                
                $stmt =$pdo->prepare($sql);$stmt->execute([
                    ':book_id'     => $book_id,
                    ':nama_file'   => $fileNameOriginal,
                    ':file_path'   => $filePathDb,
                    ':tipe_file'   => $tipeFile,
                    ':ukuran_file' => $fileSize
                ]);

                $_SESSION['success'] = 'File ebook berhasil diunggah dan disimpan!';
                header('Location: tabel_ebook.php');
                exit;

            } catch (PDOException $e) {
                $_SESSION['error'] = 'Terjadi kesalahan pada database: ' .$e->getMessage();
                header('Location: tambah.php');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Gagal mengunggah file ebook ke server.';
            header('Location: tambah.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Silakan pilih file ebook yang valid untuk diunggah.';
        header('Location: tambah.php');
        exit;
    }
} else {
    // Jika diakses langsung tanpa method POST, arahkan kembali ke form tambah
    header('Location: tambah.php');
    exit;
}