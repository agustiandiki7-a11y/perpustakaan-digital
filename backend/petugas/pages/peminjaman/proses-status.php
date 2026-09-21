<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

// Ambil data dari POST atau GET (agar lebih fleksibel)
$id = $_POST['id'] ?? $_GET['id'] ?? 0;
$status_baru = $_POST['status'] ?? $_GET['status'] ?? '';

// Normalisasi status ke huruf kecil
$status_baru = strtolower(trim($status_baru));

// Validasi status yang diperbolehkan di database
$status_valid = ['menunggu', 'disetujui', 'dipinjam', 'dikembalikan', 'terlambat', 'ditolak', 'dibatalkan'];

if ($id && in_array($status_baru, $status_valid)) {
    try {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("UPDATE loans SET status = ? WHERE id = ?");
        $stmt->execute([$status_baru, $id]);

        header("Location: tabel_peminjaman.php?pesan=sukses");
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Gagal memperbarui status: " . addslashes($e->getMessage()) . "'); window.location.href='tabel_peminjaman.php';</script>";
        exit;
    }
} else {
    // Menampilkan pesan spesifik jika data kurang lengkap
    echo "<script>alert('Data peminjaman tidak lengkap atau status tidak valid! (ID: $id, Status: $status_baru)'); window.location.href='tabel_peminjaman.php';</script>";
    exit;
}