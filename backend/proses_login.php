<?php
// Mulai session dengan pengaturan cookie yang aman (Mencegah Session Hijacking)
session_start([
    'cookie_lifetime' => 0,
    'cookie_secure' => isset($_SERVER['HTTPS']), // Aktif jika menggunakan HTTPS
    'cookie_httponly' => true, // Mencegah akses cookie via JavaScript (XSS mitigation)
    'cookie_samesite' => 'Strict' // Mencegah serangan CSRF
]);

require_once __DIR__ . '/../app/config/Database.php';

// 1. Proteksi CSRF Token: Generate token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Hanya izinkan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// 2. Validasi CSRF Token dari form
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Validasi keamanan gagal (CSRF Token tidak valid).';
    header('Location: login.php');
    exit;
}

// Rate Limiting (Brute Force Protection)
if (!isset($_SESSION['login_attempt'])) {
    $_SESSION['login_attempt'] = 0;
}
if (!isset($_SESSION['login_block_until'])) {
    $_SESSION['login_block_until'] = 0;
}

if (time() < $_SESSION['login_block_until']) {
    $_SESSION['error'] = 'Terlalu banyak percobaan login. Silakan coba beberapa saat lagi.';
    header('Location: login.php');
    exit;
}

// Ambil dan bersihkan input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

try {
    $database = new Database();
    $pdo = $database->connect();

    // 3. Aman dari SQL Injection menggunakan Prepared Statements murni
    $stmt = $pdo->prepare("
        SELECT id, nama, username, password, role, status
        FROM users
        WHERE username = :username
        LIMIT 1
    ");

    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['login_attempt']++;
        if ($_SESSION['login_attempt'] >= 5) {
            $_SESSION['login_block_until'] = time() + 300; // Blokir 5 menit jika 5x salah
        }
        $_SESSION['error'] = 'Username atau password salah.';
        header('Location: login.php');
        exit;
    }

    if ($user['status'] !== 'aktif') {
        $_SESSION['error'] = 'Akun Anda tidak aktif.';
        header('Location: login.php');
        exit;
    }

    // Reset percobaan login jika berhasil
    $_SESSION['login_attempt'] = 0;
    $_SESSION['login_block_until'] = 0;

    // 4. Regenerasi Session ID untuk mencegah Session Fixation / Insecure Deserialization
    session_regenerate_id(true);

    // Simpan data aman ke session
    $_SESSION['status'] = 'login';
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Redirect berdasarkan role
    if ($user['role'] === 'admin') {
        header('Location: admin/index.php');
        exit;
    } elseif ($user['role'] === 'petugas') {
        header('Location: petugas/index.php');
        exit;
    } else {
        header('Location: ../frontend/index.php');
        exit;
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = 'Terjadi kesalahan pada sistem.';
    header('Location: login.php');
    exit;
}
?>