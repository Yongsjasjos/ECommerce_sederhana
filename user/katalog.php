<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}
include '../asset/php/koneksi.php';

// Ambil daftar kategori untuk dropdown filter
$kategoriList = $koneksi->query("SELECT id, nama_kategori FROM categories ORDER BY nama_kategori");

// Tangkap input pencarian & filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterKategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

// Build query utama
$sql = "SELECT products.*, categories.nama_kategori 
        FROM products 
        LEFT JOIN categories ON products.kategori_id = categories.id
        WHERE 1=1";

$params = [];
$types = "";

if ($search !== '') {
    $sql .= " AND products.nama_produk LIKE ?";
    $types .= "s";
    $params[] = "%{$search}%";
}
if ($filterKategori > 0) {
    $sql .= " AND products.kategori_id = ?";
    $types .= "i";
    $params[] = $filterKategori;
}

$sql .= " ORDER BY products.id DESC";

$stmt = $koneksi->prepare($sql);

if ($types !== "") {
    // Bind parameters dynamically
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
      .navbar-fixed {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1030; /* supaya di atas elemen lain */
      }

      body {
        padding-top: 70px; /* beri ruang agar konten tidak ketutupan navbar */
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
          <a class="nav-link active" href="katalog.php"><i class="bi bi-house-door-fill"></i> Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="keranjang.php"><i class="bi bi-cart-fill"></i> Keranjang</a>
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
  <h3 class="mb-3">Katalog Produk</h3>

  <form class="row g-2 mb-4" method="get" action="">
    <div class="col-md-6">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" name="search" placeholder="Cari produk…" value="<?= htmlspecialchars($search); ?>">
      </div>
    </div>
    <div class="col-md-4">
      <select class="form-select" name="kategori">
        <option value="0">Semua Kategori</option>
        <?php while ($kat = $kategoriList->fetch_assoc()): ?>
          <option value="<?= $kat['id']; ?>" <?= $filterKategori == $kat['id'] ? 'selected' : ''; ?>>
            <?= htmlspecialchars($kat['nama_kategori']); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill"></i> Filter</button>
    </div>
  </form>

  <div class="row">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-primary shadow-sm">
            <?php if (!empty($row['gambar'])): ?>
              <img src="../asset/img/<?= $row['gambar']; ?>" class="card-img-top" height="200" style="object-fit: cover;">
            <?php else: ?>
              <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" height="200">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($row['nama_produk']); ?></h5>
              <p class="card-text text-success fw-bold">
                Rp<?= number_format($row['harga'], 0, ',', '.'); ?>
              </p>
              <p class="text-muted small flex-grow-1">
                Kategori: <?= htmlspecialchars($row['nama_kategori']); ?>
              </p>
              <a href="detail_produk.php?id=<?= $row['id']; ?>" class="btn btn-primary mt-auto">
                <i class="bi bi-eye-fill"></i> Lihat Detail
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">
          <i class="bi bi-exclamation-triangle-fill"></i> Tidak ada produk yang sesuai.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<footer class="bg-light text-center py-3 mt-5">
  <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
