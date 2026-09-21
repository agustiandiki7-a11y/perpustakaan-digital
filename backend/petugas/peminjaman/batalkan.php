<?php
require_once __DIR__ . '/../../app/config/database.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM peminjaman WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: ../index.php?pesan=sukses_hapus");
        exit;
    } catch (PDOException $e) {
        echo "Gagal menghapus data: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
    exit;
}