<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../asset/php/koneksi.php';

$user_id = $_SESSION['user_id'];

$kategori_result = $koneksi->query("SELECT id, nama_kategori FROM categories ORDER BY nama_kategori ASC");
if (!$kategori_result) {
    die("Query kategori gagal: " . $koneksi->error);
}
$kategori = [];
while ($row = $kategori_result->fetch_assoc()) {
    $kategori[] = $row;
}

$q = $_GET['q'] ?? '';
if ($q !== '') {
    $stmt = $koneksi->prepare("
        SELECT p.*, c.nama_kategori 
        FROM products p 
        JOIN categories c ON p.kategori_id = c.id 
        WHERE p.user_id = ? AND p.nama_produk LIKE ? 
        ORDER BY p.id DESC
    ");
    $like = "%$q%";
    $stmt->bind_param("is", $user_id, $like);
    $stmt->execute();
    $produk = $stmt->get_result();
} else {
    $stmt = $koneksi->prepare("
        SELECT p.*, c.nama_kategori 
        FROM products p 
        JOIN categories c ON p.kategori_id = c.id 
        WHERE p.user_id = ? 
        ORDER BY p.id DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $produk = $stmt->get_result();
}

$notif = $_GET['notif'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Kelola Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link" href="pesanan.php"><i class="bi bi-receipt"></i> Pesanan</a></li>
        <li class="nav-item"><a class="nav-link active" href="produk.php"><i class="bi bi-box-seam"></i> Produk</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5 pt-4">
  <h3>Daftar Produk Anda</h3>

  <?php if ($notif == 'tambah'): ?>
    <div class="alert alert-success alert-dismissible fade show">Produk berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php elseif ($notif == 'ubah'): ?>
    <div class="alert alert-info alert-dismissible fade show">Produk berhasil diubah!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php elseif ($notif == 'hapus'): ?>
    <div class="alert alert-danger alert-dismissible fade show">Produk berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
    <i class="bi bi-plus-circle"></i> Tambah Produk
  </button>

  <form method="GET" class="mb-3 d-flex" role="search">
    <input class="form-control me-2" type="search" placeholder="Cari produk..." name="q" value="<?= htmlspecialchars($q) ?>" />
    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
  </form>

  <!-- Tampilan Produk dalam Card -->
  <div class="row">
    <?php if ($produk->num_rows > 0): ?>
      <?php while ($row = $produk->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="../asset/img/<?= htmlspecialchars($row['gambar']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama_produk']) ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['nama_produk']) ?></h5>
              <p class="card-text text-muted mb-1"><i class="bi bi-tags"></i> <?= htmlspecialchars($row['nama_kategori']) ?></p>

              <!-- Deskripsi tersembunyi -->
              <button class="btn btn-sm btn-outline-secondary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#deskripsi<?= $row['id'] ?>">
                <i class="bi bi-info-circle"></i> Lihat Deskripsi
              </button>
              <div class="collapse" id="deskripsi<?= $row['id'] ?>">
                <p class="card-text mt-2"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
              </div>

              <p class="card-text fw-bold text-primary mt-2">Rp<?= number_format($row['harga'], 0, ',', '.') ?></p>
              <p class="card-text"><small class="text-muted">Stok: <?= $row['stok'] ?></small></p>
            </div>
            <div class="card-footer d-flex justify-content-between">
              <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
                <i class="bi bi-pencil-square"></i> Edit
              </button>
              <a href="../asset/php/hapus_produk.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                <i class="bi bi-trash"></i> Hapus
              </a>
            </div>
          </div>
        </div>

        <!-- Modal Edit Produk -->
        <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form action="../asset/php/update_produk.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="gambar_lama" value="<?= $row['gambar'] ?>">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Produk</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" name="nama_produk" value="<?= htmlspecialchars($row['nama_produk']) ?>" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" class="form-control" name="harga" value="<?= $row['harga'] ?>" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" class="form-control" name="stok" value="<?= $row['stok'] ?>" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select" required>
                      <option value="">-- Pilih Kategori --</option>
                      <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($row['kategori_id'] == $k['id']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($k['nama_kategori']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Ganti Gambar (opsional)</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*">
                    <img src="../asset/img/<?= htmlspecialchars($row['gambar']) ?>" width="100" class="mt-2">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
                  <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center">
        <p class="text-muted">Tidak ada produk ditemukan.</p>
      </div>
    <?php endif; ?>
  </div>
</div>


<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="../asset/php/simpan_produk.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Produk Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" class="form-control" name="nama_produk" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number" class="form-control" name="harga" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Stok</label>
            <input type="number" class="form-control" name="stok" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select" required>
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Gambar Produk</label>
            <input type="file" class="form-control" name="gambar" accept="image/*" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Produk</button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="bg-light text-center py-3 mt-5">
  <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
