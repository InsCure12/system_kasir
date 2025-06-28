<?php
// File: riwayat_transaksi.php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

$transaksi = mysqli_query($con, "
    SELECT t.id_transaksi, t.tanggal_transaksi, p.nama_pelanggan, t.total, k.nama AS nama_karyawan, m.nama_metode
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN karyawan k ON t.id_karyawan = k.id_karyawan
    LEFT JOIN pembayaran pb ON t.id_transaksi = pb.id_transaksi
    LEFT JOIN metode_pembayaran m ON pb.id_metode = m.id_metode
    ORDER BY t.tanggal_transaksi DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Transaksi</title>
</head>
<body>
    <h2>Riwayat Transaksi</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Total</th>
            <th>Metode</th>
            <th>Petugas</th>
        </tr>
        <?php $no = 1; while ($t = mysqli_fetch_assoc($transaksi)) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d-m-Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
            <td><?= $t['nama_pelanggan'] ?></td>
            <td>Rp<?= number_format($t['total']) ?></td>
            <td><?= $t['nama_metode'] ?></td>
            <td><?= $t['nama_karyawan'] ?></td>
        </tr>
        <?php } ?>
        <p><a href="transaksi.php">Kembali ke Transaksi</a></p>
    </table>
</body>
</html>
