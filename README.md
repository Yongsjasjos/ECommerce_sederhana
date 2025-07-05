# 🛒 Proyek E-Commerce Sederhana

Sistem aplikasi ini merupakan proyek e-commerce berbasis **PHP dan MySQL** yang dilengkapi dengan autentikasi multi-role (Admin & User), manajemen produk, pemrosesan pemesanan, dan pencetakan faktur.

📌 **Status: FINAL RELEASE (versi stabil)**  
📜 **Lisensi: Bebas digunakan untuk pembelajaran & pengembangan dengan mencantumkan sumber**

---

## 🔐 Fitur Keamanan

- 🔑 **Login Multi-Role:** Mendukung autentikasi berbasis peran (admin & user).
- 🔐 **Hashing Password:** Registrasi pengguna menggunakan password yang telah di-*hash* untuk keamanan data.
- 🧱 **Validasi Input:** Semua input diproses menggunakan `htmlspecialchars()` untuk mencegah serangan XSS.
- 🧩 **RBAC (Role-Based Access Control):** Hak akses dibatasi sesuai peran pengguna.
- 📜 **ACL (Access Control List):** Kontrol akses halaman berdasarkan daftar hak peran.

---

## 🎯 Fitur Aplikasi
 - semua data sudah dikelola dengan baik, sehingga data yang ditampilkan hanyalah data milik akun terkait saja
### 👑 Admin
- CRUD Produk (+ Fitur Pencarian).
- Melihat dan memproses pesanan masuk.
- Mencetak laporan berdasarkan rentang waktu *(fitur aktif)*.
- Melihat grafik penjualan periode 7 hari terakhir

### 🙋 User
- Melihat katalog dan detail produk.
- Menambahkan produk ke keranjang dan melakukan checkout.
- checkout banyak barang dengan menggunakan filtur keranjang
- Melihat riwayat pembelian.
- Mencetak nota transaksi dalam format PDF.

---

## 📁 Struktur Proyek

| Folder / File               | Deskripsi                                  |
|----------------------------|---------------------------------------------|
| **admin/**                 | Halaman backend untuk admin                 |
| ├── dashboard.php          | Beranda admin                               |
| ├── produk.php             | CRUD + pencarian produk                     |
| ├── pesanan.php            | Data pesanan yang masuk                     |
| └── laporan.php            | Cetak laporan pesanan                       |
| **user/**                  | Halaman pengguna                            |
| ├── katalog.php            | Katalog produk                              |
| ├── detail_produk.php      | Detail produk                               |
| ├── keranjang.php          | Keranjang belanja                           |
| ├── checkout.php           | Proses checkout                             |
| ├── riwayat.php            | Riwayat transaksi                           |
| └── sukses.php             | Notifikasi sukses transaksi                 |
| **asset/**                 | Asset pendukung                             |
| ├── img/                   | Gambar produk                               |
| └── php/                   | Skrip backend PHP                           |
|     ├── koneksi.php        | Koneksi database                            |
|     ├── login.php          | Proses login                                |
|     ├── simpan_produk.php  | Simpan data produk                          |
|     ├── update_produk.php  | Update data produk                          |
|     ├── hapus_produk.php   | Hapus data produk                           |
|     ├── tambah_keranjang.php| Tambah ke keranjang                        |
|     ├── hapus_keranjang.php| Hapus dari keranjang                        |
|     ├── checkout_proses.php| Proses checkout                             |
|     ├── cetak_nota.php     | Cetak nota transaksi                        |
|     ├── laporan_pdf.php    | Generate laporan dalam bentuk PDF           |
|     └── laporan.php        | Filter laporan                              |
| **login.php**              | Halaman login utama                         |
| **logout.php**             | Proses logout                               |
| **register.php**           | Registrasi akun pengguna                    |
| **database/**              | Struktur database                           |
| └── ecommerce.sql          | File SQL struktur & data awal               |

---

## ▶️ Cara Instalasi dan Penggunaan

1. **Clone atau unduh** proyek ke direktori lokal web server Anda (`htdocs` untuk XAMPP).
2. **Import** file `ecommerce.sql` ke MySQL melalui phpMyAdmin.
3. **Edit konfigurasi** database di `asset/php/koneksi.php`.
4. Akses melalui browser:

```bash
http://localhost/ecommerce/
```

### Akun Login

**Admin 1**
- Email: admin@gmail.com
- Password: admin

**Admin 2**
- Email: admin@gmail.com
- Password : 123

**User**
- Email: user@gmail.com
- Password: 123

---

## 📚 Glosarium

| Istilah                              | Penjelasan                                                               |
|-------------------------------------|--------------------------------------------------------------------------|
| **Authentication**                  | Verifikasi identitas pengguna (misalnya login)                           |
| **Authorization**                   | Hak akses pengguna terhadap halaman tertentu                             |
| **Access Control**                  | Pembatasan akses berdasarkan status pengguna                             |
| **RBAC (Role-Based Access Control)**| Hak akses berdasarkan peran (admin/user)                                 |
| **ACL (Access Control List)**       | Daftar kontrol hak akses per halaman atau fitur                          |

---

## ⚠️ Lisensi & Ketentuan

Proyek ini **dapat digunakan secara bebas** untuk keperluan:
- Pembelajaran
- Modifikasi untuk pengembangan lanjutan

📢 **Syarat penggunaan:**
> Setiap penggunaan ulang atau distribusi proyek ini **wajib mencantumkan nama pembuat dan sumber repositori GitHub asli.**

**Contoh kutipan:**
> *"Proyek ini dikembangkan oleh Yoga Pratama. Repositori asli dapat ditemukan di: https://github.com/Yongsjasjos/ecommerce"*

---

## 👨‍💻 Tentang Pengembang

**Yoga Pratama**  
📧 Email: [yp170090@gmail.com](mailto:yp170090@gmail.com)  
🔗 LinkedIn: [linkedin.com/in/yoga-pratama-923202349](https://www.linkedin.com/in/yoga-pratama-923202349/)  
🐱 GitHub: [github.com/Yongsjasjos](https://github.com/Yongsjasjos)

---

> Untuk saran, masukan, atau kolaborasi, silakan hubungi saya melalui kanal yang tersedia di atas.
