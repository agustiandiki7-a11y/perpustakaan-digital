<?php
session_start();
include '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $username = $_POST['username'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login'] = true;

            if ($user['role'] === 'admin') {
                header("Location: ../../backend/admin/index.php");
                exit();
            } elseif ($user['role'] === 'petugas') {
                header("Location: ../../backend/petugas/index.php");
                exit();
            } elseif ($user['role'] === 'peminjam') {
                header("Location: ../../index.php");
                exit();
            } else {
                echo "Akun anda tidak ditemukan, silahkan konfirmasi kepada admin kami, Terimakasih";
                exit();
            }
        } else {
            echo "Password anda salah. <a href='../pages/login.php'>Silahkan Coba Lagi</a>";
        }
    } else {
        echo "login gagal. <a href='../pages/login.php'>Silahkan Coba Lagi</a>";
    }

    $conn->close();
}