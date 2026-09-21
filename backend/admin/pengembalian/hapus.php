<?php

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);


$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id || $id <= 0) {

    $_SESSION['error'] =
        'ID pengembalian tidak valid.';

    header('Location: pengembalian.php');
    exit;
}


$database = new Database();
$db = $database->connect();


try {

    /*
    |--------------------------------------------------------------------------
    | Ambil data sebelum dihapus
    |--------------------------------------------------------------------------
    */

    $stmt = $db->prepare("
        SELECT
            id,
            kode_peminjaman,
            status
        FROM loans
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$data) {

        $_SESSION['error'] =
            'Data pengembalian tidak ditemukan.';

        header('Location: pengembalian.php');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Hapus transaksi
    |--------------------------------------------------------------------------
    */

    $stmt = $db->prepare("
        DELETE FROM loans
        WHERE id = ?
    ");

    $stmt->execute([$id]);


    /*
    |--------------------------------------------------------------------------
    | Pesan berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['success'] =
        'Data pengembalian dengan kode "' .
        $data['kode_peminjaman'] .
        '" berhasil dihapus.';


} catch (PDOException $e) {

    $_SESSION['error'] =
        'Gagal menghapus data pengembalian.';
}


header('Location: pengembalian.php');
exit;