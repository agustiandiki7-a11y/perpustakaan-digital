<?php
// ==========================================
// KONEKSI DATABASE & INISIALISASI
// ==========================================
require_once __DIR__ . '/app/config/Database.php';

$db = new Database();
$pdo = $db->getConnection();

// Jika ada request AJAX untuk pencarian live search
if (isset($_GET['ajax_search']) && $_GET['ajax_search'] == '1') {
    $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

    if ($keyword !== '') {
        $stmt = $pdo->prepare("
            SELECT books.*, categories.nama_kategori 
            FROM books 
            LEFT JOIN categories ON categories.id = books.category_id 
            WHERE books.judul LIKE ? OR books.penulis LIKE ? OR books.penerbit LIKE ? 
            LIMIT 8
        ");
        $searchTerm = "%{$keyword}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->query("
            SELECT books.*, categories.nama_kategori 
            FROM books 
            LEFT JOIN categories ON categories.id = books.category_id 
            ORDER BY books.id DESC LIMIT 8
        ");
    }

    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($books)) {
        echo '<div class="col-12 text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3 text-secondary opacity-50"></i><h5>Buku tidak ditemukan.</h5><p class="small">Coba gunakan kata kunci pencarian yang lain.</p></div>';
    } else {
        foreach ($books as $buku) {
            $cover = (!empty($buku['cover']) && file_exists(__DIR__ . '/' . $buku['cover'])) 
                     ? htmlspecialchars($buku['cover'], ENT_QUOTES, 'UTF-8') 
                     : '';
            
            echo '<div class="col-sm-6 col-md-4 col-lg-3 mb-4">';
            echo '<div class="card card-book h-100 shadow-sm border-0 rounded-3 overflow-hidden bg-white">';
            if ($cover) {
                echo '<img src="' . $cover . '" class="card-img-top" style="height: 240px; object-fit: cover;" alt="Cover Buku">';
            } else {
                echo '<div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 240px;"><i class="fas fa-book fa-3x opacity-50"></i></div>';
            }
            echo '<div class="card-body d-flex flex-column p-3">';
            echo '<span class="badge bg-light text-primary border mb-2 align-self-start small">' . htmlspecialchars($buku['nama_kategori'] ?? 'Umum', ENT_QUOTES, 'UTF-8') . '</span>';
            echo '<h5 class="card-title fw-bold fs-6 text-dark mb-1 text-truncate" title="' . htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8') . '</h5>';
            echo '<p class="card-text text-muted small mb-3">Oleh: ' . htmlspecialchars($buku['penulis'] ?? 'Anonim', ENT_QUOTES, 'UTF-8') . '</p>';
            echo '<div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">';
            echo '<span class="badge ' . (($buku['stok_tersedia'] > 0) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') . '">Stok: ' . (int)$buku['stok_tersedia'] . '</span>';
            echo '<a href="detail_buku.php?id=' . (int)$buku['id'] . '" class="btn btn-sm btn-primary px-3 rounded-pill">Detail</a>';
            echo '</div></div></div></div>';
        }
    }
    exit; // Hentikan eksekusi agar tidak merender HTML halaman penuh saat request AJAX
}

// Ambil data kategori untuk halaman utama
$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY id DESC LIMIT 8");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - Beranda</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white; padding: 90px 0;
            position: relative;
        }
        .card-book { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-book:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .category-card { border: none; border-radius: 12px; transition: all 0.2s ease; background: white; }
        .category-card:hover { background-color: #0d6efd; color: white; transform: translateY(-3px); }
        .category-card:hover i, .category-card:hover h6 { color: white !important; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-book-open text-primary me-2"></i>PerpusDigital
            </a>
            <div class="ms-auto">
                <a href="backend/login.php" class="btn btn-outline-light btn-sm px-3 rounded-pill">
                    <i class="fas fa-user-shield me-1"></i> Login Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 fw-semibold">Pusat Literasi & Pengetahuan Modern</span>
            <h1 class="display-4 fw-bold mb-3">Jelajahi Dunia Lewat Buku</h1>
            <p class="lead text-light opacity-75 mb-4 mx-auto" style="max-width: 600px;">
                Temukan koleksi buku berkualitas tinggi secara instan dan mudah.
            </p>
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="input-group shadow-lg rounded-pill overflow-hidden bg-white p-1">
                        <input type="text" id="liveSearchInput" class="form-control form-control-lg border-0 ps-4 shadow-none" placeholder="Ketik judul buku, penulis, atau penerbit...">
                        <button class="btn btn-primary btn-lg rounded-pill px-4" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori Section -->
    <section id="kategori" class="py-5">
        <div class="container">
            <h3 class="fw-bold text-dark mb-3">Kategori Pilihan</h3>
            <div class="row g-3">
                <?php if (empty($categories)): ?>
                    <div class="col-12 text-muted">Belum ada kategori tersedia.</div>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="card category-card shadow-sm p-3 text-center">
                                <div class="card-body py-2">
                                    <i class="fas fa-folder-open fa-2x text-primary mb-2"></i>
                                    <h6 class="fw-bold mb-0 text-dark fs-6"><?= htmlspecialchars($cat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Katalog Buku dengan AJAX Live Search Container -->
    <section id="katalog" class="py-5 bg-white border-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark mb-0">Koleksi Buku Perpustakaan</h3>
                <span class="text-muted small">Pencarian berjalan secara instan</span>
            </div>
            <div class="row g-4" id="bookContainer">
                <!-- Data buku dimuat otomatis via AJAX -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="small text-white-50 mb-0">&copy; 2026 Sistem Informasi Perpustakaan Digital. All rights reserved.</p>
        </div>
    </footer>

    <!-- jQuery & Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load awal seluruh buku saat halaman pertama kali dibuka
            loadBooks('');

            // Fungsi Live Search ketika kotak pencarian diketik
            $('#liveSearchInput').on('keyup', function() {
                let query = $(this).val();
                loadBooks(query);
            });

            // Fungsi AJAX untuk mengambil data buku
            function loadBooks(query) {
                $.ajax({
                    url: 'index.php',
                    method: 'GET',
                    data: { ajax_search: '1', q: query },
                    success: function(response) {
                        $('#bookContainer').html(response);
                    }
                });
            }
        });
    </script>
</body>
</html>