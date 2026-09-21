<?php

function mulaiSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        session_start();
    }
}

function sudahLogin(): bool
{
    return isset($_SESSION['status'])
        && $_SESSION['status'] === 'login'
        && isset($_SESSION['user_id']);
}

function cekLogin(): void
{
    mulaiSession();

    if (!sudahLogin()) {
        header('Location: /perpustakaan/backend/login.php');
        exit;
    }

    // Timeout sesi otomatis jika tidak aktif selama 30 menit
    if (
        isset($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity']) > 1800
    ) {
        session_unset();
        session_destroy();

        header('Location: /perpustakaan/backend/login.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function cekRole(array $roles): void
{
    cekLogin();

    if (
        !isset($_SESSION['role']) ||
        !in_array($_SESSION['role'], $roles, true)
    ) {
        http_response_code(403);
        exit('Akses ditolak: Anda tidak memiliki hak akses ke halaman ini.');
    }
}

function userLogin(): ?array
{
    mulaiSession();

    if (!sudahLogin()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['nama'] ?? '',
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['role'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'foto' => $_SESSION['foto'] ?? ''
    ];
}