```php
<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    $_SESSION['error'] = 'ID buku tidak valid.';
    header('Location: tabel_buku.php');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT id, judul
        FROM books
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        'id' => $id
    ]);

    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Data buku tidak ditemukan.';
        header('Location: tabel_buku.php');
        exit;
    }

    $stmt = $pdo->prepare("
        DELETE FROM loan_details
        WHERE book_id = :book_id
    ");

    $stmt->execute([
        'book_id' => $id
    ]);

    $stmt = $pdo->prepare("
        DELETE FROM books
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $id
    ]);

    if ($stmt->rowCount() > 0) {
        $pdo->commit();
        $_SESSION['success'] = 'Buku "' . $book['judul'] . '" berhasil dihapus permanen.';
    } else {
        $pdo->rollBack();
        $_SESSION['error'] = 'Buku gagal dihapus.';
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error'] = 'Gagal menghapus buku: ' . $e->getMessage();
}

header('Location: tabel_buku.php');
exit;