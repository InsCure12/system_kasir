<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) header("Location: login.php");

// Ambil produk
$produk = mysqli_query($con, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk");

//Ambil Metode Pembayaran
$metode = mysqli_query($con, "SELECT * FROM metode_pembayaran");

//ambil diskon
$diskonList = mysqli_query($con, "SELECT nilai FROM diskon GROUP BY nilai ORDER BY nilai DESC");
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    $cart = json_decode($_POST['cart'], true);
    $diskon_persen = intval($_POST['diskon']);
    $total = intval($_POST['total']);
    $id_metode = intval($_POST['metode']);
    $id_karyawan = $_SESSION['id_karyawan'];
    $tanggal = date('Y-m-d H:i:s');
    $nama_pelanggan = mysqli_real_escape_string($con, $_POST['nama_pelanggan']);

    // Cek pelanggan
    $cek_pelanggan = mysqli_query($con, "SELECT id_pelanggan FROM pelanggan WHERE nama_pelanggan='$nama_pelanggan' LIMIT 1");
    if (mysqli_num_rows($cek_pelanggan) > 0) {
        $row = mysqli_fetch_assoc($cek_pelanggan);
        $id_pelanggan = $row['id_pelanggan'];
    } else {
        mysqli_query($con, "INSERT INTO pelanggan (nama_pelanggan) VALUES ('$nama_pelanggan')");
        $id_pelanggan = mysqli_insert_id($con);
    }

    // Cek/insert diskon
    if ($diskon_persen > 0) {
        $cek_diskon = mysqli_query($con, "SELECT id_diskon FROM diskon WHERE nilai=$diskon_persen LIMIT 1");
        if (mysqli_num_rows($cek_diskon) > 0) {
            $row = mysqli_fetch_assoc($cek_diskon);
            $id_diskon = $row['id_diskon'];
        } else {
            mysqli_query($con, "INSERT INTO diskon (nama_diskon, tipe_diskon, nilai, berlaku_mulai, berlaku_sampai) VALUES ('Diskon $diskon_persen%', 'persen', $diskon_persen, CURDATE(), CURDATE())");
            $id_diskon = mysqli_insert_id($con);
        }
    } else {
        $id_diskon = "NULL";
    }

    // Simpan transaksi
    $q1 = mysqli_query($con, "INSERT INTO transaksi (id_pelanggan, id_diskon, id_karyawan, tanggal_transaksi, total, id_metode) VALUES ($id_pelanggan, $id_diskon, $id_karyawan, '$tanggal', $total, $id_metode)");
    if (!$q1) {
        echo "Transaksi: " . mysqli_error($con);
        exit;
    }
    $id_transaksi = mysqli_insert_id($con);

    // Simpan detail transaksi
    foreach ($cart as $item) {
        $id_produk = intval($item['id']);
        $qty = intval($item['qty']);
        $harga = intval($item['harga']);
        $subtotal = $qty * $harga;
        $q2 = mysqli_query($con, "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal) VALUES ($id_transaksi, $id_produk, $qty, $subtotal)");
        if (!$q2) {
            echo "Detail: " . mysqli_error($con);
            exit;
        }
        mysqli_query($con, "UPDATE produk SET stok = stok - $qty WHERE id_produk = $id_produk");
    }

    echo "OK";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaksi POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            background: #f6f7fb; 
        }
        .produk-card { 
            border: 1px solid #e0e0e0; 
            border-radius: 12px; 
            padding: 16px; 
            text-align: center; 
            background: #fff; 
            transition: box-shadow .2s; 
            cursor: pointer; 
        }
        .produk-card:hover { 
            box-shadow: 0 2px 12px rgba(44,62,80,0.09); 
        }
        .produk-img { 
            width: 70px; 
            height: 70px; 
            object-fit: cover; 
            border-radius: 8px; 
            margin-bottom: 8px; 
        }
        .produk-nama { 
            font-weight: 600; 
            font-size: 15px; 
        }
        .produk-harga { 
            color: #27ae60; 
            font-weight: 600; 
            font-size: 16px; 
        }
        .kategori-btn { 
            border: none; 
            background: #e0e0e0; 
            border-radius: 8px; 
            padding: 8px 18px; 
            margin: 0 6px 12px 0; 
            font-weight: 500; 
        }
        .kategori-btn.active { 
            background: #4f3cc9; 
            color: #fff; 
        }
        .cart-panel { 
            background: #fff; 
            border-radius: 16px; 
            box-shadow: 0 2px 12px rgba(44,62,80,0.07); 
            padding: 24px 18px; 
        }
        .cart-table th, .cart-table td { 
            vertical-align: middle !important; 
        }
        .cart-table th { 
            font-weight: 600; 
        }
        .cart-total { 
            font-size: 22px; 
            font-weight: 700; 
            color: #27ae60; 
        }
        .btn-pay { 
            background: #27ae60; 
            color: #fff; 
            font-weight: 600; 
            font-size: 18px; 
            border-radius: 8px; 
        }
        .btn-pay:hover { 
            background: #219150; 
        }
        .btn-cancel, .btn-hold { 
            border-radius: 8px; 
            font-weight: 500; 
        }
        .btn-cancel { 
            background: #e74c3c; 
            color: #fff; 
        }
        .btn-hold { 
            background: #f1c40f; 
            color: #23235b; 
        }
        .cart-action-btn { 
            border: none; 
            background: none; 
            font-size: 18px; 
        }
        .cart-action-btn:focus { 
            outline: none; 
        }
        .produk-search { 
            border-radius: 8px; 
            border: 1px solid #d1d5db; 
            padding: 8px 14px; 
            width: 100%; 
        }
        .produk-grid { 
            max-height: 420px; 
            overflow-y: auto; 
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
            border-radius: 8px;
        }
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
            margin-left: 16px;
        }
        @media (max-width: 767px) {
            .sidebar {
                position: static;
                min-height: auto;
                width: 100%;
                padding: 8px 0;
            }
            .navbar-brand { margin-left: 0; }
            .profile-card { margin-top: 10px; }
        }
    </style>
</head>
<body>
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
                <li class="nav-item"><a class="nav-link active" href="transaksi.php">Transaksi</a></li>
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
            <div class="row pt-4">
                <!-- Produk Grid -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Transaksi POS</h4>
                        <input type="text" class="produk-search" id="searchProduk" placeholder="Search items...">
                    </div>
                    <div class="produk-grid row" id="produkGrid">
                        <?php
                        // ...produk loop Anda...
                        $produk = mysqli_query($con, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk");
                        while($pr = mysqli_fetch_assoc($produk)) { ?>
                        <div class="col-md-4 mb-4">
                            <div class="produk-card"
                                data-id="<?= $pr['id_produk'] ?>"
                                data-nama="<?= htmlspecialchars($pr['nama_produk']) ?>"
                                data-harga="<?= $pr['harga'] ?>"
                                data-stok="<?= $pr['stok'] ?>"
                            >
                                <img src="uploads/<?= htmlspecialchars($pr['gambar']) ?>" class="produk-img" alt="<?= htmlspecialchars($pr['nama_produk']) ?>">
                                <div class="produk-nama"><?= htmlspecialchars($pr['nama_produk']) ?></div>
                                <div class="produk-harga">Rp<?= number_format($pr['harga']) ?></div>
                                <div style="font-size:12px;color:#888;">Stok: <?= $pr['stok'] ?></div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- Cart Panel -->
                <div class="col-lg-4">
                    <div class="cart-panel">
                        <h5>Checkout</h5>
                        <table class="table cart-table mb-2">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th style="width:70px;">QTY</th>
                                    <th>Harga</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <span>Pelanggan</span>
                            <input type="text" id="namaPelanggan" class="form-control" style="width: 60%;" placeholder="Nama Pelanggan">
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Metode Pembayaran</span>
                            <select id="metodePembayaran" name="metode" class="form-control" style="width: 60%;">
                                <?php while($m = mysqli_fetch_assoc($metode)) { ?>
                                    <option value="<?= $m['id_metode'] ?>">
                                        <?= htmlspecialchars($m['nama_metode']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount (%)</span>
                            <select id="diskonInput" class="form-control" style="width: 60%;">
                                <option value="0">0%</option>
                                <?php while($d = mysqli_fetch_assoc($diskonList)) {
                                    if ($d['nilai'] == 0) continue; ?>
                                    <option value="<?= $d['nilai'] ?>"><?= $d['nilai'] ?>%</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sub Total</span>
                            <span id="subTotal">Rp0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax 11%</span>
                            <span id="taxTotal">Rp0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="cart-total">Total</span>
                            <span class="cart-total" id="grandTotal">Rp0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-cancel" onclick="resetCart()">Cancel Order</button>
                            <button class="btn btn-pay" onclick="payOrder()">Pay (<span id="payTotal">Rp0</span>)</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
let cart = [];

function renderCart() {
    let tbody = document.getElementById('cartBody');
    tbody.innerHTML = '';
    let subTotal = 0;
    cart.forEach((item, idx) => {
        let row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.nama}</td>
            <td>
                <button class="cart-action-btn" onclick="updateQty(${idx},-1)">-</button>
                <span>${item.qty}</span>
                <button class="cart-action-btn" onclick="updateQty(${idx},1)">+</button>
            </td>
            <td>Rp${(item.harga * item.qty).toLocaleString()}</td>
            <td><button class="cart-action-btn text-danger" onclick="removeItem(${idx})">&times;</button></td>
        `;
        tbody.appendChild(row);
        subTotal += item.harga * item.qty;
    });
    document.getElementById('subTotal').innerText = 'Rp' + subTotal.toLocaleString();
    let diskon = parseInt(document.getElementById('diskonInput').value) || 0;
    let afterDiskon = subTotal - (subTotal * diskon / 100);
    let tax = Math.round(afterDiskon * 0.11);
    let grand = afterDiskon + tax;
    document.getElementById('taxTotal').innerText = 'Rp' + tax.toLocaleString();
    document.getElementById('grandTotal').innerText = 'Rp' + grand.toLocaleString();
    document.getElementById('payTotal').innerText = 'Rp' + grand.toLocaleString();
}

function addToCart(id, nama, harga, stok) {
    let idx = cart.findIndex(i => i.id == id);
    if (idx > -1) {
        if (cart[idx].qty < stok) cart[idx].qty++;
    } else {
        cart.push({id, nama, harga, qty:1, stok});
    }
    renderCart();
}

function updateQty(idx, delta) {
    if (cart[idx]) {
        cart[idx].qty += delta;
        if (cart[idx].qty < 1) cart[idx].qty = 1;
        if (cart[idx].qty > cart[idx].stok) cart[idx].qty = cart[idx].stok;
        renderCart();
    }
}

function removeItem(idx) {
    cart.splice(idx,1);
    renderCart();
}

function resetCart() {
    cart = [];
    renderCart();
}

function payOrder() {
    if(cart.length == 0) return alert('Keranjang kosong!');
    let diskon = parseInt(document.getElementById('diskonInput').value) || 0;
    let total = document.getElementById('grandTotal').innerText.replace(/[^\d]/g, '');
    let namaPelanggan = document.getElementById('namaPelanggan').value.trim();
    if(namaPelanggan === "") return alert('Nama pelanggan wajib diisi!');
    let metode = document.getElementById('metodePembayaran').value;
    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'cart=' + encodeURIComponent(JSON.stringify(cart)) +
            '&diskon=' + diskon +
            '&total=' + total +
            '&nama_pelanggan=' + encodeURIComponent(namaPelanggan) +
            '&metode=' + encodeURIComponent(metode)
    })
    .then(res => res.text())
    .then(res => {
        if(res.trim() === "OK") {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: 'Transaksi telah disimpan.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location = 'riwayat_transaksi.php';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: res
            });
        }
    });
}

document.querySelectorAll('.produk-card').forEach(card => {
    card.onclick = function() {
        addToCart(
            this.dataset.id,
            this.dataset.nama,
            parseInt(this.dataset.harga),
            parseInt(this.dataset.stok)
        );
    }
});
document.getElementById('diskonInput').onchange = renderCart;
renderCart();

// Search produk
document.getElementById('searchProduk').onkeyup = function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.produk-card').forEach(card => {
        let nama = card.dataset.nama.toLowerCase();
        card.parentElement.style.display = nama.includes(filter) ? '' : 'none';
    });
};
</script>
</body>
</html>