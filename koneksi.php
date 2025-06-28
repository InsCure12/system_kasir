<?php
// File: koneksi.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kasir";

$con = mysqli_connect($host, $user, $pass, $db);
if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>