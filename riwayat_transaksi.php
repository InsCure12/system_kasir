<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

$transaksi = mysqli_query($con, "
    SELECT t.id_transaksi, t.tanggal_transaksi, p.nama_pelanggan, t.total, k.nama AS nama_karyawan, t.id_metode
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN karyawan k ON t.id_karyawan = k.id_karyawan
    ORDER BY t.tanggal_transaksi DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Transaksi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f6f7fb; }
        .sidebar {
            background: #23235b;
            min-height: 100vh;
            color: #fff;
            padding: 0;
        }
        .sidebar .nav-link, .sidebar .navbar-brand { 
            color: #fff; 
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { 
            background: #35357a; 
            border-radius: 8px; }
        .profile-card { 
            background: #35357a; 
            color: #fff; 
            padding: 16px; 
            margin-top: 20px; 
        }
        .navbar-brand { 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: #fff; 
            margin-left: 16px; }
        .main-content { 
            background: #fff; 
            border-radius: 16px; 
            padding: 24px 32px; 
            margin: 32px 0; 
            box-shadow: 0 2px 12px rgba(50, 117, 183, 0.07);
        }
        .table thead { 
            background: #f3f4f6; 
        }
        .table th { 
            font-weight: 600; 
        }
        .table td, .table th { 
            vertical-align: middle !important; 
        }
        .dashboard-title {
            font-size: 1.7rem;
            font-weight: bold;
            margin-bottom: 24px;
        }
        .btn-secondary {
            background-color: #35357a;
            border-color: #6c757d;
        }
        .btn-back {
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar py-4">
            <div class="navbar-brand mb-4">7Eleven Mart</div>
            <ul class="nav flex-column mb-4">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="karyawan.php">Karyawan</a></li>
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Member</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                <li class="nav-item"><a class="nav-link active" href="riwayat_transaksi.php">Riwayat</a></li>
                <li class="nav-item"><a class="nav-link text-danger font-weight-bold" href="logout.php">Logout</a></li>
            </ul>
            <div class="profile-card">
                <div><b><?php echo $_SESSION['nama']; ?></b></div>
                <div style="font-size:13px;"><?php echo $_SESSION['jabatan']; ?></div>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="col-md-10 ml-sm-auto px-4">
            <div class="main-content">
                <div class="dashboard-title">Riwayat Transaksi</div>
                <a href="transaksi.php" class="btn btn-secondary btn-back mb-3">&larr; Kembali ke Transaksi</a>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Petugas</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; while ($t = mysqli_fetch_assoc($transaksi)) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
                                <td><?= htmlspecialchars($t['nama_pelanggan']) ?></td>
                                <td>Rp<?= number_format($t['total'],0,',','.') ?></td>
                                <td><?= htmlspecialchars($t['nama_karyawan']) ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-detail" 
                                    data-id="<?= $t['id_transaksi'] ?>">
                                    Lihat
                                </button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailLabel">Detail Transaksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalDetailBody">
        <!-- Isi detail transaksi akan dimuat via AJAX -->
        <div class="text-center py-4">Memuat...</div>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).on('click', '.btn-detail', function() {
    var id = $(this).data('id');
    $('#modalDetailBody').html('<div class="text-center py-4">Memuat...</div>');
    $('#modalDetail').modal('show');
    $.get('detail_riwayat.php', {id: id}, function(res) {
        $('#modalDetailBody').html(res);
    });
});
</script>
</body>
</html>