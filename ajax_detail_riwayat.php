<?php
session_start();
include "koneksi.php";
$id_transaksi = intval($_GET['id']);
$transaksi = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT t.*, p.nama_pelanggan, k.nama AS nama_karyawan, m.nama_metode
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN karyawan k ON t.id_karyawan = k.id_karyawan
    LEFT JOIN metode_pembayaran m ON t.id_metode = m.id_metode
    WHERE t.id_transaksi = $id_transaksi
"));
$detail = mysqli_query($con, "
    SELECT dt.*, pr.nama_produk
    FROM detail_transaksi dt
    JOIN produk pr ON dt.id_produk = pr.id_produk
    WHERE dt.id_transaksi = $id_transaksi
");
?>
<div>
    <p><strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($transaksi['tanggal_transaksi'])) ?></p>
    <p><strong>Pelanggan:</strong> <?= htmlspecialchars($transaksi['nama_pelanggan']) ?></p>
    <p><strong>Petugas:</strong> <?= htmlspecialchars($transaksi['nama_karyawan']) ?></p>
    <p><strong>Metode Pembayaran:</strong> <?= $transaksi['nama_metode'] ? htmlspecialchars($transaksi['nama_metode']) : '-' ?></p>
    <p><strong>Total:</strong> Rp<?= number_format($transaksi['total']) ?></p>
    <!-- ...lanjutkan seperti sebelumnya... -->
</div>