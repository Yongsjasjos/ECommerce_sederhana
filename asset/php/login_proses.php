<?php
session_start();
include 'koneksi.php';
if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt  = $koneksi->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = $user['role'];

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: ../../admin/dashboard.php");
            } else {
                header("Location: ../../user/katalog.php");
            }
            exit;
        } else {
            header("Location: ../../login.php?error=Password salah!");
            exit;
        }
    } else {
        header("Location: ../../login.php?error=Email tidak ditemukan!");
        exit;
    }
} else {
    header("Location: ../../login.php");
    exit;
}