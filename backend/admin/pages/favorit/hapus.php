<?php
require_once __DIR__.'/../../app/config/Database.php';
require_once __DIR__.'/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

if($_SERVER['REQUEST_METHOD']!=='POST'){
    $_SESSION['error']='Metode permintaan tidak valid.';
    header('Location: tabel_favorit.php');
    exit;
}

$csrfToken=$_POST['csrf_token']??'';

if(
    empty($_SESSION['csrf_token'])||
    empty($csrfToken)||
    !hash_equals($_SESSION['csrf_token'],$csrfToken)
){
    $_SESSION['error']='Token keamanan tidak valid.';
    header('Location: tabel_favorit.php');
    exit;
}

$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);

if(!$id||$id<=0){
    $_SESSION['error']='ID favorit tidak valid.';
    header('Location: tabel_favorit.php');
    exit;
}

$db=new Database();
$pdo=$db->getConnection();

try{

    $check=$pdo->prepare("
        SELECT
            f.id,
            b.judul AS judul_buku,
            e.judul AS judul_ebook
        FROM favorites f
        LEFT JOIN books b ON b.id=f.book_id
        LEFT JOIN ebooks e ON e.id=f.ebook_id
        WHERE f.id=:id
        LIMIT 1
    ");

    $check->execute([
        'id'=>$id
    ]);

    $favorite=$check->fetch(PDO::FETCH_ASSOC);

    if(!$favorite){
        $_SESSION['error']='Data favorit tidak ditemukan.';
        header('Location: tabel_favorit.php');
        exit;
    }

    $judul=$favorite['judul_buku']
        ?:($favorite['judul_ebook']??'Favorit');

    $stmt=$pdo->prepare("
        DELETE FROM favorites
        WHERE id=:id
    ");

    $stmt->execute([
        'id'=>$id
    ]);

    $_SESSION['success']='Favorit "'.$judul.'" berhasil dihapus.';

}catch(PDOException $e){

    $_SESSION['error']='Gagal menghapus data favorit.';

}

header('Location: tabel_favorit.php');
exit;