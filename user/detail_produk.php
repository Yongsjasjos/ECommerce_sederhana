<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include '../asset/php/koneksi.php';

if (!isset($_GET['id'])) {
    echo "Produk tidak ditemukan.";
    exit;
}

$id = $_GET['id'];

// Ambil data produk dari DB
$stmt = $koneksi->prepare("
    SELECT products.*, categories.nama_kategori 
    FROM products 
    LEFT JOIN categories ON products.kategori_id = categories.id
    WHERE products.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Produk tidak ditemukan.";
    exit;
}

$produk = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Detail Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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
    <div class="row">
        <div class="col-md-5">
            <?php if (!empty($produk['gambar'])) : ?>
                <img src="../asset/img/<?= htmlspecialchars($produk['gambar']); ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($produk['nama_produk']); ?>">
            <?php else : ?>
                <img src="https://via.placeholder.com/400x300?text=No+Image" class="img-fluid rounded shadow" alt="No Image">
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <h3><?= htmlspecialchars($produk['nama_produk']); ?></h3>
            <p>Kategori: <strong><?= htmlspecialchars($produk['nama_kategori']); ?></strong></p>
            <p><?= nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
            <h4 class="text-success">Rp<?= number_format($produk['harga'], 0, ',', '.'); ?></h4>
            <p>Stok: <?= $produk['stok']; ?></p>

            <div class="d-flex gap-2">
                <button 
                    type="button" 
                    class="btn btn-secondary flex-fill"
                    id="btnKeranjang"
                    <?= $produk['stok'] == 0 ? 'disabled' : '' ?>
                    >
                    <i class="bi bi-cart-plus"></i> + Simpan ke Keranjang
                </button>

                <?php if ($produk['stok'] > 0): ?>
                <button 
                    type="button" 
                    class="btn btn-success flex-fill" 
                    id="btnBeli">
                    <i class="bi bi-bag-check"></i> Beli Sekarang
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalJumlah" tabindex="-1" aria-labelledby="modalJumlahLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="form-aksi-produk-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="modalJumlahLabel"><i class="bi bi-cart"></i> Konfirmasi Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-flex gap-4">
          <div style="width: 40%;">
            <?php if (!empty($produk['gambar'])) : ?>
                <img src="../asset/img/<?= htmlspecialchars($produk['gambar']); ?>" alt="<?= htmlspecialchars($produk['nama_produk']); ?>" class="img-fluid rounded shadow">
            <?php else : ?>
                <img src="https://via.placeholder.com/300x300?text=No+Image" alt="No Image" class="img-fluid rounded shadow">
            <?php endif; ?>
          </div>
          <div style="width: 60%;">
            <h4><?= htmlspecialchars($produk['nama_produk']); ?></h4>
            <p><strong>Harga satuan:</strong> Rp<?= number_format($produk['harga'], 0, ',', '.'); ?></p>
            <p><strong>Stok tersedia:</strong> <?= $produk['stok']; ?></p>
            <div class="mb-3">
              <label for="modalJumlahInput" class="form-label">Jumlah:</label>
              <input 
                type="number" 
                id="modalJumlahInput" 
                name="jumlah" 
                class="form-control" 
                min="1" 
                max="<?= $produk['stok']; ?>" 
                value="1" 
                required
              >
            </div>
            <p><strong>Total harga:</strong> <span id="totalHarga">Rp<?= number_format($produk['harga'], 0, ',', '.'); ?></span></p>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="produk_id" value="<?= $produk['id']; ?>">
          <input type="hidden" id="modalAksi" name="aksi" value="">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Batal
          </button>
          <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
            <i class="bi bi-cart-plus"></i> Simpan ke Keranjang
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
    <div id="toastBerhasil" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Produk berhasil ditambahkan ke keranjang!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<footer class="bg-light text-center py-3 mt-5">
  <small>© 2025 TOKO YOGA - Semua hak dilindungi</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const hargaSatuan = <?= (int)$produk['harga']; ?>;
  const stok = <?= (int)$produk['stok']; ?>;

  const modalJumlah = new bootstrap.Modal(document.getElementById('modalJumlah'));
  const modalJumlahInput = document.getElementById('modalJumlahInput');
  const totalHargaEl = document.getElementById('totalHarga');
  const modalAksiInput = document.getElementById('modalAksi');
  const modalSubmitBtn = document.getElementById('modalSubmitBtn');
  const formModal = document.getElementById('form-aksi-produk-modal');

  // Fungsi update total harga saat input jumlah berubah
  function updateTotalHarga() {
    let jumlah = parseInt(modalJumlahInput.value) || 1;
    if(jumlah > stok) jumlah = stok;
    if(jumlah < 1) jumlah = 1;
    modalJumlahInput.value = jumlah;

    const total = jumlah * hargaSatuan;
    totalHargaEl.textContent = `Rp${total.toLocaleString('id-ID')}`;
  }

  modalJumlahInput.addEventListener('input', updateTotalHarga);

  // Buka modal dengan set aksi & button sesuai tombol yang diklik
  document.getElementById('btnKeranjang').addEventListener('click', () => {
    modalAksiInput.value = 'keranjang';
    modalSubmitBtn.textContent = ' Simpan ke Keranjang';
    modalSubmitBtn.className = 'btn btn-secondary';
    modalSubmitBtn.insertAdjacentHTML('afterbegin', '<i class="bi bi-cart-plus"></i>');

    updateTotalHarga();
    modalJumlah.show();
  });

  document.getElementById('btnBeli')?.addEventListener('click', () => {
    modalAksiInput.value = 'beli';
    modalSubmitBtn.textContent = ' Beli Sekarang';
    modalSubmitBtn.className = 'btn btn-success';
    modalSubmitBtn.insertAdjacentHTML('afterbegin', '<i class="bi bi-bag-check"></i>');

    updateTotalHarga();
    modalJumlah.show();
  });

  formModal.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(formModal);

    fetch('../asset/php/tambah_keranjang.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      if(data === "redirect_checkout" && formData.get('aksi') === 'beli') {
        window.location.href = 'checkout.php';
      } else if(data === "added_to_cart" && formData.get('aksi') === 'keranjang') {
        const toastElement = document.getElementById('toastBerhasil');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        modalJumlah.hide();
      } else {
        alert("Terjadi kesalahan: " + data);
      }
    })
    .catch(err => alert("Gagal: " + err.message));
  });
</script>
</body>
</html>
