<?php
// File: transaksi.php
session_start();
include "koneksi.php";

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit;
}

// Ambil data
$produk     = mysqli_query($con, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk");
$pelanggan  = mysqli_query($con, "SELECT * FROM pelanggan ORDER BY nama_pelanggan");
$diskon     = mysqli_query($con, "
    SELECT * FROM diskon 
    WHERE CURDATE() BETWEEN berlaku_mulai AND berlaku_sampai 
    ORDER BY nama_diskon
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Baru</title>
</head>
<body>
    <h2>Transaksi Baru</h2>
    <form method="POST" action="detail_transaksi.php">
        
        <!-- Pilih Pelanggan -->
        <label>Pelanggan:</label><br>
        <select name="id_pelanggan" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php while($p = mysqli_fetch_assoc($pelanggan)) { ?>
                <option value="<?= $p['id_pelanggan'] ?>"><?= $p['nama_pelanggan'] ?></option>
            <?php } ?>
        </select><br><br>

        <!-- Pilih Produk -->
        <label>Produk:</label><br>
        <select name="id_produk" required>
            <option value="">-- Pilih Produk --</option>
            <?php while($pr = mysqli_fetch_assoc($produk)) { ?>
                <option value="<?= $pr['id_produk'] ?>" data-harga="<?= $pr['harga'] ?>">
                    <?= $pr['nama_produk'] ?> - Rp<?= number_format($pr['harga']) ?> (Stok: <?= $pr['stok'] ?>)
                </option>
            <?php } ?>
        </select><br><br>

        <!-- Jumlah -->
        <label>Jumlah:</label><br>
        <input type="number" name="jumlah" min="1" required><br><br>

       <!-- Pilih Diskon -->
        <label>Diskon:</label><br>
        <select name="id_diskon">
            <option value="">(Tanpa Diskon)</option>
                <?php while($d = mysqli_fetch_assoc($diskon)) { ?>
            <option value="<?= $d['id_diskon'] ?>">
                <?= $d['nama_diskon'] ?> (<?= $d['tipe_diskon'] == 'persen' ? $d['nilai'].'%' : 'Rp'.number_format($d['nilai']) ?>)
            </option>
            <?php } ?>
        </select><br><br>

        <!-- Submit -->
        <button type="submit">Lanjutkan ke Detail Transaksi</button>
    </form>

    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>
</body>
</html>
