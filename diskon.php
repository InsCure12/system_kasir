<?php
// File: diskon.php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

// Proses simpan jika form disubmit
if (isset($_POST['simpan'])) {
    $nama   = $_POST['nama'];
    $tipe   = $_POST['tipe'];
    $nilai  = $_POST['nilai'];
    $mulai  = $_POST['berlaku_mulai'];
    $sampai = $_POST['berlaku_sampai'];

    if ($sampai < $mulai) {
        echo "<script>alert('Tanggal akhir harus lebih besar atau sama dengan tanggal mulai!');</script>";
    } else {
        mysqli_query($con, "INSERT INTO diskon (nama_diskon, tipe_diskon, nilai, berlaku_mulai, berlaku_sampai)
                            VALUES ('$nama', '$tipe', '$nilai', '$mulai', '$sampai')");
        echo "<script>alert('Diskon berhasil ditambahkan!');window.location='diskon.php';</script>";
        exit();
    }
}

$diskon = mysqli_query($con, "SELECT * FROM diskon ORDER BY berlaku_mulai DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Diskon</title>
</head>
<body>
    <h2>Daftar Diskon</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama Diskon</th>
            <th>Tipe</th>
            <th>Nilai</th>
            <th>Periode</th>
        </tr>
        <?php $no = 1; while ($d = mysqli_fetch_assoc($diskon)) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $d['nama_diskon'] ?></td>
            <td><?= $d['tipe_diskon'] ?></td>
            <td><?= $d['tipe_diskon'] == 'persen' ? $d['nilai'] . '%' : 'Rp' . number_format($d['nilai']) ?></td>
            <td><?= $d['berlaku_mulai'] ?> s/d <?= $d['berlaku_sampai'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <h2>Tambah Diskon</h2>
    <form method="POST">
        <label>Nama Diskon:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Tipe Diskon:</label><br>
        <select name="tipe" required>
            <option value="">-- Pilih Tipe --</option>
            <option value="persen">Persentase (%)</option>
            <option value="nominal">Nominal (Rp)</option>
        </select><br><br>

        <label>Nilai Diskon:</label><br>
        <input type="number" name="nilai" step="0.01" required><br><br>

        <label>Berlaku Mulai:</label><br>
        <input type="date" name="berlaku_mulai" required><br><br>

        <label>Berlaku Sampai:</label><br>
        <input type="date" name="berlaku_sampai" required><br><br>

        <button type="submit" name="simpan">Simpan</button>
    </form>
</body>
</html>
