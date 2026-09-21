<?php
$pageTitle='Detail Ebook';

require_once __DIR__.'/../../app/config/Database.php';
require_once __DIR__.'/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin','petugas']);

$database=new Database();
$pdo=$database->getConnection();

$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);

if(!$id){
    die('ID ebook tidak valid.');
}

try{
    $stmt=$pdo->prepare("
        SELECT
            book_files.id,
            book_files.book_id,
            book_files.nama_file,
            book_files.file_path,
            book_files.tipe_file,
            book_files.ukuran_file,
            book_files.created_at,
            books.kode_buku,
            books.judul,
            books.penulis,
            categories.nama_kategori
        FROM book_files
        INNER JOIN books
            ON books.id=book_files.book_id
        LEFT JOIN categories
            ON categories.id=books.category_id
        WHERE book_files.id=:id
        LIMIT 1
    ");

    $stmt->execute([
        ':id'=>$id
    ]);

    $ebook=$stmt->fetch(PDO::FETCH_ASSOC);

}catch(PDOException $e){
    die('Gagal mengambil detail ebook.');
}

if(!$ebook){
    die('Data ebook tidak ditemukan.');
}

function escapeHtml($value):string{
    return htmlspecialchars(
        (string)($value??''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function formatUkuran($bytes):string{
    if(!is_numeric($bytes)||$bytes<=0){
        return '-';
    }

    $bytes=(int)$bytes;

    if($bytes>=1073741824){
        return number_format($bytes/1073741824,2).' GB';
    }

    if($bytes>=1048576){
        return number_format($bytes/1048576,2).' MB';
    }

    if($bytes>=1024){
        return number_format($bytes/1024,2).' KB';
    }

    return $bytes.' Bytes';
}

$filePath=trim((string)($ebook['file_path']??''));

if($filePath!==''){
    $fileUrl='/perpustakaan/'.ltrim($filePath,'/');
}else{
    $fileUrl='';
}

$tipeFile=strtolower((string)($ebook['tipe_file']??''));

if($tipeFile===''){
    $extension=strtolower(pathinfo($ebook['nama_file'],PATHINFO_EXTENSION));

    if($extension!==''){
        $tipeFile='application/'.$extension;
    }
}
?>

<?php include __DIR__.'/../layout/header.php'; ?>

<div class="wrapper">

    <?php include __DIR__.'/../layout/sidebar.php'; ?>

    <div class="main-panel">

        <?php include __DIR__.'/../layout/navbar.php'; ?>

        <div class="container">

            <div class="page-inner">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <h3 class="fw-bold mb-1">Detail Ebook</h3>
                        <p class="text-muted mb-0">
                            Informasi lengkap ebook
                        </p>
                    </div>

                    <a href="tabel_ebook.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>

                </div>

                <div class="row">

                    <div class="col-md-8">

                        <div class="card card-round">

                            <div class="card-header">

                                <h4 class="card-title mb-0">
                                    Informasi Ebook
                                </h4>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive">

                                    <table class="table table-borderless">

                                        <tr>
                                            <th width="180">Kode Buku</th>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= escapeHtml($ebook['kode_buku']) ?>
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Judul Buku</th>
                                            <td>
                                                <strong>
                                                    <?= escapeHtml($ebook['judul']) ?>
                                                </strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Penulis</th>
                                            <td>
                                                <?= escapeHtml($ebook['penulis']) ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Kategori</th>
                                            <td>

                                                <?php if(!empty($ebook['nama_kategori'])): ?>

                                                    <span class="badge bg-secondary">
                                                        <?= escapeHtml($ebook['nama_kategori']) ?>
                                                    </span>

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                <?php endif; ?>

                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Nama File</th>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger me-1"></i>
                                                <?= escapeHtml($ebook['nama_file']) ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Tipe File</th>
                                            <td>
                                                <?= escapeHtml($ebook['tipe_file']) ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Ukuran File</th>
                                            <td>
                                                <?= formatUkuran($ebook['ukuran_file']) ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Tanggal Upload</th>
                                            <td>

                                                <?php if(!empty($ebook['created_at'])): ?>

                                                    <?= date(
                                                        'd-m-Y H:i',
                                                        strtotime($ebook['created_at'])
                                                    ) ?>

                                                <?php else: ?>

                                                    -

                                                <?php endif; ?>

                                            </td>
                                        </tr>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card card-round">

                            <div class="card-header">

                                <h4 class="card-title mb-0">
                                    File Ebook
                                </h4>

                            </div>

                            <div class="card-body text-center">

                                <div class="mb-4">

                                    <i
                                        class="fas fa-file-pdf text-danger"
                                        style="font-size:80px;">
                                    </i>

                                </div>

                                <h5 class="fw-bold mb-2">
                                    <?= escapeHtml($ebook['nama_file']) ?>
                                </h5>

                                <p class="text-muted mb-4">
                                    <?= formatUkuran($ebook['ukuran_file']) ?>
                                </p>

                                <?php if($fileUrl!==''): ?>

                                    <a
                                        href="<?= escapeHtml($fileUrl) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-book-open me-1"></i>
                                        Baca Ebook
                                    </a>

                                    <a
                                        href="<?= escapeHtml($fileUrl) ?>"
                                        download="<?= escapeHtml($ebook['nama_file']) ?>"
                                        class="btn btn-success w-100">
                                        <i class="fas fa-download me-1"></i>
                                        Download
                                    </a>

                                <?php else: ?>

                                    <div class="alert alert-warning mb-0">
                                        File ebook tidak tersedia.
                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php include __DIR__.'/../layout/footer.php'; ?>

    </div>

</div>

</body>
</html> 