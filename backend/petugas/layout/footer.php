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

</body>
</html>