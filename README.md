# SiKalog UMKM — Sistem Katalog Produk UMKM Berbasis Web

Website siap pakai untuk mini project RPL/SKPL. Dibangun dengan **PHP native + MySQL**
(tanpa framework), mengikuti persis wireframe dan seluruh 24 skenario Black Box Testing
pada dokumen SKPL Anda. Bisa dijalankan di **XAMPP/Laragon lokal** atau di-upload ke
**hosting cPanel mana pun yang mendukung PHP + MySQL** (hosting jenis yang sama dipakai
untuk WordPress).

---

## 1. Daftar Isi Paket

```
sikalog-umkm-site/
├── admin/                  -> Seluruh halaman & logika admin
│   ├── includes/            (layout sidebar admin)
│   ├── login.php            (Login Admin)
│   ├── dashboard.php        (Dashboard ringkasan)
│   ├── produk.php           (Daftar semua produk)
│   ├── tambah-produk.php    (Form tambah produk)
│   ├── edit-produk.php      (Form edit produk)
│   ├── proses-hapus.php     (Handler hapus produk)
│   ├── kategori.php         (Kelola kategori)
│   └── logout.php
├── assets/
│   ├── css/style.css        (Semua styling situs)
│   ├── js/script.js         (Thumbnail gallery, konfirmasi hapus, dll)
│   └── img/placeholder.png
├── config/
│   └── database.php         (** WAJIB DIATUR sebelum dipakai **)
├── includes/
│   ├── header.php / footer.php  (Navbar & footer publik)
│   └── functions.php        (Fungsi bantuan: format rupiah, pagination, dll)
├── uploads/                  (Folder gambar produk yang diupload admin)
├── index.php                 (Beranda)
├── katalog.php                (Katalog produk: cari, filter, pagination)
├── detail.php                 (Detail produk: galeri, tombol WhatsApp)
└── sikalog_umkm_database.sql  (Script database — import ini duluan)
```

---

## 2. Cara Instalasi — LOKAL (XAMPP / Laragon)

1. **Install XAMPP** (https://www.apachefriends.org) jika belum punya.
2. Copy seluruh folder `sikalog-umkm-site` ke dalam `C:\xampp\htdocs\` (Windows) atau
   `/Applications/XAMPP/htdocs/` (Mac). Boleh di-rename folder sesuai keinginan, misalnya `htdocs/sikalog`.
3. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
4. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), klik tab **Import**, pilih file
   `sikalog_umkm_database.sql`, klik **Go**. Database `sikalog_umkm` beserta seluruh
   tabel dan 10 data produk contoh akan otomatis terbuat.
5. Buka `config/database.php`, pastikan settingnya seperti ini (default XAMPP):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'sikalog_umkm');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
6. Buka browser ke `http://localhost/sikalog-umkm-site/index.php` — situs sudah aktif!

---

## 3. Cara Instalasi — HOSTING / PRODUKSI (cPanel, Niagahoster, Hostinger, dll)

1. Buat database MySQL baru lewat **cPanel → MySQL Databases**. Catat nama database,
   username, dan password yang dibuat (biasanya hosting memberi prefix otomatis,
   misal `namauser_sikalog`).
2. Buka **phpMyAdmin** di cPanel, pilih database tadi, import file
   `sikalog_umkm_database.sql`.
3. Upload seluruh isi folder `sikalog-umkm-site` ke `public_html` (lewat File Manager
   atau FTP/FileZilla).
4. Edit `config/database.php`, isi dengan kredensial database hosting Anda:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'namauser_sikalog');
   define('DB_USER', 'namauser_sikalog');
   define('DB_PASS', 'password_database_anda');
   ```
5. Buka domain Anda di browser — situs langsung aktif dan **bisa diakses publik**.

> Hosting jenis ini (PHP + MySQL) persis sama dengan yang dipakai untuk hosting
> WordPress, jadi paket ini kompatibel dengan hosting WordPress mana pun.

---

## 4. Login Admin Default

| Username | Password   |
|----------|-----------|
| `admin`  | `admin123` |

**Sangat disarankan langsung ganti password setelah live**, dengan menjalankan query
berikut di phpMyAdmin (ganti `password_baru_anda` dan jalankan hasil hash dari
https://phpdevtools.dev/bcrypt-hash-generator atau buat lewat PHP):

```sql
UPDATE admin SET password = '<hasil_bcrypt_hash_password_baru>' WHERE username = 'admin';
```

---

## 5. Pengaturan yang Perlu Disesuaikan

Buka `config/database.php`, ubah baris berikut sesuai nomor WhatsApp pemilik UMKM
(format internasional, tanpa tanda `+`):

```php
define('WA_NUMBER', '628123456789');
```

---

## 6. Pemetaan Wireframe ke File

| Wireframe (dari dokumen SKPL) | File Implementasi |
|---|---|
| Beranda | `index.php` |
| Katalog Produk | `katalog.php` |
| Detail Produk | `detail.php` |
| Login Admin | `admin/login.php` |
| Admin Dashboard | `admin/dashboard.php` |

---

## 7. Pemetaan Black Box Testing ke Fitur

Seluruh 24 skenario pada dokumen Bab 5.3 (Black Box Testing) sudah diimplementasikan
dan diuji langsung di lingkungan PHP + MySQL sungguhan sebelum paket ini dikirim:

- **Navigasi (Beranda)**: menu BERANDA/KATALOG/TENTANG KAMI/KONTAK, tombol CTA "Lihat
  Katalog", klik produk → detail, link sosial media buka tab baru, tombol WA membuka
  `wa.me`.
- **Katalog**: search box + tombol Cari (query `LIKE`), search kosong = tampil semua,
  filter kategori, tombol "Semua Produk" reset filter, tombol Detail, pagination
  nomor halaman + Previous/Next.
- **Detail Produk**: navigasi Home/Catalog, 3 thumbnail mengganti gambar utama
  (JavaScript), tombol WhatsApp hijau membawa pesan otomatis berisi nama produk +
  harga, produk terkait dari kategori yang sama.
- **Login Admin**: verifikasi username/password ke database, pesan error merah jika
  salah, validasi wajib isi jika kosong, password tersamar otomatis (`type=password`),
  logo kembali ke Beranda.
- **Dashboard Admin**: kartu Total Produk & Total Kategori (live dari database), tabel
  Produk Terbaru, tombol Tambah Produk Baru, tombol Edit (hijau) & Hapus (merah,
  dengan dialog konfirmasi `confirm()`), pagination, Logout menghancurkan session.

---

## 8. Catatan Keamanan (sudah diterapkan)

- Semua query database memakai **Prepared Statement (PDO)** — aman dari SQL Injection.
- Password admin disimpan ter-**hash bcrypt**, bukan teks biasa.
- Validasi upload gambar: maksimal **2 MB**, hanya format **JPG/PNG/WEBP** (dicek dari
  isi file asli, bukan cuma ekstensi nama file).
- Folder `uploads/` diberi `.htaccess` supaya file `.php` tidak bisa dieksekusi di
  sana, sekalipun ada yang berhasil mengunggah file berbahaya.
- Semua output ke HTML melewati fungsi `h()` (escaping) untuk mencegah XSS.
- Halaman admin dilindungi `wajibLogin()` — otomatis redirect ke Login jika sesi tidak
  ada / sudah habis.

---

## 9. Pertanyaan Umum

**Q: Apakah ini WordPress asli?**
A: Bukan — ini situs PHP+MySQL custom yang dibangun khusus mengikuti wireframe Anda.
Membuat tampilan custom seperti ini akan sangat sulit dicapai memakai WordPress +
tema/plugin siap pakai (Astra/WooCommerce) tanpa coding custom theme yang berat. Paket
ini berjalan di jenis hosting yang **sama persis** dengan hosting WordPress, jadi tetap
bisa Anda upload ke hosting mana pun yang biasa dipakai untuk WordPress.

**Q: Bagaimana cara menambah gambar produk lebih dari 1?**
A: Saat Tambah/Edit Produk, ada 3 kolom upload: Gambar Utama, Gambar Tambahan 1, dan
Gambar Tambahan 2. Ketiganya akan otomatis muncul sebagai thumbnail yang bisa diklik
di halaman Detail Produk.

**Q: Saya upload ke hosting tapi gambar tidak muncul.**
A: Pastikan folder `uploads/` punya permission **755** atau **775** (writable). Bisa
diatur lewat File Manager hosting → klik kanan folder → Permissions.
