<?php
session_start();

// Pastikan user login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

// Cek apakah id produk dikirim
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus produk dari session cart
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    header("Location: ../../user/keranjang.php");
    exit;
} else {
    header("Location: ../../user/keranjang.php");
    exit;
}