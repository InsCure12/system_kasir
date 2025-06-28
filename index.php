<?php
// File: index.php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
echo "Selamat datang, " . $_SESSION['user']['nama'];
echo " | <a href='produk.php'>Produk</a> | <a href='transaksi.php'>Transaksi</a> | <a href='logout.php'>Logout</a>";
?>
