<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
          <a class="nav-link" href="dashboard.php">
            <i class="bi bi-house-door"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="pesanan.php">
            <i class="bi bi-receipt"></i> Pesanan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="produk.php">
            <i class="bi bi-box-seam"></i> Produk
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <h3>Cetak Laporan Pesanan Berdasarkan Tanggal</h3>
    <form action="../asset/php/laporan_pdf.php" method="POST" class="row g-3">
        <div class="col-md-5">
            <label for="tanggal_awal" class="form-label">Dari Tanggal</label>
            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" required>
        </div>
        <div class="col-md-5">
            <label for="tanggal_akhir" class="form-label">Sampai Tanggal</label>
            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-danger w-100">
              <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
            </button>
        </div>
    </form>
</div>
<footer class="bg-light text-center py-3 mt-5">
  <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
