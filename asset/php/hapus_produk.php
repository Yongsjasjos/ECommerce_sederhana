<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

include 'koneksi.php';

$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil nama file gambar & cek apakah produk milik admin yang login (opsional jika digunakan multi-admin)
$stmt = $koneksi->prepare("SELECT gambar, user_id FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../../admin/produk.php?notif=notfound");
    exit;
}

$data = $result->fetch_assoc();

if ($data['user_id'] != $user_id) {
    header("Location: ../../admin/produk.php?notif=unauthorized");
    exit;
}

// Hapus gambar dari server
$gambar = $data['gambar'];
$gambar_path = '../../asset/img/' . $gambar;
if (file_exists($gambar_path)) {
    unlink($gambar_path);
}

// Hapus data produk dari database
$stmt_delete = $koneksi->prepare("DELETE FROM products WHERE id = ?");
$stmt_delete->bind_param("i", $id);
if ($stmt_delete->execute()) {
    header("Location: ../../admin/produk.php?notif=hapus");
    exit;
} else {
    die("Gagal menghapus produk: " . $stmt_delete->error);
}
