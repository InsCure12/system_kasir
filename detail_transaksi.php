<?php
// File: detail_transaksi.php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];
    $id_diskon = isset($_POST['id_diskon']) ? $_POST['id_diskon'] : null;

    $produk = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM produk WHERE id_produk = $id_produk"));
    $harga = $produk['harga'];
    $nama_produk = $produk['nama_produk'];
    $stok = $produk['stok'];
    $subtotal = $harga * $jumlah;

    $diskon = null;
    $nilai_diskon = 0;
    if ($id_diskon) {
        $diskon = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM diskon WHERE id_diskon = $id_diskon"));
        if ($diskon['tipe_diskon'] == 'persen') {
            $nilai_diskon = $subtotal * $diskon['nilai'] / 100;
        } else {
            $nilai_diskon = $diskon['nilai'];
        }
    }

    $total = $subtotal - $nilai_diskon;
} else {
    echo "<p>Data tidak tersedia. <a href='transaksi.php'>Kembali</a></p>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Transaksi</title>
</head>
<body>
    <h2>Detail Transaksi</h2>
    <p><strong>Produk:</strong> <?= $nama_produk ?></p>
    <p><strong>Jumlah:</strong> <?= $jumlah ?></p>
    <p><strong>Harga Satuan:</strong> Rp<?= number_format($harga) ?></p>
    <p><strong>Subtotal:</strong> Rp<?= number_format($subtotal) ?></p>
    <?php if ($diskon) { ?>
        <p><strong>Diskon:</strong> <?= $diskon['nama_diskon'] ?> (<?= $diskon['tipe_diskon'] == 'persen' ? $diskon['nilai'].'%' : 'Rp'.number_format($diskon['nilai']) ?>)</p>
        <p><strong>Potongan:</strong> Rp<?= number_format($nilai_diskon) ?></p>
    <?php } ?>
    <p><strong>Total Bayar:</strong> Rp<?= number_format($total) ?></p>

    <form method="POST" action="pembayaran.php">
        <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">
        <input type="hidden" name="id_produk" value="<?= $id_produk ?>">
        <input type="hidden" name="jumlah" value="<?= $jumlah ?>">
        <input type="hidden" name="id_diskon" value="<?= $id_diskon ?>">
        <input type="hidden" name="total" value="<?= $total ?>">

        <button type="submit">Proses & Simpan Transaksi</button>
    </form>
    <p><a href="transaksi.php">Kembali ke transaksi</a></p>
</body>
</html>
