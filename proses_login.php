<?php
// File: proses_login.php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $result = mysqli_query($con, "SELECT * FROM karyawan WHERE username = '$username' AND password = '$password'");
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $_SESSION['id_karyawan'] = $data['id_karyawan'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['jabatan'] = $data['jabatan'];

        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Login gagal! Username atau password salah.');window.location='login.php';</script>";
    }
} else {
    header("Location: login.php");
    exit();
}
