<?php
session_start();
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Kasir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { 
            background: #f6f7fb; 
        }
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
            background: #35357a; 
            border-radius: 8px; 
        }
        .profile-card { 
            background: #35357a; 
            color: #fff; 
            padding: 16px; 
            margin-top: 20px;
        }
        .card-stat { 
            border-radius: 12px; 
        }
        .card-stat .stat-label { 
            font-size: 14px; 
            color: #888; 
        }
        .card-stat .stat-value { 
            font-size: 24px; 
            font-weight: bold; 
        }
        .table td, .table th { 
            vertical-align: middle; 
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar py-4">
            <div class="navbar-brand mb-4">Kayu Kopi</div>
            <ul class="nav flex-column mb-4">
                <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="karyawan.php">Karyawan</a></li>
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Member</a></li>
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
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-3 border-bottom">
                <h2>Sales Graphics</h2>
                <div>Selamat datang, <b><?php echo $_SESSION['nama']; ?></b></div>
            </div>
            <!-- Statistic Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card card-stat p-3">
                        <div class="stat-label">Total Sales</div>
                        <div class="stat-value">
                            <?php
                            $q = mysqli_query($con, "SELECT SUM(total) as total FROM transaksi");
                            $d = mysqli_fetch_assoc($q);
                            echo "Rp " . number_format($d['total'] ? $d['total'] : 0, 0, ',', '.');
                            ?>
                        </div>
                        <div class="<?= $percent_class ?>" style="font-size:13px;"><?= $percent_label ?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card card-stat p-3">
                        <div class="stat-label">Total Orders</div>
                        <div class="stat-value">
                            <?php
                            $q = mysqli_query($con, "SELECT COUNT(*) as total FROM transaksi");
                            $d = mysqli_fetch_assoc($q);
                            echo $d['total'];
                            ?>
                        </div>
                        <div class="<?= $percent_orders_class ?>" style="font-size:13px;"><?= $percent_orders_label ?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card card-stat p-3">
                        <div class="stat-label">Total Member's</div>
                        <div class="stat-value">
                            <?php
                            $q = mysqli_query($con, "SELECT COUNT(*) as total FROM pelanggan");
                            $d = mysqli_fetch_assoc($q);
                            echo $d['total'];
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Charts and Orders List -->
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><b>Sales Report</b></span>
                            <span style="font-size:13px;">
                                Avg per month: <b>Rp<?= number_format($avg_per_month, 0, ',', '.') ?></b>
                            </span>
                        </div>
                        <canvas id="barChart" height="120"></canvas>
                    </div>
                    <div class="card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><b>Orders List</b></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pelanggan</th>
                                        <th>Order ID</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($con, "SELECT t.id_transaksi, p.nama_pelanggan, t.tanggal_transaksi, t.total
                                        FROM transaksi t
                                        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                        ORDER BY t.tanggal_transaksi DESC
                                        LIMIT 7");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        $status = strtolower($row['status']);
                                        $badge = 'status-badge ';
                                        if ($status == 'completed' || $status == 'selesai') $badge .= 'status-completed';
                                        elseif ($status == 'pending') $badge .= 'status-pending';
                                        else $badge .= 'status-canceled';
                                        echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$row['nama_pelanggan']}</td>
                                            <td>#{$row['id_transaksi']}</td>
                                            <td>{$row['tanggal_transaksi']}</td>
                                            <td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>
                                            <td><span class='$badge'>" . ucfirst($row['status']) . "</span></td>
                                        </tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Right Side Widgets -->
                <div class="col-md-4 mb-4">
                    <div class="card p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><b>Orders List</b></span>
                        </div>
                        <canvas id="ordersListChart" height="120"></canvas>
                    </div>
                    <div class="card p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><b>Monthly Sales</b></span>
                            <span style="font-size:13px;">This month</span>
                        </div>
                        <canvas id="monthlySalesChart" height="80"></canvas>
                    </div>
                    <div class="card p-3">
                        <div><b>Total Orders</b></div>
                        <div style="font-size:24px; font-weight:bold;">
                            <?php
                            $q = mysqli_query($con, "SELECT COUNT(*) as total FROM transaksi");
                            $d = mysqli_fetch_assoc($q);
                            echo $d['total'];
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php
// Ambil total penjualan per hari di bulan ini
$dailySales = [];
$days = [];
$bulan = date('m');
$tahun = date('Y');
for ($i = 1; $i <= date('t'); $i++) {
    $tgl = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i);
    $q = mysqli_query($con, "SELECT SUM(total) as total FROM transaksi WHERE tanggal_transaksi='$tgl'");
    $d = mysqli_fetch_assoc($q);
    $dailySales[] = (int)($d['total'] ?? 0);
    $days[] = $i;
}
?>
<?php
// Ambil total penjualan per bulan (12 bulan terakhir)
$salesData = [];
$monthLabels = [];
for ($i = 11; $i >= 0; $i--) {
    $bulan = date('m', strtotime("-$i months"));
    $tahun = date('Y', strtotime("-$i months"));
    $label = date('M', strtotime("-$i months"));
    $q = mysqli_query($con, "SELECT SUM(total) as total FROM transaksi WHERE MONTH(tanggal_transaksi)='$bulan' AND YEAR(tanggal_transaksi)='$tahun'");
    $d = mysqli_fetch_assoc($q);
    $salesData[] = (int)($d['total'] ?? 0);
    $monthLabels[] = $label;
}
?>
<?php
// Hitung rata-rata total transaksi per bulan (12 bulan terakhir)
$q = mysqli_query($con, "
    SELECT YEAR(tanggal_transaksi) as tahun, MONTH(tanggal_transaksi) as bulan, SUM(total) as total_bulan
    FROM transaksi
    GROUP BY tahun, bulan
    ORDER BY tahun DESC, bulan DESC
    LIMIT 12
");
$total = 0;
$bulan = 0;
while ($row = mysqli_fetch_assoc($q)) {
    $total += $row['total_bulan'];
    $bulan++;
}
$avg_per_month = $bulan > 0 ? $total / $bulan : 0;
?>
<?php
// Total penjualan hari ini
$q = mysqli_query($con, "SELECT SUM(total) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()");
$d = mysqli_fetch_assoc($q);
$total_today = $d['total'] ?? 0;

// Total penjualan kemarin
$q = mysqli_query($con, "SELECT SUM(total) as total FROM transaksi WHERE DATE(tanggal_transaksi) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
$d = mysqli_fetch_assoc($q);
$total_yesterday = $d['total'] ?? 0;

// Hitung persentase perubahan
if ($total_yesterday > 0) {
    $percent = (($total_today - $total_yesterday) / $total_yesterday) * 100;
} else {
    $percent = $total_today > 0 ? 100 : 0;
}
$percent_label = ($percent >= 0 ? '+' : '') . number_format($percent, 1) . '% since yesterday';
$percent_class = $percent >= 0 ? 'text-success' : 'text-danger';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($monthLabels); ?>,
            datasets: [{
                label: 'Sales',
                data: <?php echo json_encode($salesData); ?>,
                backgroundColor: '#4f3cc9'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    // Orders List Chart (Stacked Bar)
    var ctx2 = document.getElementById('ordersListChart').getContext('2d');
    var ordersListChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['January', 'February'],
            datasets: [
                { label: 'Completed', data: [12, 15], backgroundColor: '#4f3cc9' },
                { label: 'Pending', data: [8, 5], backgroundColor: '#ffb74d' },
                { label: 'Canceled', data: [3, 2], backgroundColor: '#e53935' }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { x: { stacked: true }, y: { stacked: true } }
        }
    });

    // Monthly Sales Line Chart
    var ctx3 = document.getElementById('monthlySalesChart').getContext('2d');
    var monthlySalesChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: ['1','2','3','4','5','6','7','8','9','10'],
            datasets: [{
                label: 'Sales',
                data: [5000, 7000, 8000, 6000, 9000, 11000, 12000, 10000, 9500, 10500],
                backgroundColor: 'rgba(79, 60, 201, 0.1)',
                borderColor: '#4f3cc9',
                borderWidth: 2,
                pointRadius: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
</script>
</body>
</html>