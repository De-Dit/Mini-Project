# SiKalog UMKM — Versi Statis (GitHub Pages)

Ini adalah versi **statis** (HTML/CSS/JS murni) dari project SiKalog UMKM
yang aslinya dibangun dengan PHP + MySQL. Karena **GitHub Pages hanya bisa
menghosting file statis** (tidak bisa menjalankan PHP atau database),
seluruh halaman publik (Beranda, Katalog, Detail Produk) sudah di-generate
menjadi file `.html` biasa, dengan data 10 produk contoh ditanam langsung
di dalam HTML.

## Apa yang berubah dari versi PHP asli

| Fitur (versi PHP)                         | Versi statis (HTML)                                   |
|--------------------------------------------|--------------------------------------------------------|
| `index.php`                                 | `index.html`                                           |
| `katalog.php` (query DB + pagination)       | `katalog.html` (data 10 produk ditanam, filter kategori & pencarian berjalan di browser lewat JavaScript, tanpa pagination karena datanya sedikit) |
| `detail.php?id=1`                           | `detail-1.html`, `detail-2.html`, dst (1 file per produk) |
| Folder `admin/` (dashboard, login, CRUD)    | **Tidak disertakan** — panel admin butuh PHP + MySQL (server backend) yang tidak bisa jalan di GitHub Pages |
| `config/database.php`, koneksi MySQL        | **Tidak dipakai lagi** — semua data produk sudah ditanam langsung sebagai HTML |

Jika Anda ingin menambah/mengubah/menghapus produk di masa depan, Anda perlu
mengedit file HTML terkait secara manual (tidak ada lagi panel admin di versi
statis ini), atau menghubungkan ulang ke backend PHP+MySQL kalau ingin hosting
di server yang mendukung PHP (misalnya Hostinger, Niagahoster, dll — bukan
GitHub Pages).

## Struktur folder

```
├── index.html          → Beranda
├── katalog.html         → Katalog produk (search & filter kategori via JS)
├── detail-1.html … detail-10.html  → Halaman detail tiap produk
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── img/placeholder.png
└── uploads/              → Foto-foto produk
```

## Cara publish ke GitHub Pages

1. Buat repository baru di GitHub (boleh public atau private, tapi GitHub
   Pages gratis mensyaratkan public untuk akun free tier).
2. Upload/push seluruh isi folder ini ke root repository tersebut
   (atau ke folder `/docs` — sesuaikan setting Pages di langkah 4).

   ```bash
   git init
   git add .
   git commit -m "Deploy SiKalog UMKM versi statis"
   git branch -M main
   git remote add origin https://github.com/USERNAME/NAMA-REPO.git
   git push -u origin main
   ```

3. Buka repository di GitHub → tab **Settings** → menu **Pages** (di sidebar
   kiri, bagian "Code and automation").
4. Di bagian **Build and deployment**:
   - Source: **Deploy from a branch**
   - Branch: pilih `main`, folder `/ (root)` → klik **Save**.
5. Tunggu 1–2 menit, GitHub akan memberi tahu URL situs Anda, biasanya:
   `https://USERNAME.github.io/NAMA-REPO/`
6. Buka URL tersebut — situs SiKalog UMKM versi statis sudah live.

## Catatan tambahan

- Nomor WhatsApp admin (`628123456789`) masih hardcode di semua halaman.
  Ganti dengan nomor asli dengan cari-ganti (`Ctrl+F` / `find & sed`) di semua
  file HTML sebelum publish, contoh:

  ```bash
  grep -rl "628123456789" . | xargs sed -i 's/628123456789/62812XXXXXXX/g'
  ```

- Karena tidak ada database, menambah produk baru berarti menambahkan blok
  HTML baru secara manual (bisa dicontoh dari struktur `detail-*.html` yang
  sudah ada), atau meminta bantuan untuk generate ulang skrip Python
  (`build_site.py` yang dipakai untuk membuat situs ini) dengan data produk
  baru.
