<?php
include "koneksi.php";
$id = intval($_GET['id']);

// Ambil nama file gambar
$q = mysqli_query($con, "SELECT gambar FROM produk WHERE id_produk=$id");
$d = mysqli_fetch_assoc($q);
if ($d && !empty($d['gambar']) && file_exists("uploads/" . $d['gambar'])) {
    unlink("uploads/" . $d['gambar']);
}

// Hapus produk dari database
mysqli_query($con, "DELETE FROM produk WHERE id_produk=$id");

// Redirect kembali ke produk.php
header("Location: produk.php");
exit;
?>