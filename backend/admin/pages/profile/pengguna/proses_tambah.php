<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $pdo = $database->connect();

    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = 'peminjam';

    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Nama, Username, Email, dan Password wajib diisi!';
        header('Location: tambah.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Format email tidak valid!';
        header('Location: tambah.php');
        exit;
    }

    try {
        // Cek duplikasi username atau email
        $stmtCek = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $stmtCek->execute([':email' => $email, ':username' => $username]);
        if ($stmtCek->rowCount() > 0) {
            $_SESSION['error'] = 'Username atau Email tersebut sudah terdaftar!';
            header('Location: tambah.php');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (nama, username, email, password, role) VALUES (:nama, :username, :email, :password, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama'     => $nama,
            ':username' => $username,
            ':email'    => $email,
            ':password' => $hashedPassword,
            ':role'     => $role
        ]);

        $_SESSION['success'] = 'Akun peminjam berhasil ditambahkan!';
        header('Location: tabel_pengguna.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
        header('Location: tambah.php');
        exit;
    }
} else {
    header('Location: tambah.php');
    exit;
}