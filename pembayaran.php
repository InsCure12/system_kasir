<?php
// File: pembayaran.php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

// Validasi input
if (!isset($_POST['id_pelanggan'], $_POST['id_produk'], $_POST['jumlah'], $_POST['total'])) {
    echo "<p>Data tidak lengkap. Silakan kembali ke <a href='transaksi.php'>transaksi</a>.</p>";
    exit();
}

$id_pelanggan = $_POST['id_pelanggan'];
$id_produk = $_POST['id_produk'];
$jumlah = $_POST['jumlah'];
$id_diskon = $_POST['id_diskon'] ?: "NULL";
$total = $_POST['total'];
$id_karyawan = $_SESSION['id_karyawan'];
$tanggal = date('Y-m-d H:i:s');

$metode = mysqli_query($con, "SELECT * FROM metode_pembayaran");

if (isset($_POST['bayar'])) {
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $id_metode = $_POST['id_metode'];

    $metode_nama = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama_metode FROM metode_pembayaran WHERE id_metode = $id_metode"));

    if (strtolower($metode_nama['nama_metode']) === 'cash') {
        $kembalian = $jumlah_bayar - $total;
    } else {
        $kembalian = 0;
    }

    mysqli_query($con, "INSERT INTO transaksi (id_pelanggan, id_diskon, id_karyawan, tanggal_transaksi, total)
        VALUES ($id_pelanggan, $id_diskon, $id_karyawan, '$tanggal', $total)");
    $id_transaksi = mysqli_insert_id($con);

    $data_produk = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM produk WHERE id_produk=$id_produk"));
    $harga = $data_produk['harga'];
    $subtotal = $harga * $jumlah;

    mysqli_query($con, "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal)
        VALUES ($id_transaksi, $id_produk, $jumlah, $subtotal)");

    mysqli_query($con, "INSERT INTO pembayaran (id_transaksi, id_metode, jumlah_bayar, kembalian)
        VALUES ($id_transaksi, $id_metode, $jumlah_bayar, $kembalian)");

    mysqli_query($con, "UPDATE produk SET stok = stok - $jumlah WHERE id_produk = $id_produk");

    echo "<script>alert('Transaksi berhasil');window.location='riwayat_transaksi.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran</title>
    <script>
    function updateKembalian() {
        const metode = document.getElementById('id_metode');
        const selectedText = metode.options[metode.selectedIndex].text.toLowerCase();
        const bayar = parseFloat(document.getElementById('jumlah_bayar').value);
        const total = parseFloat(<?= $total ?>);

        if (selectedText === 'cash') {
            const kembali = bayar - total;
            document.getElementById('kembalian_display').innerText = 'Rp' + kembali.toFixed(2);
        } else {
            document.getElementById('kembalian_display').innerText = 'Rp0.00';
        }
    }
    </script>
</head>
<body>
    <h2>Pembayaran</h2>
    <p>Total yang harus dibayar: <strong>Rp<?= number_format($total) ?></strong></p>

    <form method="POST">
        <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">
        <input type="hidden" name="id_produk" value="<?= $id_produk ?>">
        <input type="hidden" name="jumlah" value="<?= $jumlah ?>">
        <input type="hidden" name="id_diskon" value="<?= $id_diskon ?>">
        <input type="hidden" name="total" value="<?= $total ?>">

        <label>Jumlah Bayar:</label><br>
        <input type="number" name="jumlah_bayar" id="jumlah_bayar" oninput="updateKembalian()" required><br><br>

        <label>Metode Pembayaran:</label><br>
        <select name="id_metode" id="id_metode" onchange="updateKembalian()" required>
            <?php mysqli_data_seek($metode, 0); while ($m = mysqli_fetch_assoc($metode)) { ?>
                <option value="<?= $m['id_metode'] ?>"><?= $m['nama_metode'] ?></option>
            <?php } ?>
        </select><br><br>

        <p>Kembalian: <span id="kembalian_display">Rp0.00</span></p>

        <button type="submit" name="bayar">Bayar & Simpan Transaksi</button>
    </form>
    <p><a href="detail_transaksi.php">Kembali ke Detail Transaksi</a></p>
</body>
</html>
