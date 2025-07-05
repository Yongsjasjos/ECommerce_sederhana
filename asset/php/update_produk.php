<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

include 'koneksi.php';

$id = (int)$_POST['id'];
$user_id = $_SESSION['user_id'];
$nama_produk = trim($_POST['nama_produk']);
$harga = (int)$_POST['harga'];
$stok = (int)$_POST['stok'];
$kategori_id = (int)$_POST['kategori_id'];
$deskripsi = trim($_POST['deskripsi']);
$gambar_lama = $_POST['gambar_lama'];

// Validasi dasar
if ($nama_produk === '' || $harga < 0 || $stok < 0 || $kategori_id === 0 || $deskripsi === '') {
    header("Location: ../../admin/produk.php?notif=gagal");
    exit;
}

// Cek apakah upload gambar baru
$gambar_baru = $_FILES['gambar']['name'];
$upload_folder = '../../asset/img/';
$gambar_final = $gambar_lama; // default pakai gambar lama

if (!empty($gambar_baru)) {
    $tmp = $_FILES['gambar']['tmp_name'];
    $ext = pathinfo($gambar_baru, PATHINFO_EXTENSION);
    $nama_baru = uniqid() . '.' . $ext;

    if (!is_dir($upload_folder)) {
        mkdir($upload_folder, 0755, true);
    }

    if (move_uploaded_file($tmp, $upload_folder . $nama_baru)) {
        // Hapus gambar lama
        if (file_exists($upload_folder . $gambar_lama)) {
            unlink($upload_folder . $gambar_lama);
        }
        $gambar_final = $nama_baru;
    } else {
        die("Upload gambar baru gagal.");
    }
}

// Update database
$stmt = $koneksi->prepare("UPDATE products SET nama_produk = ?, harga = ?, stok = ?, kategori_id = ?, gambar = ?, deskripsi = ?, user_id = ? WHERE id = ?");
$stmt->bind_param("siiissii", $nama_produk, $harga, $stok, $kategori_id, $gambar_final, $deskripsi, $user_id, $id);

if ($stmt->execute()) {
    header("Location: ../../admin/produk.php?notif=ubah");
    exit;
} else {
    die("Gagal mengupdate produk: " . $stmt->error);
}