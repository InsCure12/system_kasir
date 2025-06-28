<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM produk WHERE id_produk=$id"));

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    mysqli_query($con, "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok' WHERE id_produk=$id");
    header("Location: produk.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Ubah Produk</title></head>
<body>
<h2>Ubah Produk</h2>
<form method="POST">
    Nama Produk:<br>
    <input type="text" name="nama" value="<?= $data['nama_produk'] ?>" required><br>
    Harga:<br>
    <input type="number" name="harga" value="<?= $data['harga'] ?>" step="0.01" required><br>
    Stok:<br>
    <input type="number" name="stok" value="<?= $data['stok'] ?>" required><br><br>
    <button type="submit" name="update">Update</button>
</form>
</body>
</html>