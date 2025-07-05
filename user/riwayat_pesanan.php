<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include '../asset/php/koneksi.php';

$user_id = $_SESSION['user_id'];

// Ambil filter dari form
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Query dasar dengan join untuk cari nama produk
$sql = "SELECT DISTINCT o.* FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ? ";
$params = [$user_id];
$types = "i";

// Filter status
if ($status_filter !== 'all' && in_array($status_filter, ['pending', 'diproses', 'selesai'])) {
    $sql .= " AND o.status = ? ";
    $params[] = $status_filter;
    $types .= "s";
}

// Filter search
if ($search !== '') {
    $sql .= " AND (o.id LIKE CONCAT('%', ?, '%') OR p.nama_produk LIKE CONCAT('%', ?, '%')) ";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

$sql .= " ORDER BY o.id DESC";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .navbar-fixed {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }
        body {
            padding-top: 70px;
        }
        .produk-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
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
        <li class="nav-item">
          <a class="nav-link" href="katalog.php"><i class="bi bi-house-door-fill"></i> Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="keranjang.php"><i class="bi bi-cart-fill"></i> Keranjang</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="riwayat_pesanan.php"><i class="bi bi-clock-history"></i> Riwayat Pesanan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h3>Riwayat Pesanan Anda</h3>
    
    <!-- Form filter & search -->
    <form method="get" class="row g-3 mb-4 align-items-end">
        <div class="col-md-4">
            <label for="search" class="form-label">Cari ID Pesanan atau Nama Produk</label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($search); ?>" class="form-control" placeholder="Cari...">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Filter Status</label>
            <select name="status" id="status" class="form-select">
              <option value="all" <?= $status_filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
              <option value="pending" <?= $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="diproses" <?= $status_filter === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
              <option value="selesai" <?= $status_filter === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
        </div>
    </form>

    <?php if ($orders->num_rows === 0): ?>
        <div class="alert alert-warning mt-3">Tidak ada pesanan ditemukan.</div>
    <?php else: ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <?php
            // Ambil status_admin terakhir dari order_items
            $sql_status = "SELECT status_admin FROM order_items WHERE order_id = ? ORDER BY id DESC LIMIT 1";
            $stmt_status = $koneksi->prepare($sql_status);
            $stmt_status->bind_param("i", $order['id']);
            $stmt_status->execute();
            $result_status = $stmt_status->get_result();
            $status_admin = $result_status->fetch_assoc()['status_admin'] ?? $order['status'];
            ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light">
                    <strong>ID Pesanan:</strong> <?= $order['id']; ?> |
                    <strong>Status:</strong> <?= ucfirst($status_admin); ?> |
                    <strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($order['tanggal'])); ?> |
                    <strong>Total Bayar:</strong> Rp<?= number_format($order['total_harga'], 0, ',', '.'); ?>
                </div>
                <div class="card-body p-2">
                    <ul class="list-group">
                        <?php
                        $order_id = $order['id'];
                        $sql_items = "SELECT oi.jumlah, oi.harga_satuan, oi.status_admin, p.nama_produk, p.gambar 
                                      FROM order_items oi
                                      JOIN products p ON oi.product_id = p.id
                                      WHERE oi.order_id = ?";
                        $stmt_items = $koneksi->prepare($sql_items);
                        $stmt_items->bind_param("i", $order_id);
                        $stmt_items->execute();
                        $items = $stmt_items->get_result();
                        while ($item = $items->fetch_assoc()):
                        ?>
                            <li class="list-group-item d-flex align-items-center">
                                <img src="../asset/img/<?= htmlspecialchars($item['gambar']); ?>" alt="Produk" class="produk-img">
                                <div class="flex-grow-1">
                                    <?= htmlspecialchars($item['nama_produk']); ?><br>
                                    <small>
                                        <?= $item['jumlah']; ?> x Rp<?= number_format($item['harga_satuan'], 0, ',', '.'); ?><br>
                                        Status Item: <span class="badge bg-secondary"><?= ucfirst($item['status_admin']); ?></span>
                                    </small>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php if (strtolower($status_admin) === 'selesai'): ?>
                        <a href="../asset/php/cetak_nota.php?order_id=<?= $order['id']; ?>" class="btn btn-sm btn-primary mt-2">
                          <i class="bi bi-receipt me-1"></i> Cetak Nota
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<footer class="bg-light text-center py-3 mt-5">
  <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
