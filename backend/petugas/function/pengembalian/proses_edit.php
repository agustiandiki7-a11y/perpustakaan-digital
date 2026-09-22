<?php

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);


/*
|--------------------------------------------------------------------------
| Hanya POST
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: pengembalian.php');
    exit;
}


$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$tanggal_kembali = trim(
    $_POST['tanggal_kembali'] ?? ''
);

$status = strtolower(
    trim($_POST['status'] ?? '')
);

$catatan = trim(
    $_POST['catatan'] ?? ''
);


/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/
if (!$id || $id <= 0) {

    $_SESSION['error'] = 'ID pengembalian tidak valid.';

    header('Location: pengembalian.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Validasi status
|--------------------------------------------------------------------------
*/
$statusValid = [
    'dipinjam',
    'dikembalikan',
    'terlambat'
];

if (!in_array($status, $statusValid, true)) {

    $_SESSION['error'] = 'Status pengembalian tidak valid.';

    header('Location: edit.php?id=' . $id);
    exit;
}


/*
|--------------------------------------------------------------------------
| Jika dikembalikan, tanggal wajib ada
|--------------------------------------------------------------------------
*/
if ($status === 'dikembalikan' && empty($tanggal_kembali)) {

    $_SESSION['error'] =
        'Tanggal kembali wajib diisi jika status dikembalikan.';

    header('Location: edit.php?id=' . $id);
    exit;
}


$database = new Database();
$db = $database->connect();


try {

    /*
    |--------------------------------------------------------------------------
    | Cek transaksi
    |--------------------------------------------------------------------------
    */

    $stmt = $db->prepare("
        SELECT id
        FROM loans
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $loan = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$loan) {

        $_SESSION['error'] =
            'Data pengembalian tidak ditemukan.';

        header('Location: pengembalian.php');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    $stmt = $db->prepare("
        UPDATE loans
        SET
            tanggal_kembali = ?,
            status = ?,
            catatan = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");

    $stmt->execute([
        !empty($tanggal_kembali)
            ? $tanggal_kembali
            : null,

        $status,

        $catatan !== ''
            ? $catatan
            : null,

        $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $_SESSION['success'] =
        'Data pengembalian berhasil diperbarui.';

    header('Location: tabel_pengembalian.php');
    exit;


} catch (PDOException $e) {

    $_SESSION['error'] =
        'Gagal memperbarui data pengembalian.';

    header('Location: edit.php?id=' . $id);
    exit;
}