<?php

require_once __DIR__ . '/../app/helpers/auth.php';

mulaiSession();

if (sudahLogin()) {
    header('Location: index.php');
    exit;
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Perpustakaan Digital</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo h1 {
            font-size: 28px;
            color: #222;
            margin-bottom: 8px;
        }

        .logo p {
            color: #777;
            font-size: 14px;
        }

        .alert {
            background: #ffe5e5;
            color: #b00020;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
        }

        .form-group input:focus {
            border-color: #333;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #222;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #444;
        }

        .register {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        .register a {
            color: #222;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="login-card">

            <div class="logo">
                <h1>Perpustakaan Digital</h1>
                <p>Silakan masuk ke akun Anda</p>
            </div>

            <?php if ($error): ?>
                <div class="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST">

                <div class="form-group">
                    <label for="username">Username</label>

                    <input
                        type="text"id="username"name="username" required maxlength="50" autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"id="password" name="password"required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login">
                    Login
                </button>

            </form>

            <div class="register">
                Belum punya akun?
                <a href="register.php">Daftar</a>
            </div>

        </div>

    </div>

</body>

</html>