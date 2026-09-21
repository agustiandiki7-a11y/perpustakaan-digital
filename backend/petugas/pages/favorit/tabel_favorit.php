<?php
require_once __DIR__.'/../../app/config/Database.php';
require_once __DIR__.'/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin']);

$db=new Database();
$pdo=$db->getConnection();

$stmt=$pdo->query("
    SELECT
        f.id,
        f.created_at,
        u.nama,
        u.username,
        b.judul AS judul_buku,
        b.cover
    FROM favorites f
    LEFT JOIN users u ON u.id=f.user_id
    LEFT JOIN books b ON b.id=f.book_id
    ORDER BY f.id DESC
");

$favorites=$stmt->fetchAll(PDO::FETCH_ASSOC);

$success=$_SESSION['success']??'';
$error=$_SESSION['error']??'';

unset($_SESSION['success'],$_SESSION['error']);

if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token']=bin2hex(random_bytes(32));
}

require_once __DIR__.'/../layout/header.php';
?>

<?php require_once __DIR__.'/../layout/sidebar.php'; ?>

<div class="main-panel">

<?php require_once __DIR__.'/../layout/navbar.php'; ?>

<div class="container">
<div class="page-inner">

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between pt-2 pb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-heart text-danger me-2"></i>
                Favorit Pengguna
            </h3>

            <h6 class="op-7 mb-0">
                Daftar buku yang ditambahkan pengguna ke favorit
            </h6>
        </div>

        <div>
            <span class="badge badge-danger">
                <i class="fas fa-heart me-1"></i>
                <?= count($favorites) ?> Favorit
            </span>
        </div>

    </div>

    <!-- ALERT -->
    <?php if($success): ?>

        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>

            <?= htmlspecialchars(
                $success,
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

    <?php if($error): ?>

        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>

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

    <!-- STATISTIK -->
    <div class="row mb-4">

        <div class="col-sm-6 col-lg-4">

            <div class="card card-stats card-round">
                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-icon">

                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-heart"></i>
                            </div>

                        </div>

                        <div class="col col-stats ms-3 ms-sm-0">

                            <div class="numbers">

                                <p class="card-category">
                                    Total Favorit
                                </p>

                                <h4 class="card-title">
                                    <?= count($favorites) ?>
                                </h4>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div class="col-sm-6 col-lg-4">

            <div class="card card-stats card-round">
                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-icon">

                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-book"></i>
                            </div>

                        </div>

                        <div class="col col-stats ms-3 ms-sm-0">

                            <div class="numbers">

                                <p class="card-category">
                                    Buku Favorit
                                </p>

                                <h4 class="card-title">
                                    <?= count($favorites) ?>
                                </h4>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div class="col-sm-6 col-lg-4">

            <div class="card card-stats card-round">
                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-icon">

                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-users"></i>
                            </div>

                        </div>

                        <div class="col col-stats ms-3 ms-sm-0">

                            <div class="numbers">

                                <p class="card-category">
                                    Pengguna
                                </p>

                                <h4 class="card-title">

                                    <?php
                                    $uniqueUsers=[];

                                    foreach($favorites as $favorite){
                                        if(!empty($favorite['username'])){
                                            $uniqueUsers[]=$favorite['username'];
                                        }
                                    }

                                    echo count(array_unique($uniqueUsers));
                                    ?>

                                </h4>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- DATA FAVORIT -->
    <div class="card card-round">

        <div class="card-header">

            <div class="d-flex align-items-center justify-content-between">

                <div>
                    <h4 class="card-title mb-1">
                        <i class="fas fa-bookmark text-primary me-2"></i>
                        Koleksi Favorit
                    </h4>

                    <p class="card-category mb-0">
                        Buku yang dipilih pengguna sebagai favorit
                    </p>
                </div>

                <?php if(!empty($favorites)): ?>

                    <div style="max-width:280px;">

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>

                            <input
                                type="text"
                                id="searchFavorit"
                                class="form-control"
                                placeholder="Cari favorit..."
                            >

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

        <div class="card-body">

            <?php if(empty($favorites)): ?>

                <div class="text-center py-5">

                    <div
                        class="mb-4"
                        style="
                            width:90px;
                            height:90px;
                            margin:auto;
                            border-radius:50%;
                            background:#f3f2ff;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        "
                    >

                        <i
                            class="fas fa-heart"
                            style="
                                font-size:38px;
                                color:#6861ce;
                            "
                        ></i>

                    </div>

                    <h4 class="fw-bold mb-2">
                        Belum Ada Favorit
                    </h4>

                    <p class="text-muted mb-0">
                        Belum ada pengguna yang menambahkan
                        buku ke favorit.
                    </p>

                </div>

            <?php else: ?>

                <div class="row" id="favoritContainer">

                    <?php foreach($favorites as $favorite): ?>

                        <?php
                        $judul=$favorite['judul_buku']??'Buku Tidak Ditemukan';
                        $namaUser=$favorite['nama']??'Pengguna';
                        $username=$favorite['username']??'-';

                        $searchText=strtolower(
                            $judul.' '.
                            $namaUser.' '.
                            $username
                        );
                        ?>

                        <div
                            class="col-md-6 col-lg-4 mb-4 favorit-item"
                            data-search="<?= htmlspecialchars(
                                $searchText,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                            <div class="card card-round h-100">

                                <!-- COVER -->
                                <div
                                    style="
                                        height:220px;
                                        background:#f5f6fa;
                                        position:relative;
                                        overflow:hidden;
                                        border-radius:12px 12px 0 0;
                                    "
                                >

                                    <?php if(!empty($favorite['cover'])): ?>

                                        <img
                                            src="/perpustakaan/uploads/cover/<?= htmlspecialchars(
                                                $favorite['cover'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            alt="Cover Buku"
                                            style="
                                                width:100%;
                                                height:100%;
                                                object-fit:cover;
                                            "
                                        >

                                    <?php else: ?>

                                        <div
                                            class="h-100 d-flex flex-column align-items-center justify-content-center text-muted"
                                        >

                                            <i
                                                class="fas fa-book mb-2"
                                                style="font-size:48px;"
                                            ></i>

                                            <small>
                                                Cover Tidak Tersedia
                                            </small>

                                        </div>

                                    <?php endif; ?>

                                    <div
                                        style="
                                            position:absolute;
                                            top:14px;
                                            right:14px;
                                            width:40px;
                                            height:40px;
                                            border-radius:50%;
                                            background:#fff;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            box-shadow:0 3px 10px rgba(0,0,0,.12);
                                        "
                                    >

                                        <i class="fas fa-heart text-danger"></i>

                                    </div>

                                </div>

                                <!-- DETAIL -->
                                <div class="card-body">

                                    <h5 class="fw-bold mb-3">
                                        <?= htmlspecialchars(
                                            $judul,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </h5>

                                    <div class="d-flex align-items-center mb-3">

                                        <div
                                            style="
                                                width:40px;
                                                height:40px;
                                                border-radius:50%;
                                                background:#f0efff;
                                                color:#6861ce;
                                                display:flex;
                                                align-items:center;
                                                justify-content:center;
                                                margin-right:10px;
                                            "
                                        >

                                            <i class="fas fa-user"></i>

                                        </div>

                                        <div>

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars(
                                                    $namaUser,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </div>

                                            <small class="text-muted">
                                                @<?= htmlspecialchars(
                                                    $username,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </small>

                                        </div>

                                    </div>

                                    <div class="text-muted small mb-3">

                                        <i class="far fa-calendar-alt me-1"></i>

                                        <?= htmlspecialchars(
                                            date(
                                                'd M Y, H:i',
                                                strtotime($favorite['created_at'])
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                    <form
                                        action="hapus.php"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus favorit ini?')"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$favorite['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                $_SESSION['csrf_token'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-outline-danger btn-sm w-100"
                                        >

                                            <i class="fas fa-trash-alt me-1"></i>
                                            Hapus Favorit

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

                <div
                    id="searchEmpty"
                    class="text-center py-5"
                    style="display:none;"
                >

                    <i class="fas fa-search text-muted mb-3" style="font-size:40px;"></i>

                    <h5 class="fw-bold">
                        Data Tidak Ditemukan
                    </h5>

                    <p class="text-muted">
                        Coba gunakan kata pencarian yang berbeda.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="footer-container">

        <div class="footer-left">
            <span>&copy; <?= date('Y') ?></span>

            <a href="/perpustakaan/backend/index.php">
                Perpustakaan Digital
            </a>

            <span class="footer-dot">•</span>

            <span>Semua Hak Dilindungi.</span>
        </div>

        <div class="footer-right">
            <i class="fas fa-book-open"></i>
            <span>Sistem Informasi Perpustakaan</span>
        </div>

    </div>
</footer>
<!-- End Footer -->

<style>
.footer{
    width:100%;
    margin:0;
    padding:18px 25px;
    background:#fff;
    border-top:1px solid #edf0f5;
    box-sizing:border-box;
    position:relative;
    overflow:visible;
}

.footer-container{
    width:100%;
    min-width:0;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    box-sizing:border-box;
}

.footer-left{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:7px;
    min-width:0;
    color:#8d9498;
    font-size:13px;
    line-height:20px;
}

.footer-left a{
    color:#6861ce;
    font-weight:600;
    text-decoration:none;
    white-space:nowrap;
}

.footer-left a:hover{
    color:#4d47a8;
}

.footer-dot{
    color:#c8cbd1;
}

.footer-right{
    display:flex;
    align-items:center;
    gap:8px;
    flex-shrink:0;
    color:#8d9498;
    font-size:13px;
    white-space:nowrap;
}

.footer-right i{
    color:#6861ce;
}

@media(max-width:768px){

    .footer{
        padding:16px 20px;
    }

    .footer-container{
        flex-direction:column;
        justify-content:center;
        text-align:center;
        gap:8px;
    }

    .footer-left{
        justify-content:center;
    }

    .footer-right{
        justify-content:center;
    }
}
</style>

<!-- ========================================================= -->
<!-- JAVASCRIPT KAIADMIN                                      -->
<!-- ========================================================= -->

<!-- jQuery -->
<script src="/perpustakaan/backend/assets/js/core/jquery-3.7.1.min.js"></script>

<!-- Popper -->
<script src="/perpustakaan/backend/assets/js/core/popper.min.js"></script>

<!-- Bootstrap -->
<script src="/perpustakaan/backend/assets/js/core/bootstrap.min.js"></script>

<!-- KaiAdmin Plugins -->
<script src="/perpustakaan/backend/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="/perpustakaan/backend/assets/js/plugin/chart.js/chart.min.js"></script>
<script src="/perpustakaan/backend/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="/perpustakaan/backend/assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
<script src="/perpustakaan/backend/assets/js/plugin/jqvmap/jquery.vmap.min.js"></script>
<script src="/perpustakaan/backend/assets/js/plugin/jqvmap/maps/jquery.vmap.world.js"></script>
<script src="/perpustakaan/backend/assets/js/plugin/sortable/sortable.min.js"></script>

<!-- KaiAdmin -->
<script src="/perpustakaan/backend/assets/js/kaiadmin.min.js"></script>


</div>
</div>

<script>
$(document).ready(function(){

    $('#searchFavorit').on('keyup',function(){

        let keyword=$(this).val().toLowerCase();
        let total=0;

        $('.favorit-item').each(function(){

            let data=$(this).attr('data-search');

            if(data.includes(keyword)){
                $(this).show();
                total++;
            }else{
                $(this).hide();
            }

        });

        if(total===0){
            $('#searchEmpty').show();
        }else{
            $('#searchEmpty').hide();
        }

    });

});
</script>