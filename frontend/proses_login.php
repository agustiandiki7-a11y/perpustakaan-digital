<?php

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/helpers/auth.php';

mulaiSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

try {

    $database = new Database();
    $db = $database->connect();

    $userModel = new User($db);

    $user = $userModel->findByUsername($username);

    if (!$user) {
        $_SESSION['login_error'] = 'Username atau password salah.';
        header('Location: login.php');
        exit;
    }

    if ($user['status'] !== 'aktif') {
        $_SESSION['login_error'] = 'Akun Anda tidak aktif.';
        header('Location: login.php');
        exit;
    }

    if (!$userModel->verifyPassword($password, $user['password'])) {
        $_SESSION['login_error'] = 'Username atau password salah.';
        header('Location: login.php');
        exit;
    }

    // Halaman frontend hanya untuk peminjam
    if ($user['role'] !== 'peminjam') {
        $_SESSION['login_error'] = 'Silakan gunakan halaman login sesuai akun Anda.';
        header('Location: login.php');
        exit;
    }

    // Regenerasi session setelah login
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'       => $user['id'],
        'nama'     => $user['nama'],
        'username' => $user['username'],
        'role'     => $user['role'],
        'email'    => $user['email'],
        'foto'     => $user['foto']
    ];

    header('Location: index.php');
    exit;

} catch (Throwable $e) {

    $_SESSION['login_error'] = 'Terjadi kesalahan pada sistem.';
    header('Location: login.php');
    exit;
}