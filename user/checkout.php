<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include '../asset/php/koneksi.php';

// Cek apakah keranjang kosong
if (empty($_SESSION['cart'])) {
    // Jika request AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Keranjang Anda kosong.']);
        exit;
    }

    // Jika bukan AJAX, redirect biasa
    header("Location: keranjang.php");
    exit;
}

// Ambil isi keranjang
$cart = $_SESSION['cart'];
$total = 0;
$produkData = [];

$ids = implode(',', array_keys($cart));
$sql = "SELECT * FROM products WHERE id IN ($ids)";
$result = $koneksi->query($sql);

// Proses item dalam keranjang
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $jumlah = $cart[$id];

    // Cek stok
    if ($jumlah > $row['stok']) {
        $msg = "Stok tidak cukup untuk produk: " . $row['nama_produk'];

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $msg]);
            exit;
        }

        // Bukan AJAX
        exit($msg);
    }

    $subtotal = $row['harga'] * $jumlah;
    $total += $subtotal;

    $produkData[] = [
        'id' => $id,
        'harga' => $row['harga'],
        'jumlah' => $jumlah
    ];
}

// Simpan ke tabel orders
$user_id = $_SESSION['user_id'];
$stmt = $koneksi->prepare("INSERT INTO orders (user_id, total_harga, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;

// Simpan detail order (order_items) dan kurangi stok
foreach ($produkData as $item) {
    $stmt = $koneksi->prepare("INSERT INTO order_items (order_id, product_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $item['id'], $item['jumlah'], $item['harga']);
    $stmt->execute();

    $koneksi->query("UPDATE products SET stok = stok - {$item['jumlah']} WHERE id = {$item['id']}");
}

// Kosongkan keranjang
unset($_SESSION['cart']);

// ✔️ Respons AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode([
        'status' => 'success',
        'message' => 'Pesanan Anda berhasil.',
        'redirect' => 'riwayat_pesanan.php'
    ]);
    exit;
}

// ✔️ Request normal → redirect ke sukses.php
header("Location: sukses.php");
exit;