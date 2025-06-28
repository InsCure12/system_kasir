<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // Cek apakah ada transaksi terkait pelanggan ini
    $cek = mysqli_query($con, "SELECT COUNT(*) as jml FROM transaksi WHERE id_pelanggan = $id");
    $jml = mysqli_fetch_assoc($cek)['jml'];
    if ($jml > 0) {
        echo "<script>alert('Tidak bisa hapus! Pelanggan masih memiliki transaksi.');window.location='pelanggan.php';</script>";
        exit();
    }
    mysqli_query($con, "DELETE FROM pelanggan WHERE id_pelanggan = $id");
}
header("Location: pelanggan.php");
exit();