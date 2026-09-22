<?php

require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();

$user = userLogin();

$nama = $user['nama'] ?? 'Pengguna';
$role = $user['role'] ?? '';

?>

<!-- Navbar -->
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">

    <div class="container-fluid">

        <!-- Tombol Sidebar Mobile -->
        <nav
            class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
        >
        </nav>

        <!-- Bagian Kanan Navbar -->
        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

            <!-- Search -->
            <li class="nav-item topbar-icon dropdown hidden-caret">
                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="searchDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <i class="fa fa-search"></i>
                </a>

                <ul class="dropdown-menu dropdown-search animated fadeIn">

                    <form class="navbar-left navbar-form nav-search">

                        <div class="input-group">

                            <input
                                type="text"
                                placeholder="Cari..."
                                class="form-control"
                            />

                            <div class="input-group-text">
                                <i class="fa fa-search search-icon"></i>
                            </div>

                        </div>

                    </form>

                </ul>
            </li>


            <!-- Notifikasi -->
            <li class="nav-item topbar-icon dropdown hidden-caret">

                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="notifDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >

                    <i class="fa fa-bell"></i>

                    <span class="notification">
                        0
                    </span>

                </a>

                <ul
                    class="dropdown-menu notif-box animated fadeIn"
                    aria-labelledby="notifDropdown"
                >

                    <li>

                        <div class="dropdown-title">
                            Tidak ada notifikasi baru
                        </div>

                    </li>

                    <li>

                        <div class="notif-scroll scrollbar-outer">

                            <div class="notif-center">

                                <!-- Notifikasi nanti bisa ditambahkan -->

                            </div>

                        </div>

                    </li>

                </ul>

            </li>


            <!-- Profil User -->
            <li class="nav-item topbar-user dropdown hidden-caret">

                <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                >

                    <div class="avatar-sm">

                        <?php if (!empty($user['foto'])): ?>

                            <img
                                src="/perpustakaan/uploads/profil/<?= htmlspecialchars($user['foto']) ?>"
                                alt="Foto Profil"
                                class="avatar-img rounded-circle"
                            >

                        <?php else: ?>

                            <div
                                class="avatar-img rounded-circle d-flex align-items-center justify-content-center"
                                style="
                                    background: #6861ce;
                                    color: white;
                                    font-size: 16px;
                                "
                            >
                                <i class="fas fa-user"></i>
                            </div>

                        <?php endif; ?>

                    </div>

                    <span class="profile-username">

                        <span class="op-7">
                            Halo,
                        </span>

                        <span class="fw-bold">
                            <?= htmlspecialchars($nama) ?>
                        </span>

                    </span>

                </a>


                <!-- Dropdown Profil -->
                <ul class="dropdown-menu dropdown-user animated fadeIn">

                    <div class="dropdown-user-scroll scrollbar-outer">

                        <li>

                            <div class="user-box">

                                <div class="avatar-lg">

                                    <?php if (!empty($user['foto'])): ?>

                                        <img
                                            src="/perpustakaan/uploads/profil/<?= htmlspecialchars($user['foto']) ?>"
                                            alt="Foto Profil"
                                            class="avatar-img rounded"
                                        >

                                    <?php else: ?>

                                        <div
                                            class="avatar-img rounded d-flex align-items-center justify-content-center"
                                            style="
                                                background: #6861ce;
                                                color: white;
                                                font-size: 30px;
                                                width: 100%;
                                                height: 100%;
                                            "
                                        >
                                            <i class="fas fa-user"></i>
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <div class="u-text">

                                    <h4>
                                        <?= htmlspecialchars($nama) ?>
                                    </h4>

                                    <p class="text-muted">
                                        <?= htmlspecialchars($role) ?>
                                    </p>

                                </div>

                            </div>

                        </li>


                        <li>

                            <div class="dropdown-divider"></div>

                            <a
                                class="dropdown-item"
                                href="/perpustakaan/backend/profil.php"
                            >
                                <i class="fas fa-user me-2"></i>
                                Profil Saya
                            </a>


                            <div class="dropdown-divider"></div>


                            <a
                                class="dropdown-item"
                                href="/perpustakaan/backend/logout.php"
                                onclick="return confirm('Yakin ingin keluar dari sistem?');"
                            >
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>

                        </li>

                    </div>

                </ul>

            </li>

        </ul>

    </div>

</nav>
<!-- End Navbar -->