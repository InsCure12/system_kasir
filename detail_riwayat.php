<?php
// File: detail_riwayat.php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

if (!isset($_GET['id'])) {
    echo "<p>ID transaksi tidak ditemukan. <a href='riwayat_transaksi.php'>Kembali</a></p>";
    exit();
}

$id_transaksi = $_GET['id'];
$transaksi = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT t.*, p.nama_pelanggan, k.nama AS nama_karyawan, m.nama_metode, b.jumlah_bayar, b.kembalian
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN karyawan k ON t.id_karyawan = k.id_karyawan
    LEFT JOIN pembayaran b ON t.id_transaksi = b.id_transaksi
    LEFT JOIN metode_pembayaran m ON b.id_metode = m.id_metode
    WHERE t.id_transaksi = $id_transaksi
"));

$detail = mysqli_query($con, "
    SELECT dt.*, pr.nama_produk
    FROM detail_transaksi dt
    JOIN produk pr ON dt.id_produk = pr.id_produk
    WHERE dt.id_transaksi = $id_transaksi
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Transaksi #<?= $id_transaksi ?></title>
</head>
<body>
    <h2>Detail Transaksi #<?= $id_transaksi ?></h2>
    <p><strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($transaksi['tanggal_transaksi'])) ?></p>
    <p><strong>Pelanggan:</strong> <?= $transaksi['nama_pelanggan'] ?></p>
    <p><strong>Petugas:</strong> <?= $transaksi['nama_karyawan'] ?></p>
    <p><strong>Metode Pembayaran:</strong> <?= $transaksi['nama_metode'] ?></p>
    <p><strong>Total:</strong> Rp<?= number_format($transaksi['total']) ?></p>
    <p><strong>Dibayar:</strong> Rp<?= number_format($transaksi['jumlah_bayar']) ?></p>
    <p><strong>Kembalian:</strong> Rp<?= number_format($transaksi['kembalian']) ?></p>

    <h3>Produk</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
        <?php $no = 1; while ($d = mysqli_fetch_assoc($detail)) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $d['nama_produk'] ?></td>
            <td><?= $d['jumlah'] ?></td>
            <td>Rp<?= number_format($d['subtotal']) ?></td>
        </tr>
        <?php } ?>
    </table>
    <p><a href="riwayat_transaksi.php">Kembali ke Riwayat</a></p>
</body>
</html>
