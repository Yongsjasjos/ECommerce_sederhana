<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

include 'koneksi.php';

$admin_id  = intval($_SESSION['user_id']);
$tgl_awal  = $_POST['tanggal_awal'] ?? '';
$tgl_akhir = $_POST['tanggal_akhir'] ?? '';

if (!$tgl_awal || !$tgl_akhir || !strtotime($tgl_awal) || !strtotime($tgl_akhir)) {
    exit("Tanggal tidak valid.");
}

// Ambil order_id yang terkait produk milik admin & status selesai di item & order
$sql = "
    SELECT DISTINCT oi.order_id
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE p.user_id = ?
      AND oi.status_admin = 'selesai'
      AND oi.status_admin = 'selesai'
      AND DATE(o.tanggal) BETWEEN ? AND ?
";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iss", $admin_id, $tgl_awal, $tgl_akhir);
$stmt->execute();
$res = $stmt->get_result();

$order_ids = [];
while ($row = $res->fetch_assoc()) {
    $order_ids[] = intval($row['order_id']);
}

if (empty($order_ids)) {
    exit("<p>Tidak ada pesanan selesai untuk produk Anda di periode tersebut.</p>");
}

// Ambil data lengkap order
$ids_in = implode(",", $order_ids);
$sql2 = "
    SELECT o.*, u.nama AS nama_user
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id IN ($ids_in)
    ORDER BY o.tanggal DESC
";
$orders = $koneksi->query($sql2);

$total_seluruh = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Produk Selesai Saya</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; }
        h2, p { text-align: center; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { border-bottom: 1px solid #ddd; padding: 10px; vertical-align: top; }
        th { background: #eee; }
        .produk-table td { padding: 3px 6px; border: none; font-size: 13px; }
        .text-right { text-align: right; }
        .grand-total td { font-weight: bold; background: #fafafa; }
        .btn-print { margin: 20px auto; display: block; padding: 8px 12px; background: #007bff; color: white; border: none; cursor: pointer; }
        .btn-print:hover { background: #0056b3; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>

<h2>Laporan Produk Selesai Saya</h2>
<p>Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s.d. <?= date('d-m-Y', strtotime($tgl_akhir)) ?></p>
<button onclick="window.print()" class="btn-print">Cetak / Simpan PDF</button>

<table>
    <thead>
        <tr>
            <th>ID Pesanan</th>
            <th>Pembeli</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Produk Saya</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
<?php while ($o = $orders->fetch_assoc()): 
    $oid = intval($o['id']);

    // Ambil produk milik admin dengan status selesai
    $sp = $koneksi->prepare("
        SELECT p.nama_produk, oi.jumlah, oi.harga_satuan
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ? AND p.user_id = ? AND oi.status_admin = 'selesai'
    ");
    $sp->bind_param("ii", $oid, $admin_id);
    $sp->execute();
    $pr = $sp->get_result();

    // Hitung total produk admin
    $st = $koneksi->prepare("
        SELECT SUM(oi.jumlah * oi.harga_satuan) AS tot
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ? AND p.user_id = ? AND oi.status_admin = 'selesai'
    ");
    $st->bind_param("ii", $oid, $admin_id);
    $st->execute();
    $tr = $st->get_result()->fetch_assoc();
    $tot = $tr['tot'] ?? 0;

    if ($tot <= 0) continue;
    $total_seluruh += $tot;
?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['nama_user']) ?></td>
            <td><?= date('d-m-Y', strtotime($o['tanggal'])) ?></td>
            <td><?= ucfirst($o['status']) ?></td>
            <td>
                <table class="produk-table">
                <?php while ($p = $pr->fetch_assoc()): ?>
                    <tr>
                        <td>• <?= htmlspecialchars($p['nama_produk']) ?></td>
                        <td><?= $p['jumlah'] ?>× Rp<?= number_format($p['harga_satuan'], 0, ',', '.') ?></td>
                        <td>= Rp<?= number_format($p['jumlah'] * $p['harga_satuan'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
                </table>
            </td>
            <td class="text-right">Rp<?= number_format($tot, 0, ',', '.') ?></td>
        </tr>
<?php endwhile; ?>
        <tr class="grand-total">
            <td colspan="5" class="text-right">TOTAL SEMUA PESANAN</td>
            <td class="text-right">Rp<?= number_format($total_seluruh, 0, ',', '.') ?></td>
        </tr>
    </tbody>
</table>

</body>
</html>
