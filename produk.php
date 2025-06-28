<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

// Proses tambah produk
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Proses upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $namaFile = uniqid('produk_') . '.' . $ext;
        if (!is_dir('uploads')) mkdir('uploads');
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $namaFile);
        $gambar = $namaFile;
    }

    if (empty($nama) || empty($harga) || empty($stok) || empty($gambar)) {
        echo "<script>alert('Semua kolom wajib diisi!');</script>";
    } else {
        $cek = mysqli_query($con, "SELECT * FROM produk WHERE nama_produk='$nama'");
        if (mysqli_num_rows($cek) > 0) {
            echo "<script>alert('Nama produk sudah ada!');</script>";
        } else {
            $sql = "INSERT INTO produk (nama_produk, harga, stok, gambar) VALUES ('$nama', '$harga', '$stok', '$gambar')";
            $result = mysqli_query($con, $sql);
            if ($result) {
                echo "<script>alert('Produk berhasil ditambahkan');window.location='produk.php';</script>";
                exit();
            } else {
                echo "<script>alert('Gagal menambah produk: " . mysqli_error($con) . "');</script>";
            }
        }
    }
}

$produk = mysqli_query($con, "SELECT * FROM produk ORDER BY nama_produk ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f6f7fb;
        }
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
            box-shadow: 0 2px 12px rgba(44,62,80,0.07);
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
        .btn-danger { 
            background: #dc3545; border: none; 
        }
        .btn-warning { 
            background: #ffb74d; 
            border: none; 
            color: #23235b; 
        }
        .btn-primary { 
            background: #4f3cc9; 
            border: none; 
        }
        .search-bar { 
            width: 320px; 
        }
        .stock-low { 
            color: #e67e22; 
            font-weight: 600; 
        }
        .stock-out { 
            color: #e74c3c; 
            font-weight: 600; 
        }
        .stock-ok { 
            color: #2ecc71; 
            font-weight: 600;
        }
        .table-img { 
            width: 38px; 
            height: 38px; 
            object-fit: cover; 
            border-radius: 6px; 
        }
        .bulk-actions { 
            font-size: 15px; 
            color: #555; 
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
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link active" href="produk.php">Produk</a></li>
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
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambahProduk"><i class="bi bi-plus"></i>Tambah Produk</button>
                        <span class="bulk-actions ml-3"></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <input type="text" class="form-control search-bar" placeholder="Search Products" id="searchInput">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="produkTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($p = mysqli_fetch_assoc($produk)) { ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span><?= htmlspecialchars($p['nama_produk']) ?></span>
                                    </div>
                                </td>
                                <td>Rp<?= number_format($p['harga']) ?></td>
                                <td>
                                    <?php
                                    if ($p['stok'] == 0) {
                                        echo '<span class="stock-out">Out of Stock</span>';
                                    } elseif ($p['stok'] < 10) {
                                        echo '<span class="stock-low">' . $p['stok'] . ' Stock Low</span>';
                                    } else {
                                        echo '<span class="stock-ok">' . $p['stok'] . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="edit_produk.php?id=<?= $p['id_produk'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                    <a href="hapus_produk.php?id=<?= $p['id_produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal Tambah Produk -->
            <div class="modal fade" id="modalTambahProduk" tabindex="-1" role="dialog" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form method="POST" action="produk.php" enctype="multipart/form-data">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTambahProdukLabel">Tambah Produk</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nama Produk:</label>
                                    <input type="text" name="nama_produk" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Harga:</label>
                                    <input type="number" name="harga" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Stok:</label>
                                    <input type="number" name="stok" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Gambar Produk:</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*" required>
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
        </main>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Checkbox select all
    document.getElementById('selectAll').onclick = function() {
        var checks = document.querySelectorAll('.row-check');
        for(var c of checks) c.checked = this.checked;
    };
    // Search filter
    document.getElementById('searchInput').onkeyup = function() {
        var filter = this.value.toLowerCase();
        var rows = document.querySelectorAll('#produkTable tbody tr');
        rows.forEach(function(row) {
            var text = row.innerText.toLowerCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    };
</script>
</body>
</html>