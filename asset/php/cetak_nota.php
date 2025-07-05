<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek login & role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

include 'koneksi.php';

// Validasi parameter order_id
if (!isset($_GET['order_id'])) {
    echo "ID pesanan tidak ditemukan.";
    exit;
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Ambil data pesanan
$sql_order = "SELECT id, status, total_harga, tanggal FROM orders WHERE id = ? AND user_id = ?";
$stmt = $koneksi->prepare($sql_order);
if (!$stmt) {
    die("Query order error: " . $koneksi->error);
}
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result_order = $stmt->get_result();

if ($result_order->num_rows === 0) {
    echo "Pesanan tidak ditemukan atau bukan milik Anda.";
    exit;
}

$order = $result_order->fetch_assoc();

// Ambil item pesanan yang selesai
$sql_items = "SELECT oi.jumlah, oi.harga_satuan, p.nama_produk 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ? AND oi.status_admin = 'selesai'";
$stmt_items = $koneksi->prepare($sql_items);
if (!$stmt_items) {
    die("Query items error: " . $koneksi->error);
}
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

if ($items->num_rows === 0) {
    echo "Tidak ada item yang selesai pada pesanan ini.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pesanan #<?= $order['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h2 { text-align: center; }
        .info-pesanan { width: 100%; margin-top: 25px; border-collapse: collapse; }
        .info-pesanan td { padding: 6px 10px; border: none; }
        .info-pesanan td:first-child { width: 30%; font-weight: bold; }

        table.produk {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        table.produk th, table.produk td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
        }
        .total { font-weight: bold; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:right;">
        <button onclick="window.print()">🖨️ Cetak</button>
    </div>

    <h2>Nota Pesanan</h2>
    <hr>

    <table class="info-pesanan" role="presentation">
        <tr>
            <td>ID Pesanan</td>
            <td>: <?= $order['id']; ?></td>
        </tr>
        <tr>
            <td>Tanggal Pesanan</td>
            <td>:
                <?php 
                if (!empty($order['tanggal']) && strtotime($order['tanggal'])) {
                    echo date('d-m-Y H:i', strtotime($order['tanggal']));
                } else {
                    echo 'Tanggal tidak tersedia';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: <?= ucfirst($order['status']); ?></td>
        </tr>
        <tr>
            <td>Nama Pelanggan</td>
            <td>: <?= htmlspecialchars($_SESSION['nama']); ?></td>
        </tr>
    </table>

    <table class="produk">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($item = $items->fetch_assoc()): 
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['nama_produk']); ?></td>
                <td><?= $item['jumlah']; ?></td>
                <td>Rp<?= number_format($item['harga_satuan'], 0, ',', '.'); ?></td>
                <td>Rp<?= number_format($subtotal, 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="total">
                <td colspan="3" class="text-right">Total</td>
                <td>Rp<?= number_format($total, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di toko kami!<br>
        Dicetak pada: <?= date('d-m-Y H:i'); ?>
    </div>
</body>
</html>
