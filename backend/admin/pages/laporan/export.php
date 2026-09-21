<?php
require_once __DIR__ . '/../../../app/config/Database.php';
require_once __DIR__ . '/../../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

try {
    $sql = "
        SELECT
            l.id,
            l.tanggal_pinjam,
            l.tanggal_kembali,
            l.status,
            l.denda,
            u.nama AS nama_peminjam,
            u.username,
            b.kode_buku,
            b.judul
        FROM loans l
        INNER JOIN users u ON l.user_id = u.id
        INNER JOIN books b ON l.book_id = b.id
        ORDER BY l.tanggal_pinjam DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Gagal mengambil data laporan.');
}

$filename = 'laporan-perpustakaan-' . date('Y-m-d-H-i-s') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

fprintf($output, "\xEF\xBB\xBF");

fputcsv($output, [
    'No',
    'ID Transaksi',
    'Kode Buku',
    'Judul Buku',
    'Nama Peminjam',
    'Username',
    'Tanggal Pinjam',
    'Tanggal Kembali',
    'Status',
    'Denda'
], ';');

$no = 1;

foreach ($laporan as $data) {

    $tanggalPinjam = !empty($data['tanggal_pinjam'])
        ? date('d-m-Y', strtotime($data['tanggal_pinjam']))
        : '-';

    $tanggalKembali = !empty($data['tanggal_kembali'])
        ? date('d-m-Y', strtotime($data['tanggal_kembali']))
        : '-';

    $status = strtolower(trim($data['status'] ?? ''));

    $statusText = match ($status) {
        'menunggu' => 'Menunggu',
        'dipinjam' => 'Dipinjam',
        'dikembalikan' => 'Dikembalikan',
        'terlambat' => 'Terlambat',
        'dibatalkan' => 'Dibatalkan',
        default => ucfirst($status)
    };

    $denda = (float)($data['denda'] ?? 0);

    fputcsv($output, [
        $no++,
        $data['id'],
        $data['kode_buku'],
        $data['judul'],
        $data['nama_peminjam'],
        $data['username'],
        $tanggalPinjam,
        $tanggalKembali,
        $statusText,
        $denda
    ], ';');
}

fclose($output);
exit;