# SiKalog UMKM — Website Katalog Produk UMKM

Website statis (HTML, CSS, JavaScript murni) untuk membantu pelaku UMKM memasarkan produk secara digital. Dibuat agar bisa langsung di-deploy ke **GitHub Pages**.

## 📁 Struktur Halaman

| Halaman | File | Akses |
|---|---|---|
| Beranda | `index.html` | Publik |
| Katalog Produk | `katalog.html` | Publik |
| Detail Produk | `produk-detail.html?id=...` | Publik |
| Login Admin | `login.html` | Publik |
| Dashboard Admin | `dashboard.html` | Admin (login) |
| Daftar Produk | `admin-produk.html` | Admin (login) |
| Tambah/Edit Produk | `admin-produk-form.html` | Admin (login) |
| Kelola Kategori | `admin-kategori.html` | Admin (login) |

## 🔑 Akun Demo Admin

- **Username:** `admin`
- **Password:** `admin123`

Password disimpan dalam bentuk hash (SHA-256) — bukan teks biasa.

## 🚪 Cara Masuk ke Halaman Login (tersembunyi)

Tidak ada tautan "Login" di navigasi publik agar tampilan tetap bersih untuk pengunjung. Ada 2 cara mengakses halaman login admin:

1. **Buka langsung:** tambahkan `login.html` di akhir URL, misalnya `https://username-anda.github.io/sikalog-umkm/login.html`.
2. **Trigger tersembunyi:** klik teks copyright di footer (`© 2026 SiKalog UMKM. Hak Cipta Dilindungi.`) sebanyak **5 kali berturut-turut** dalam waktu 2 detik. Sistem otomatis mengarahkan ke halaman Login Admin. Tersedia di footer Beranda, Katalog, dan Detail Produk.

## 🚀 Cara Deploy ke GitHub Pages

1. Buat repository baru di GitHub, misalnya `sikalog-umkm`.
2. Upload seluruh isi folder ini (jangan folder-nya, tapi isinya) ke root repository.
3. Buka **Settings → Pages** pada repository.
4. Pada **Source**, pilih branch `main` dan folder `/root`, lalu klik **Save**.
5. Tunggu beberapa menit, website akan aktif di `https://<username>.github.io/sikalog-umkm/`.

## ⚙️ Konfigurasi

Edit file `js/data.js`, bagian `UMKM_CONFIG` di bagian atas, untuk mengubah:
- Nama & slogan UMKM
- Deskripsi, lokasi, email
- **Nomor WhatsApp** (ganti `whatsapp: "6281234567890"` dengan nomor asli, format `62xxxxxxxxxx` tanpa tanda `+`)
- Link Instagram & Facebook
- Durasi session timeout admin (default 30 menit)

## 🗄️ Tentang "Database"

Karena GitHub Pages adalah *static hosting* (tidak ada server/PHP/MySQL), data Admin, Kategori, dan Produk disimpan menggunakan **localStorage** di browser pengunjung, dengan struktur field yang mengikuti rancangan database SIKALOG UMKM (id_admin, id_kategori, id_produk, dst).

Konsekuensinya:
- Data yang diinput/diedit admin **hanya tersimpan di browser & perangkat itu sendiri**, tidak otomatis muncul di perangkat/browser lain.
- Menghapus cache/localStorage browser akan mengembalikan data ke kondisi awal (seed data).
- Gambar produk yang diunggah admin disimpan sebagai base64 di localStorage (bukan file di server), sehingga jangan mengunggah terlalu banyak gambar beresolusi besar karena localStorage punya batas kapasitas (~5–10MB per domain).
- Ini cocok untuk **demo, tugas kuliah, atau portofolio**. Untuk penggunaan produksi nyata (multi-user, data konsisten di semua perangkat), sebaiknya gunakan versi backend (PHP + MySQL) seperti yang sudah pernah dibangun sebelumnya.

## 🖼️ Gambar Produk Seed

12 produk contoh (seed data) memakai gambar lokal di folder `img/produk/` — bukan lagi placeholder online — sehingga situs tetap tampil normal walau offline. Setiap gambar dibuat dari satu foto dasar dengan label kategori, watermark brand, dan nama produk yang ditambahkan otomatis.

Tersedia script `tools/generate_images.py` untuk membuat ulang gambar-gambar ini dengan foto dasar yang berbeda:
1. Ganti file `tools/source-photo.png` dengan foto Anda sendiri (nama file harus sama).
2. Install Pillow: `pip install Pillow`
3. Jalankan: `cd tools && python3 generate_images.py`
4. Gambar baru akan menimpa file di `img/produk/`.

Produk yang ditambahkan lewat form admin (Tambah Produk) tetap mendukung upload gambar asli langsung dari perangkat admin (disimpan sebagai base64, maks. 2MB, format JPG/PNG/WEBP).

## 🔒 Keamanan (disesuaikan untuk lingkungan statis)

- Password admin di-hash menggunakan SHA-256 (Web Crypto API) sebagai simulasi hashing.
- Sesi admin otomatis berakhir (session timeout) setelah 30 menit tanpa aktivitas.
- Upload gambar produk divalidasi: maksimal 2MB, format JPG/PNG/WEBP.
- **Catatan penting:** karena seluruh logika berjalan di sisi klien (browser), proteksi ini bersifat simulasi untuk keperluan demo/tugas — bukan pengganti keamanan backend sungguhan. Siapa pun yang mengetahui cara kerja localStorage secara teknis berpotensi memanipulasi data di browser-nya sendiri (tidak memengaruhi pengguna lain).

## 🛠️ Fitur yang Sudah Diimplementasikan

- ✅ Login & logout admin dengan validasi & session timeout
- ✅ CRUD produk (insert, edit, hapus) dengan upload gambar & validasi
- ✅ Insert & hapus kategori (dengan proteksi agar kategori yang masih dipakai produk tidak bisa dihapus)
- ✅ Katalog produk dengan pencarian kata kunci
- ✅ Filter kategori
- ✅ Pagination (9 produk/halaman di katalog publik, 8 baris/halaman di admin)
- ✅ Halaman detail produk dengan galeri gambar & produk terkait
- ✅ Tombol pesan via WhatsApp dengan pesan otomatis berisi nama & harga produk
- ✅ Dashboard admin dengan statistik & tabel produk terbaru

## 💡 Pengembangan Lanjutan (opsional)

- Menghubungkan ke backend nyata (PHP/MySQL atau Firebase) agar data konsisten lintas perangkat.
- Menambahkan upload gambar ke layanan penyimpanan (misalnya Cloudinary) agar tidak membebani localStorage.
