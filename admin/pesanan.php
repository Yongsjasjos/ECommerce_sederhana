<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../asset/php/koneksi.php';
$user_id = intval($_SESSION['user_id']);

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$where = "WHERE p.user_id = $user_id";
if ($search !== '') {
    $search = $koneksi->real_escape_string($search);
    $where .= " AND (o.id LIKE '%$search%' OR u.nama LIKE '%$search%')";
}
if ($status_filter !== '') {
    $where .= " AND oi.status_admin = '$status_filter'";
}

$sql = "SELECT DISTINCT o.*, u.nama AS nama_user
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN users u ON o.user_id = u.id
        $where
        ORDER BY o.id DESC";
$pesanan = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-fixed { position: fixed; top: 0; width: 100%; z-index: 1030; }
        body { padding-top: 70px; }
        .produk-img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 10px; }
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
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="pesanan.php"><i class="bi bi-receipt"></i> Pesanan</a></li>
        <li class="nav-item"><a class="nav-link" href="produk.php"><i class="bi bi-box-seam"></i> Produk</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <h3>Daftar Pesanan Produk Saya</h3>
    <form method="GET" action="">
        <div class="row align-items-end mb-4 flex-nowrap" style="display: flex; flex-wrap: nowrap; gap: 10px;">

            <div class="col-auto" style="flex: 1 1 auto; min-width: 150px;">
                <input type="text" name="search" class="form-control" placeholder="Cari ID / Nama..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-auto" style="flex: 0 0 150px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="diproses" <?= $status_filter == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="selesai" <?= $status_filter == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>

            <div class="col-auto" style="flex: 0 0 100px;">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
            </div>

            <div class="col-auto" style="flex: 0 0 auto;">
                <a href="laporan.php?search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" 
                class="btn btn-success">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </a>
            </div>
        </div>
    </form>

    <?php if ($pesanan->num_rows > 0): ?>
        <?php while ($order = $pesanan->fetch_assoc()): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light">
                    <strong>ID Pesanan:</strong> <?= $order['id']; ?> |
                    <strong>Pemesan:</strong> <?= htmlspecialchars($order['nama_user']); ?> |
                    <strong>Status Global:</strong> <?= ucfirst($order['status']); ?>
                </div>
                <div class="card-body p-2">
                    <ul class="list-group mb-2">
                        <?php
                        $sql_items = "SELECT oi.*, p.nama_produk, p.gambar
                                      FROM order_items oi
                                      JOIN products p ON oi.product_id = p.id
                                      WHERE oi.order_id = {$order['id']} AND p.user_id = $user_id";
                        $items = $koneksi->query($sql_items);

                        if ($items->num_rows > 0):
                            while ($item = $items->fetch_assoc()):
                        ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="../asset/img/<?= htmlspecialchars($item['gambar']); ?>" class="produk-img" alt="Gambar">
                                        <?= htmlspecialchars($item['nama_produk']); ?> (<?= $item['jumlah']; ?>x) - Rp<?= number_format($item['harga_satuan'], 0, ',', '.'); ?>
                                    </div>
                                    <div>
                                        <?php if ($item['status_admin'] !== 'selesai'): ?>
                                            <form action="../asset/php/update_status.php" method="POST" class="d-flex">
                                                <input type="hidden" name="order_item_id" value="<?= $item['id']; ?>">
                                                <select name="status" class="form-select form-select-sm me-2">
                                                    <option value="pending" <?= $item['status_admin'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="diproses" <?= $item['status_admin'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                                    <option value="selesai" <?= $item['status_admin'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endwhile; else: ?>
                            <li class="list-group-item text-muted">Tidak ada produk Anda di pesanan ini.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">Belum ada pesanan yang berisi produk Anda.</div>
    <?php endif; ?>
</div>

<footer class="bg-light text-center py-3 mt-5">
    <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
