<?php
session_start();

// Cek apakah sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = intval($_SESSION['user_id']);

// Koneksi database
$conn = new mysqli("localhost", "root", "", "db_ecommerce");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Jakarta');

// Ambil data pesanan milik admin dalam 7 hari terakhir (jumlah pesanan per hari)
$sqlOrders = "
    SELECT DATE(o.tanggal) AS tanggal, COUNT(DISTINCT o.id) AS jumlah_pesanan
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE p.user_id = $admin_id
      AND o.tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(o.tanggal)
    ORDER BY tanggal ASC
";
$resultOrders = $conn->query($sqlOrders);

// Siapkan array tanggal 7 hari terakhir dengan default 0
$labelsOrders = [];
$dataOrders = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labelsOrders[$date] = date('d M', strtotime($date));
    $dataOrders[$date] = 0;
}

// Isi data pesanan sesuai query
while ($row = $resultOrders->fetch_assoc()) {
    $tgl = $row['tanggal'];
    if (isset($dataOrders[$tgl])) {
        $dataOrders[$tgl] = (int)$row['jumlah_pesanan'];
    }
}

// Produk paling laku (Top 5)
$sqlTopProducts = "
    SELECT p.nama_produk, SUM(oi.jumlah) AS total_qty
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE p.user_id = $admin_id
    GROUP BY p.id
    ORDER BY total_qty DESC
    LIMIT 5
";
$resultTop = $conn->query($sqlTopProducts);

$labelsProducts = [];
$dataProducts = [];
while ($row = $resultTop->fetch_assoc()) {
    $labelsProducts[] = $row['nama_produk'];
    $dataProducts[] = (int)$row['total_qty'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin - TOKO YOGA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body { padding-top: 70px; }
        .navbar-fixed {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-fixed">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">TOKO YOGA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
              <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door"></i> Home</a></li>
              <li class="nav-item"><a class="nav-link" href="pesanan.php"><i class="bi bi-receipt"></i> Pesanan</a></li>
              <li class="nav-item"><a class="nav-link" href="produk.php"><i class="bi bi-box-seam"></i> Produk</a></li>
              <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
          </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="card bg-success bg-opacity-50 text-white shadow-sm border-0 mb-4">
        <div class="card-body d-flex align-items-center gap-4">
            <i class="bi bi-person-circle display-3"></i>
            <div>
                <h5>Selamat Datang</h5>
                <h2><?= htmlspecialchars($_SESSION['nama']); ?></h2>
            </div>
        </div>
    </div>

    <p>Ini adalah halaman dashboard admin. Anda bisa mengelola produk, kategori, dan pesanan di sini.</p>

    <div class="row g-3 mt-4">
        <div class="col-md-6">
            <div class="p-3 border rounded shadow-sm bg-white" style="height: 350px;">
                <h5 class="text-center mb-3">Grafik Pesanan (7 Hari)</h5>
                <canvas id="chartOrders" height="250"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 border rounded shadow-sm bg-white" style="height: 350px;">
                <h5 class="text-center mb-3">Produk Paling Laku (Top 5)</h5>
                <canvas id="chartProducts" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<footer class="bg-light text-center py-3 mt-5">
    <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Grafik Pesanan 7 hari (Line Chart)
    const ctxOrders = document.getElementById('chartOrders').getContext('2d');
    new Chart(ctxOrders, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_values($labelsOrders)); ?>,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: <?= json_encode(array_values($dataOrders)); ?>,
                fill: true,
                borderColor: 'rgba(75,192,192,1)',
                backgroundColor: 'rgba(75,192,192,0.2)',
                tension: 0.3,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah' },
                    ticks: { stepSize: 1 }
                },
                x: { title: { display: true, text: 'Tanggal' } }
            }
        }
    });

    // Grafik Produk Paling Laku (Pie Chart)
    const ctxProducts = document.getElementById('chartProducts').getContext('2d');
    new Chart(ctxProducts, {
        type: 'pie',
        data: {
            labels: <?= json_encode($labelsProducts); ?>,
            datasets: [{
                data: <?= json_encode($dataProducts); ?>,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

</body>
</html>
