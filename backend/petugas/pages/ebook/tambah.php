<?php

$pageTitle = 'Tambah Ebook';

require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$database = new Database();
$pdo = $database->getConnection();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

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
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="wrapper">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-panel">
        <?php include __DIR__ . '/../layout/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">

                <div class="page-header">
                    <h4 class="page-title">Tambah Ebook</h4>

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
                            <a href="#">Tambah Ebook</a>
                        </li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    Form Tambah Ebook
                                </div>
                            </div>

                            <form
                                action="proses_tambah.php"
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
                                                <option value="<?= (int) $book['id'] ?>">
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

                                        <small class="text-muted">
                                            Pilih buku yang akan diberikan file ebook.
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="file_ebook">
                                            File Ebook
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="file"
                                            name="file_ebook"
                                            id="file_ebook"
                                            class="form-control"
                                            accept=".pdf,.epub"
                                            required>

                                        <small class="text-muted">
                                            Format yang diperbolehkan: PDF atau EPUB.
                                            Maksimal 20 MB.
                                        </small>
                                    </div>

                                </div>

                                <div class="card-action">
                                    <button
                                        type="submit"
                                        class="btn btn-success">

                                        <i class="fas fa-save me-1"></i>
                                        Simpan Ebook

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