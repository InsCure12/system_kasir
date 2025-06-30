<?php
// File: metode_pembayaran.php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

// Jika tidak ada data metode pembayaran, tambahkan default
$cek = mysqli_num_rows(mysqli_query($con, "SELECT * FROM metode_pembayaran"));
if ($cek == 0) {
    mysqli_query($con, "INSERT INTO metode_pembayaran (nama_metode) VALUES ('Cash'), ('Qris'), ('Debit')");
}

$metode = mysqli_query($con, "SELECT * FROM metode_pembayaran");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Metode Pembayaran</title>
</head>
<body>
    <h2>Daftar Metode Pembayaran</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama Metode</th>
        </tr>
        <?php $no = 1; while ($m = mysqli_fetch_assoc($metode)) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $m['nama_metode'] ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
