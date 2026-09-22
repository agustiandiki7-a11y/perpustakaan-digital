<?php
$rootPath = dirname(__DIR__, 3);
require_once $rootPath . '/app/config/Database.php';
require_once $rootPath . '/app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $pdo = $database->connect();

    $id       = $_POST['id'] ?? null;
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = 'peminjam';

    if (empty($id) || !is_numeric($id) || empty($nama) || empty($email)) {
        $_SESSION['error'] = 'Data tidak lengkap atau ID tidak valid!';
        header('Location: tabel_pengguna.php');
        exit;
    }

    try {
        $stmtCek = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmtCek->execute([':email' => $email, ':id' => $id]);
        if ($stmtCek->rowCount() > 0) {
            $_SESSION['error'] = 'Email tersebut sudah digunakan oleh akun lain!';
            header("Location: edit.php?id=$id");
            exit;
        }

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nama = :nama, email = :email, password = :password, role = :role WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama'     => $nama,
                ':email'    => $email,
                ':password' => $hashedPassword,
                ':role'     => $role,
                ':id'       => $id
            ]);
        } else {
            $sql = "UPDATE users SET nama = :nama, email = :email, role = :role WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama'  => $nama,
                ':email' => $email,
                ':role'  => $role,
                ':id'    => $id
            ]);
        }

        $_SESSION['success'] = 'Data pengguna berhasil diperbarui!';
        header('Location: tabel_pengguna.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal memperbarui data: ' . $e->getMessage();
        header("Location: edit.php?id=$id");
        exit;
    }
} else {
    header('Location: tabel_pengguna.php');
    exit;
}