<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($nama) || empty($jabatan) || empty($username) || empty($password)) {
        echo "<script>alert('Semua kolom wajib diisi!');</script>";
    } else {
        $cek = mysqli_query($con, "SELECT * FROM karyawan WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            echo "<script>alert('Username sudah digunakan!');</script>";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO karyawan (nama, jabatan, username, password) VALUES ('$nama', '$jabatan', '$username', '$password')";
            $result = mysqli_query($con, $sql);
            if ($result) {
                echo "<script>alert('Karyawan berhasil ditambahkan');window.location='karyawan.php';</script>";
                exit();
            } else {
                echo "<script>alert('Gagal menambah karyawan: " . mysqli_error($con) . "');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Karyawan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background: #f6f7fb; }
        .sidebar {
            background: #23235b;
            min-height: 100vh;
            color: #fff;
            padding: 0;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            margin-left: 16px;
        }
        .sidebar .nav-link, .sidebar .navbar-brand { 
            color: #fff; 
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { 
            background: #35357a; border-radius: 8px; 
        }
        .profile-card { 
            background: #35357a; 
            color: #fff; 
            padding: 16px; 
            margin-top: 20px; 
        }
        .table thead { 
            background: #4f3cc9; 
            color: #fff; 
        }
        .btn-danger { 
            background: #dc3545; 
            border: none; 
        }
        .btn-primary {
            background: #4f3cc9;
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
                <li class="nav-item"><a class="nav-link active" href="#">Karyawan</a></li>
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat_transaksi.php">Riwayat</a></li>
                <li class="nav-item"><a class="nav-link text-danger font-weight-bold" href="logout.php">Logout</a></li>
            </ul>
            <div class="profile-card">
                <div><b><?php echo $_SESSION['nama']; ?></b></div>
                <div style="font-size:13px;"><?php echo $_SESSION['jabatan']; ?></div>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="col-md-10 ml-sm-auto px-4">
            <div class="d-flex justify-content-between align-items-center pt-4 pb-2 mb-3 border-bottom">
                <h2>Dashboard Karyawan</h2>
            </div>
            <div class="card p-3">
                <div class="table-responsive">
                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahKaryawan">+ Tambah Karyawan
                    </button>
                    <div class="modal fade" id="modalTambahKaryawan" tabindex="-1" role="dialog" aria-labelledby="modalTambahKaryawanLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form method="POST" action="karyawan.php">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="modalTambahKaryawanLabel">Tambah Karyawan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nama:</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Jabatan:</label>
                                    <select name="jabatan" class="form-control" required>
                                        <option value="">-- Pilih Jabatan --</option>
                                        <option value="admin">Admin</option>
                                        <option value="kasir">Kasir</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Username:</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password:</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    </div>
                    <?php
                    $karyawan = mysqli_query($con, "SELECT * FROM karyawan ORDER BY nama ASC");
                    ?>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Username</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; while ($k = mysqli_fetch_assoc($karyawan)) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($k['nama']) ?></td>
                                <td><?= htmlspecialchars($k['jabatan']) ?></td>
                                <td><?= htmlspecialchars($k['username']) ?></td>
                                <td>
                                    <a href="hapus_karyawan.php?id=<?= $k['id_karyawan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus karyawan ini?')">Hapus</a>
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
</body>