<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_item_id = intval($_POST['order_item_id']);
    $status = $_POST['status'];

    if (!in_array($status, ['pending', 'diproses', 'selesai'])) {
        die("Status tidak valid.");
    }

    $admin_id = intval($_SESSION['user_id']);

    // Pastikan item ini milik produk dari admin yang login
    $check = $koneksi->prepare("
        SELECT oi.id
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.id = ? AND p.user_id = ?
    ");
    $check->bind_param("ii", $order_item_id, $admin_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        die("Item tidak ditemukan atau Anda tidak berhak mengubahnya.");
    }

    $update = $koneksi->prepare("UPDATE order_items SET status_admin = ? WHERE id = ?");
    $update->bind_param("si", $status, $order_item_id);
    if ($update->execute()) {
        header("Location: ../../admin/pesanan.php");
    } else {
        echo "Gagal memperbarui status.";
    }
}
