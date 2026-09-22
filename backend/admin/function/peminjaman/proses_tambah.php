<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $book_id = $_POST['book_id'] ?? '';
    $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? '';
    $tanggal_jatuh_tempo = $_POST['tanggal_jatuh_tempo'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    
    // Nilai default untuk peminjaman baru dari admin/petugas
    $status = 'dipinjam'; 
    $tanggal_pengajuan = date('Y-m-d');
    
    // Generate kode peminjaman unik secara otomatis (misal: PJM-YYYYMMDD-XXXX)
    $kode_peminjaman = 'PJM-' . date('Ymd') . '-' . mt_rand(1000, 9999);

    if (!empty($user_id) && !empty($book_id) && !empty($tanggal_pinjam) && !empty($tanggal_jatuh_tempo)) {
        try {
            $database = new Database();
            $db = $database->connect();

            // Mulai transaksi database (agar data tersimpan di loans & loan_details secara bersamaan)
            $db->beginTransaction();

            // 1. Simpan ke tabel utama 'loans'
            $sql_loan = "INSERT INTO loans (kode_peminjaman, user_id, tanggal_pengajuan, tanggal_pinjam, tanggal_jatuh_tempo, status, catatan) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_loan = $db->prepare($sql_loan);
            $stmt_loan->execute([$kode_peminjaman, $user_id, $tanggal_pengajuan, $tanggal_pinjam, $tanggal_jatuh_tempo, $status, $catatan]);
            
            // Ambil ID dari loan yang baru saja dimasukkan
            $loan_id = $db->lastInsertId();

            // 2. Simpan ke tabel relasi 'loan_details' (jumlah default 1 buku)
            $sql_detail = "INSERT INTO loan_details (loan_id, book_id, jumlah) VALUES (?, ?, 1)";
            $stmt_detail = $db->prepare($sql_detail);
            $stmt_detail->execute([$loan_id, $book_id]);

            // Commit transaksi jika berhasil semua
            $db->commit();

            header("Location: tabel_peminjaman.php?pesan=sukses");
            exit;

        } catch (PDOException $e) {
            // Batalkan transaksi jika ada error
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            echo "Gagal menyimpan data peminjaman: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Semua kolom wajib diisi!'); window.history.back();</script>";
    }
} else {
    header("Location: tabel_peminjaman.php");
    exit;
}