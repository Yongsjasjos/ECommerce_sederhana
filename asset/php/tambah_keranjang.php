<?php
session_start();

// Pastikan user login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk_id = intval($_POST['produk_id']);
    $jumlah    = intval($_POST['jumlah']);
    $aksi      = $_POST['aksi'];

    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Tambahkan produk ke keranjang
    if (isset($_SESSION['cart'][$produk_id])) {
        $_SESSION['cart'][$produk_id] += $jumlah;
    } else {
        $_SESSION['cart'][$produk_id] = $jumlah;
    }

    // Jika aksi adalah "beli", redirect AJAX ke checkout
    if ($aksi === 'beli') {
        echo "redirect_checkout";
        exit;
    }

    // Jika aksi hanya simpan ke keranjang
    echo "added_to_cart";
    exit;
}

http_response_code(400);
echo "Bad request";
exit;
