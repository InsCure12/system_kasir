<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

$id = $_GET['id'];
mysqli_query($con, "DELETE FROM produk WHERE id_produk=$id");
header("Location: produk.php");
?>