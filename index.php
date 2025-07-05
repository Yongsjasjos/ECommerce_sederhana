<?php
include 'asset/php/koneksi.php';

// Ambil kategori dari tabel categories
$kategori_result = $koneksi->query("SELECT * FROM categories");

// Ambil data pencarian dan filter
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$kategori_id = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Buat query produk dengan JOIN kategori
$sql = "SELECT products.*, categories.nama_kategori 
        FROM products 
        LEFT JOIN categories ON products.kategori_id = categories.id 
        WHERE 1=1";

if ($cari != '') {
    $cari = $koneksi->real_escape_string($cari);
    $sql .= " AND products.nama_produk LIKE '%$cari%'";
}

if ($kategori_id != '') {
    $kategori_id = (int)$kategori_id;
    $sql .= " AND products.kategori_id = $kategori_id";
}

$sql .= " ORDER BY products.id DESC";

$produk = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Beranda | E-Commerce</title>
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
          <a class="nav-link active" href="index.php">
            <i class="bi bi-house-door-fill"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">
            <i class="bi bi-box-arrow-in-right"></i> Login
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.php">
            <i class="bi bi-person-plus-fill"></i> Daftar
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3 class="mb-3">Produk Terbaru</h3>

  <!-- FORM CARI DAN FILTER -->
  <form method="GET" class="row mb-4 g-2">
    <div class="col-md-5">
      <input type="text" name="cari" class="form-control" placeholder="Cari produk..." value="<?= htmlspecialchars($cari); ?>">
    </div>
    <div class="col-md-4">
      <select name="kategori" class="form-select">
        <option value="">-- Semua Kategori --</option>
        <?php while($kat = $kategori_result->fetch_assoc()): ?>
          <option value="<?= $kat['id']; ?>" <?= ($kat['id'] == $kategori_id) ? 'selected' : ''; ?>>
            <?= $kat['nama_kategori']; ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-funnel-fill me-1"></i> Terapkan
      </button>
    </div>
  </form>

  <!-- DAFTAR PRODUK -->
  <div class="row">
    <?php if ($produk->num_rows > 0): ?>
      <?php while ($row = $produk->fetch_assoc()): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="asset/img/<?= $row['gambar']; ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="<?= $row['nama_produk']; ?>">
            <div class="card-body">
              <h5 class="card-title"><?= $row['nama_produk']; ?></h5>
              <p class="card-text text-success fw-bold">Rp<?= number_format($row['harga'], 0, ',', '.'); ?></p>
              <p class="text-muted small">Stok: <?= $row['stok']; ?></p>
              <button 
                class="btn btn-outline-primary btn-sm" 
                data-bs-toggle="modal" 
                data-bs-target="#modalProduk<?= $row['id']; ?>">
                <i class="bi bi-eye-fill me-1"></i> Lihat Detail
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Detail Produk -->
        <div class="modal fade" id="modalProduk<?= $row['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id']; ?>" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel<?= $row['id']; ?>"><?= $row['nama_produk']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-5">
                    <img src="asset/img/<?= $row['gambar']; ?>" alt="<?= $row['nama_produk']; ?>" class="img-fluid">
                  </div>
                  <div class="col-md-7">
                    <h4><?= $row['nama_produk']; ?></h4>
                    <p class="text-success fw-bold">Rp<?= number_format($row['harga'], 0, ',', '.'); ?></p>
                    <p>Stok: <?= $row['stok']; ?></p>
                    <p><strong>Kategori:</strong> <?= $row['nama_kategori']; ?></p>
                    <p><strong>Deskripsi:</strong><br><?= nl2br($row['deskripsi']); ?></p>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
                <button class="btn btn-primary" onclick="alert('Anda harus login jika ingin belanja');">
                  <i class="bi bi-cart-fill me-1"></i> Beli Sekarang
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->

      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning">Produk tidak ditemukan.</div>
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
