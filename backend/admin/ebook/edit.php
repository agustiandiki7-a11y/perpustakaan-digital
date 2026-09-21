<?php

$pageTitle = 'Edit Ebook';

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$pdo = $database->getConnection();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'ID ebook tidak valid.';
    header('Location: tabel_ebook.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM book_files
    WHERE id = ?
");

$stmt->execute([$id]);

$ebook = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ebook) {
    $_SESSION['error'] = 'Data ebook tidak ditemukan.';
    header('Location: tabel_ebook.php');
    exit;
}

$stmt = $pdo->query("
    SELECT
        books.id,
        books.kode_buku,
        books.judul,
        books.penulis,
        categories.nama_kategori
    FROM books
    LEFT JOIN categories ON categories.id = books.category_id
    WHERE books.status = 'aktif'
    ORDER BY books.judul ASC
");

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="wrapper">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php include __DIR__ . '/../layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="page-header">
                    <h4 class="page-title">Edit Ebook</h4>

                    <ul class="breadcrumbs">
                        <li class="nav-home">
                            <a href="../index.php">
                                <i class="icon-home"></i>
                            </a>
                        </li>

                        <li class="separator">
                            <i class="icon-arrow-right"></i>
                        </li>

                        <li class="nav-item">
                            <a href="tabel_ebook.php">Data Ebook</a>
                        </li>

                        <li class="separator">
                            <i class="icon-arrow-right"></i>
                        </li>

                        <li class="nav-item">
                            <a href="#">Edit Ebook</a>
                        </li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    Form Edit Ebook
                                </div>
                            </div>

                            <form
                                action="proses_edit.php"
                                method="POST"
                                enctype="multipart/form-data">

                                <div class="card-body">

                                    <?php if ($error): ?>
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <?= htmlspecialchars(
                                                $error,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="alert">
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $ebook['id'] ?>">

                                    <div class="form-group">
                                        <label for="book_id">
                                            Buku
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select
                                            name="book_id"
                                            id="book_id"
                                            class="form-control"
                                            required>

                                            <option value="">
                                                -- Pilih Buku --
                                            </option>

                                            <?php foreach ($books as $book): ?>
                                                <option
                                                    value="<?= (int) $book['id'] ?>"
                                                    <?= (int) $ebook['book_id'] === (int) $book['id'] ? 'selected' : '' ?>>

                                                    <?= htmlspecialchars(
                                                        $book['kode_buku'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                    -

                                                    <?= htmlspecialchars(
                                                        $book['judul'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nama_file">
                                            Nama File
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="nama_file"
                                            id="nama_file"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $ebook['nama_file'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label for="file_ebook">
                                            Ganti File Ebook
                                        </label>

                                        <input
                                            type="file"
                                            name="file_ebook"
                                            id="file_ebook"
                                            class="form-control"
                                            accept=".pdf,.epub">

                                        <small class="text-muted">
                                            Kosongkan jika tidak ingin mengganti file.
                                            Format PDF atau EPUB, maksimal 20 MB.
                                        </small>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-file me-2"></i>

                                        File saat ini:
                                        <strong>
                                            <?= htmlspecialchars(
                                                $ebook['nama_file'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </div>

                                </div>

                                <div class="card-action">
                                    <button
                                        type="submit"
                                        class="btn btn-success">

                                        <i class="fas fa-save me-1"></i>
                                        Simpan Perubahan

                                    </button>

                                    <a
                                        href="tabel_ebook.php"
                                        class="btn btn-danger">

                                        <i class="fas fa-times me-1"></i>
                                        Batal

                                    </a>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <?php include __DIR__ . '/../layout/footer.php'; ?>
    </div>
</div>