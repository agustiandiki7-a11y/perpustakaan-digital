<?php
require_once __DIR__ . '/../../app/config/Database.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

mulaiSession();
cekRole(['admin', 'petugas']);

$db = new Database();
$pdo = $db->getConnection();

function cetakDetailEsc($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die('ID laporan tidak valid.');
}

try {
    $sql = "
        SELECT
            l.id,
            l.tanggal_pinjam,
            l.tanggal_kembali,
            l.status,
            l.denda,
            u.nama AS nama_peminjam,
            u.username,
            u.email,
            u.no_hp,
            u.alamat,
            b.kode_buku,
            b.judul,
            b.penulis,
            b.penerbit,
            b.tahun_terbit
        FROM loans l
        INNER JOIN users u ON l.user_id = u.id
        INNER JOIN books b ON l.book_id = b.id
        WHERE l.id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id
    ]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die('Data laporan tidak ditemukan.');
    }
} catch (PDOException $e) {
    die('Gagal mengambil data laporan.');
}

$status = strtolower(trim($data['status'] ?? ''));

$statusText = match ($status) {
    'menunggu' => 'Menunggu',
    'dipinjam' => 'Dipinjam',
    'dikembalikan' => 'Dikembalikan',
    'terlambat' => 'Terlambat',
    'dibatalkan' => 'Dibatalkan',
    default => ucfirst($status)
};

$tanggalPinjam = !empty($data['tanggal_pinjam'])
    ? date('d-m-Y', strtotime($data['tanggal_pinjam']))
    : '-';

$tanggalKembali = !empty($data['tanggal_kembali'])
    ? date('d-m-Y', strtotime($data['tanggal_kembali']))
    : '-';

$denda = (float)($data['denda'] ?? 0);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Detail Laporan #<?= (int)$data['id']; ?>
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 23px;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 6px 0;
            font-size: 17px;
            font-weight: normal;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        .line {
            margin-top: 15px;
            border-bottom: 3px solid #000;
        }

        .line-small {
            border-bottom: 1px solid #000;
            margin-top: 3px;
            margin-bottom: 25px;
        }

        .transaction {
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        .transaction-title {
            padding: 10px;
            font-weight: bold;
            background: #eee;
            border-bottom: 1px solid #000;
        }

        .row {
            display: flex;
            border-bottom: 1px solid #ddd;
        }

        .row:last-child {
            border-bottom: 0;
        }

        .label {
            width: 35%;
            padding: 9px;
            font-weight: bold;
        }

        .value {
            width: 65%;
            padding: 9px;
        }

        .status {
            font-weight: bold;
        }

        .footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
        }

        .signature {
            width: 220px;
            text-align: center;
        }

        .space {
            height: 70px;
        }

        .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .print-button {
            position: fixed;
            right: 15px;
            top: 15px;
            padding: 10px 18px;
            border: 0;
            border-radius: 5px;
            background: #000;
            color: #fff;
            cursor: pointer;
        }

        @media print {

            body {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 15mm;
            }

        }

    </style>

</head>

<body>

<button
    class="print-button no-print"
    onclick="window.print()">
    Cetak
</button>

<div class="container">

    <div class="header">

        <h1>PERPUSTAKAAN</h1>

        <h2>DETAIL LAPORAN PEMINJAMAN</h2>

        <p>
            Detail transaksi peminjaman buku
        </p>

        <div class="line"></div>
        <div class="line-small"></div>

    </div>

    <div class="transaction">

        <div class="transaction-title">
            Informasi Transaksi
        </div>

        <div class="row">
            <div class="label">
                ID Transaksi
            </div>

            <div class="value">
                #<?= (int)$data['id']; ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Tanggal Pinjam
            </div>

            <div class="value">
                <?= cetakDetailEsc($tanggalPinjam); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Tanggal Kembali
            </div>

            <div class="value">
                <?= cetakDetailEsc($tanggalKembali); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Status
            </div>

            <div class="value status">
                <?= cetakDetailEsc($statusText); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Denda
            </div>

            <div class="value">
                Rp <?= number_format($denda, 0, ',', '.'); ?>
            </div>
        </div>

    </div>

    <div class="transaction">

        <div class="transaction-title">
            Data Peminjam
        </div>

        <div class="row">
            <div class="label">
                Nama
            </div>

            <div class="value">
                <?= cetakDetailEsc($data['nama_peminjam']); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Username
            </div>

            <div class="value">
                <?= cetakDetailEsc($data['username']); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Email
            </div>

            <div class="value">
                <?= !empty($data['email'])
                    ? cetakDetailEsc($data['email'])
                    : '-'; ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                No. HP
            </div>

            <div class="value">
                <?= !empty($data['no_hp'])
                    ? cetakDetailEsc($data['no_hp'])
                    : '-'; ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Alamat
            </div>

            <div class="value">
                <?= !empty($data['alamat'])
                    ? nl2br(cetakDetailEsc($data['alamat']))
                    : '-'; ?>
            </div>
        </div>

    </div>

    <div class="transaction">

        <div class="transaction-title">
            Data Buku
        </div>

        <div class="row">
            <div class="label">
                Kode Buku
            </div>

            <div class="value">
                <?= cetakDetailEsc($data['kode_buku']); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Judul
            </div>

            <div class="value">
                <?= cetakDetailEsc($data['judul']); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Penulis
            </div>

            <div class="value">
                <?= !empty($data['penulis'])
                    ? cetakDetailEsc($data['penulis'])
                    : '-'; ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Penerbit
            </div>

            <div class="value">
                <?= !empty($data['penerbit'])
                    ? cetakDetailEsc($data['penerbit'])
                    : '-'; ?>
            </div>
        </div>

        <div class="row">
            <div class="label">
                Tahun Terbit
            </div>

            <div class="value">
                <?= !empty($data['tahun_terbit'])
                    ? cetakDetailEsc($data['tahun_terbit'])
                    : '-'; ?>
            </div>
        </div>

    </div>

    <div class="footer">

        <div class="signature">

            <div>
                Mengetahui,
            </div>

            <div>
                Petugas Perpustakaan
            </div>

            <div class="space"></div>

            <div class="name">
                ____________________
            </div>

        </div>

    </div>

</div>

<script>
window.addEventListener('load', function () {
    setTimeout(function () {
        window.print();
    }, 500);
});
</script>

</body>

</html>