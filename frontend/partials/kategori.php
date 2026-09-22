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
