-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2025 at 06:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nama_kategori`) VALUES
(1, 'Fashion & Aksesoris'),
(2, 'makanan/minimuman'),
(3, 'Elektronik'),
(4, 'Olahraga & Outdor'),
(5, 'kecantikan'),
(6, 'Rumah Tangga & Perabot'),
(7, 'Produk Digital'),
(8, 'ATK'),
(9, 'Ibu & Anak'),
(10, 'Kesehatan'),
(11, 'Otomotif');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `total_harga` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','diproses','selesai') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `tanggal`, `total_harga`, `status`) VALUES
(2, 2, '2025-07-04 21:46:26', 1200000.00, 'selesai'),
(3, 2, '2025-07-05 06:38:35', 2400000.00, 'selesai'),
(4, 2, '2025-07-05 08:31:28', 16089998.00, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `status_admin` enum('pending','diproses','selesai','') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `jumlah`, `harga_satuan`, `status_admin`) VALUES
(2, 2, 1, 1, 1200000.00, 'selesai'),
(3, 3, 1, 2, 1200000.00, 'selesai'),
(4, 4, 4, 1, 89999.00, 'selesai'),
(5, 4, 5, 1, 5000000.00, 'selesai'),
(6, 4, 8, 1, 10999999.00, 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `nama_produk`, `harga`, `stok`, `deskripsi`, `gambar`, `kategori_id`, `user_id`) VALUES
(1, 'Jaket Pria', 800000.00, 28, '🧥 JAKET WINDBREAKER PRIA | ANTI ANGIN & RINGAN\r\nJaket pria model windbreaker, cocok untuk cuaca tidak menentu! Bahan ringan, adem, dan tetap stylish. Bisa dipakai buat riding, jogging, atau hangout santai.\r\n✨ Spesifikasi Produk:\r\n* Bahan: Parasut / Taslan premium (ringan & cepat kering)\r\n* Lapisan dalam: Jaring (tidak panas)\r\n* Model: Hoodie + resleting depan\r\n* Saku kanan & kiri dengan ritsleting\r\n* Ukuran: M – XXL (cek size chart di foto)\r\n* Warna: Hitam / Navy / Army / Abu-abu\r\n🔥 Keunggulan:\r\n✅ Tahan angin & gerimis ringan\r\n✅ Ringan, mudah dilipat & dibawa\r\n✅ Nyaman dipakai harian\r\n✅ Desain kasual & sporty\r\n💡 Cocok untuk:\r\n• Naik motor\r\n• Lari / jogging\r\n• Traveling\r\n• Gaya kasual harian\r\n📦 READY STOCK & SIAP KIRIM!\r\n🛒 Langsung checkout sekarang, stok terbatas!', '6867e854e9af3.jpeg', 1, 1),
(3, 'Jasa Koding Website', 700000.00, 999898989, 'Jasa Pembuatan Website Profesional — Solusi Digital untuk Bisnis Anda\r\n\r\nButuh website keren, fungsional, dan responsif untuk mengembangkan bisnis? Kami menyediakan layanan pembuatan website custom sesuai kebutuhan Anda, dari website sederhana hingga platform kompleks.\r\n\r\nLayanan Kami Meliputi:\r\n\r\nWebsite company profile / bisnis\r\n\r\nToko online (e-commerce) dengan fitur lengkap\r\n\r\nWebsite personal / portfolio\r\n\r\nSistem pemesanan / booking online\r\n\r\nDashboard admin mudah digunakan\r\n\r\nKeunggulan:\r\n✅ Desain modern, responsif, dan mobile-friendly\r\n✅ Pengembangan cepat dengan teknologi terkini\r\n✅ Mudah dikelola tanpa perlu keahlian teknis\r\n✅ SEO-friendly untuk mendukung visibilitas online\r\n✅ Support dan maintenance pasca pembuatan\r\n\r\nKenapa Memilih Kami?\r\n\r\nTim developer berpengalaman dan profesional\r\n\r\nHarga kompetitif sesuai dengan kebutuhan Anda\r\n\r\nProses komunikasi mudah dan transparan\r\n\r\nGaransi kualitas dan ketepatan waktu\r\n\r\nMulai sekarang, bawa bisnis Anda ke dunia digital dengan website yang tepat!\r\nHubungi kami untuk konsultasi gratis dan dapatkan penawaran terbaik!', '68687924a3eb8.png', 7, 1),
(4, 'Sepatu Pria Modern', 89999.00, 119, 'Sepatu Casual Pria – Gaya Santai dan Nyaman Sepanjang Hari\r\nLengkapi penampilanmu dengan sepatu casual pria yang stylish dan nyaman ini. Dirancang khusus untuk aktivitas sehari-hari, sepatu ini menghadirkan kombinasi sempurna antara desain modern dan kenyamanan maksimal.\r\n\r\nFitur Utama:\r\nBahan: [contoh: kulit sintetis / kanvas berkualitas / suede lembut]\r\nSol karet anti slip, tahan lama dan fleksibel\r\nDesain simpel dengan warna netral yang mudah dipadukan dengan berbagai outfit\r\nDetail jahitan rapi dan kuat\r\nUkuran tersedia: 39 – 44 (sesuaikan dengan stok)\r\n\r\nKeunggulan:\r\nRingan dan breathable, cocok untuk dipakai seharian\r\nTampil casual tapi tetap trendy\r\nCocok untuk aktivitas santai, jalan-jalan, atau ke kantor dengan dress code kasual', '68687a078f760.jpeg', 1, 1),
(5, 'Analisis Data', 5000000.00, 9999998, 'Jasa Analisis Data Profesional — Ambil Keputusan Lebih Tepat dengan Data Akurat\r\nButuh insight mendalam dari data bisnis atau penelitian kamu? Kami siap membantu menganalisis data dengan metode yang tepat, memberikan laporan lengkap dan rekomendasi strategis untuk menunjang keputusan bisnis.\r\n\r\nLayanan Kami:\r\nPengolahan dan pembersihan data\r\nAnalisis statistik (deskriptif & inferensial)\r\nVisualisasi data interaktif\r\nPembuatan laporan dan presentasi hasil analisis\r\nAnalisis data kuantitatif dan kualitatif\r\n\r\nData mining dan predictive analytics (jika diperlukan)\r\n\r\nKeunggulan:\r\n✅ Menggunakan software analisis terkini (SPSS, R, Python, Excel, dll)\r\n✅ Metodologi analisis sesuai kebutuhan proyek\r\n✅ Hasil analisis mudah dipahami dan aplikatif\r\n✅ Konsultasi dan revisi gratis sampai klien puas\r\n\r\nSiapa yang Cocok Pakai Jasa Ini?\r\n✔ Pelaku bisnis yang ingin memahami performa dan tren pasar\r\n✔ Peneliti dan akademisi untuk mendukung studi\r\n✔ Startup dan perusahaan dalam pengambilan keputusan berbasis data\r\n✔ Tim marketing untuk analisis customer behavior\r\n\r\nDapatkan insight data yang akurat dan terpercaya! Hubungi kami sekarang untuk konsultasi gratis dan penawaran terbaik.', '68687ad4cc838.png', 7, 1),
(6, 'Tas Ransel', 389900.00, 20, 'Tas Ransel Pria/Wanita – Stylish, Praktis, dan Tahan Lama\r\nTemani aktivitas harianmu dengan tas ransel yang nyaman dan multifungsi ini. Cocok untuk kerja, sekolah, traveling, atau sekadar jalan-jalan santai.\r\n\r\nFitur Utama:\r\nBahan: Polyester / Canvas berkualitas tinggi, tahan air dan tahan lama\r\nKapasitas luas dengan banyak kompartemen untuk menyimpan laptop, gadget, buku, dan kebutuhan sehari-hari\r\nTali bahu empuk dan dapat disesuaikan untuk kenyamanan maksimal\r\nDesain modern dan minimalis, cocok untuk segala gaya\r\nUkuran: [sebutkan ukuran, misal 45cm x 30cm x 15cm]\r\n\r\nKeunggulan:\r\nRingan tapi kuat, ideal untuk penggunaan sehari-hari\r\nTahan air, melindungi barang dari hujan ringan\r\nBanyak kantong dan slot untuk organisasi barang yang rapi\r\nCocok untuk aktivitas outdoor maupun indoor', '68687bdcd3ce2.jpg', 1, 1),
(7, 'HP PINTAR', 7999999.00, 5, 'Apple iPhone 13 – Performa Super Cepat & Kamera Profesional\r\nNikmati pengalaman smartphone premium dengan iPhone 13, yang hadir dengan desain elegan, performa bertenaga, dan teknologi kamera canggih. Cocok untuk kamu yang menginginkan kualitas terbaik dalam genggaman.\r\n\r\nSpesifikasi Utama:\r\nLayar Super Retina XDR 6.1 inci – tampilan lebih cerah dan jernih\r\nChip A15 Bionic – proses cepat dan hemat daya\r\nKamera ganda 12MP dengan mode malam, Deep Fusion, dan video Cinematic\r\nKapasitas penyimpanan: 128GB / 256GB / 512GB\r\nBaterai tahan lama dengan pengisian cepat dan pengisian nirkabel\r\nSistem operasi iOS terbaru dengan fitur keamanan dan privasi terbaik\r\n\r\nFitur Unggulan:\r\nDesain tahan air dan debu (IP68)\r\nFace ID untuk keamanan maksimal\r\nKonektivitas 5G super cepat\r\nPengalaman gaming dan multimedia yang mulus\r\n\r\nDapatkan iPhone 13 sekarang dan rasakan teknologi terbaik di ujung jari Anda!\r\nStok terbatas, pesan segera!', '68687cba7208a.webp', 3, 3),
(8, 'Jersey Timnas', 10999998.00, 119, 'Jersey Timnas Indonesia – Semangat Juang di Setiap Laga!\r\nDukung perjuangan Garuda dengan jersey resmi Timnas Indonesia yang nyaman dan bergaya. Terbuat dari bahan breathable berkualitas tinggi, jersey ini cocok untuk olahraga, kumpul bareng teman, atau koleksi penggemar sepak bola sejati.\r\n\r\nFitur Produk:\r\nBahan: Polyester breathable, cepat kering dan nyaman dipakai\r\nDesain resmi dengan logo dan warna khas merah putih\r\nCutting modern dan fit, mendukung mobilitas aktif saat bergerak\r\nTersedia ukuran: S, M, L, XL, XXL\r\nCocok untuk olahraga dan aktivitas casual sehari-hari\r\n\r\nKeunggulan:\r\nRingan dan tidak gerah\r\nJahitan kuat dan detail logo presisi\r\nTampilan sporty dan keren, tunjukkan dukunganmu untuk Timnas!\r\n\r\nSiap jadi bagian dari semangat kebangsaan? Dapatkan jersey Timnas Indonesia sekarang juga!', '68687d9a46f9e.jpg', 4, 3),
(9, 'Bola Voli', 300000.00, 20, 'Bola Voli Premium – Kualitas Terbaik untuk Permainan Maksimal\r\nTingkatkan performa permainanmu dengan bola voli berkualitas tinggi ini. Dirancang untuk tahan lama dan nyaman digunakan, cocok untuk latihan maupun pertandingan resmi.\r\n\r\nSpesifikasi Produk:\r\nBahan: Synthetic leather / kulit sintetis berkualitas\r\nUkuran standar resmi (size 5) sesuai regulasi internasional\r\nBerat: Sesuai standar bola voli profesional\r\nPermukaan halus dan tahan lama, memberikan grip optimal\r\nWarna: Kombinasi cerah untuk visibilitas terbaik di lapangan\r\n\r\nKeunggulan:\r\nTahan benturan dan tekanan tinggi\r\nRingan dan mudah dikontrol saat servis dan smash\r\nCocok untuk penggunaan indoor dan outdoor\r\nIdeal untuk klub, sekolah, dan komunitas voli\r\n\r\nSiap meningkatkan skill voli kamu? Dapatkan bola voli premium ini sekarang juga!', '68687e9b8a668.jpeg', 4, 3),
(10, 'Panci Listrik', 250000.00, 32, 'Panci Listrik Serbaguna – Praktis & Efisien untuk Masak Sehari-hari\r\nMasak lebih mudah dan cepat dengan panci listrik multifungsi ini. Cocok untuk memasak, mengukus, merebus, dan menghangatkan makanan dengan praktis tanpa perlu kompor.\r\n\r\nFitur Produk:\r\nKapasitas: [misal 3 liter / 5 liter]\r\nDaya listrik: [misal 400 watt] hemat energi\r\nMaterial: Stainless steel / anti karat, mudah dibersihkan\r\nDilengkapi pengatur suhu dan timer otomatis\r\nDesain compact dan ringan, mudah disimpan dan dibawa\r\nKabel bisa dilepas untuk kemudahan penyimpanan\r\n\r\nKeunggulan:\r\nMemasak cepat dan merata\r\nAman digunakan dengan proteksi overheating\r\nMultifungsi: cocok untuk sup, rebusan, steam, dan makanan lainnya\r\nCocok untuk rumah, kos, dan traveling\r\n\r\nNikmati kemudahan memasak dengan panci listrik serbaguna ini. Pesan sekarang, stok terbatas!', '68687f3d2db41.jpeg', 6, 3),
(11, 'Master Rem', 2000000.00, 20, 'Master Rem – Kendali Pengereman Lebih Maksimal & Responsif\r\nPastikan sistem pengereman kendaraan kamu bekerja optimal dengan master rem berkualitas tinggi. Komponen penting ini dirancang untuk memberikan tekanan hidrolik yang stabil dan responsif, meningkatkan keamanan dan kenyamanan saat berkendara.\r\n\r\nSpesifikasi Produk:\r\nMaterial: Aluminium alloy / Baja tahan lama\r\nCocok untuk: [sebutkan tipe kendaraan, misal motor matic/manual atau mobil tertentu]\r\nUkuran: [misal 14mm / 1/2 inch – sesuaikan dengan produk]\r\nSudah termasuk tuas / selang (jika ada)\r\nTahan panas & tekanan tinggi\r\n\r\nKeunggulan:\r\n✅ Pengereman lebih pakem & stabil\r\n✅ Mudah dipasang, cocok untuk mekanik maupun pengguna pribadi\r\n✅ Cocok untuk pemakaian harian maupun performa tinggi\r\n✅ Desain ergonomis & kokoh\r\n\r\nGanti master rem lama kamu dengan yang baru & berkualitas. Stok terbatas – langsung checkout sekarang juga!', '68687fcdd9a89.jpeg', 11, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Yoga Pratama', 'admin@gmail.com', '$2y$10$XVgdoZcUPbnG7N12z.ZTXOZ4eed6JNQRhnVHD15v.VcKO/np27rte', 'admin'),
(2, 'User Tester', 'user@gmail.com', '$2y$10$/mviBajW6wZpX4aRiNobhO1ErqH1QJT1SL4SynChVBb4HNVUS4PIK', 'user'),
(3, 'admin', 'admin1@gmail.com', '$2y$10$BnCNgBlTA.vijCO2g4V6TewAQYdpvv9LazR7ZaPn.WXVArFSxiXvC', 'admin'),
(4, 'agus bagus', 'h@gmail.com', '$2y$10$mDoz3iplcdjPjkvG/9BYHuJ.4tY.X3wKI7upflCu5SVGVGNH63Pri', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
