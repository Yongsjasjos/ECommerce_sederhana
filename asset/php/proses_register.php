<?php
include 'koneksi.php';

$nama     = $_POST['nama'];
$email    = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

try {
    // Cek apakah email sudah terdaftar
    $cek = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar!');history.back();</script>";
        exit;
    }

    // Simpan user baru
    $stmt = $koneksi->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $nama, $email, $password);
    $stmt->execute();

    echo "<script>alert('Pendaftaran berhasil! Silakan login.');window.location='../../login.php';</script>";

} catch (Exception $e) {
    // Tangani error
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>