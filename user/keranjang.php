<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include '../asset/php/koneksi.php';

// Ambil isi keranjang dari session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$produkData = [];
$totalHarga = 0;

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $koneksi->query($sql);

    while ($row = $result->fetch_assoc()) {
        $row['jumlah'] = $cart[$row['id']];
        $row['subtotal'] = $row['harga'] * $row['jumlah'];
        $totalHarga += $row['subtotal'];
        $produkData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
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
          <a class="nav-link active" href="keranjang.php"><i class="bi bi-cart-fill"></i> Keranjang</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="riwayat_pesanan.php"><i class="bi bi-clock-history"></i> Riwayat Pesanan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h3 class="mb-4">Keranjang Belanja</h3>

    <?php if (empty($produkData)) : ?>
        <div class="alert alert-warning">Keranjang kamu masih kosong.</div>
        <a href="katalog.php" class="btn btn-primary"><i class="bi bi-arrow-left-circle"></i> Kembali ke Katalog</a>
    <?php else : ?>
        <form action="checkout.php" method="POST">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <strong>Jumlah Item:</strong> <?= count($produkData); ?> |
                    <strong>Total Bayar:</strong> Rp<?= number_format($totalHarga, 0, ',', '.'); ?>
                </div>
                <div class="card-body p-2">
                    <ul class="list-group">
                        <?php foreach ($produkData as $produk): ?>
                            <li class="list-group-item d-flex align-items-center">
                                <img src="../asset/img/<?= htmlspecialchars($produk['gambar']); ?>" alt="Produk" class="produk-img">
                                <div class="flex-grow-1">
                                    <strong><?= htmlspecialchars($produk['nama_produk']); ?></strong><br>
                                    <small><?= $produk['jumlah']; ?> x Rp<?= number_format($produk['harga'], 0, ',', '.'); ?></small><br>
                                    <span class="text-muted">Subtotal: Rp<?= number_format($produk['subtotal'], 0, ',', '.'); ?></span>
                                </div>
                                <a href="../asset/php/hapus_keranjang.php?id=<?= $produk['id']; ?>" class="btn btn-sm btn-danger ms-2">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-bag-check-fill me-1"></i> Lanjut ke Checkout
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<footer class="bg-light text-center py-3 mt-5">
  <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
