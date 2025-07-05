<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

include 'koneksi.php';

$user_id = $_SESSION['user_id'];
$nama_produk = trim($_POST['nama_produk']);
$harga = (int)$_POST['harga'];
$stok = (int)$_POST['stok'];
$kategori_id = (int)$_POST['kategori_id'];
$deskripsi = trim($_POST['deskripsi']);

// Validasi input dasar
if ($nama_produk === '' || $harga < 0 || $stok < 0 || $kategori_id === 0 || $deskripsi === '') {
    header("Location: ../../admin/produk.php?notif=gagal");
    exit;
}

// Handle upload gambar
$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];
$folder_upload = '../../asset/img/';
$ekstensi = pathinfo($gambar, PATHINFO_EXTENSION);
$nama_baru = uniqid() . '.' . $ekstensi;

if (!is_dir($folder_upload)) {
    mkdir($folder_upload, 0755, true);
}

if (move_uploaded_file($tmp, $folder_upload . $nama_baru)) {
    // Simpan ke database
    $stmt = $koneksi->prepare("INSERT INTO products (nama_produk, harga, stok, kategori_id, gambar, deskripsi, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiissi", $nama_produk, $harga, $stok, $kategori_id, $nama_baru, $deskripsi, $user_id);
    
    if ($stmt->execute()) {
        header("Location: ../../admin/produk.php?notif=tambah");
        exit;
    } else {
        // Hapus gambar jika gagal insert
        unlink($folder_upload . $nama_baru);
        die("Gagal menyimpan produk: " . $stmt->error);
    }
} else {
    die("Gagal upload gambar.");
}