/* =========================================================
   SiKalog UMKM - data.js
   Simulasi database "SIKALOG UMKM" menggunakan localStorage
   karena GitHub Pages hanya melayani file statis (tanpa server
   PHP/MySQL). Tabel yang disimulasikan: Admin, Kategori, Produk.

   Catatan keamanan (lihat README): karena seluruhnya berjalan
   di sisi klien, ini adalah SIMULASI untuk keperluan demo/UAS,
   bukan implementasi keamanan produksi sungguhan.
   ========================================================= */

const UMKM_CONFIG = {
  nama: "SiKalog UMKM",
  slogan: "Belanja Produk Lokal, Kualitas Terjamin",
  deskripsi:
    "SiKalog UMKM adalah usaha rumahan yang memproduksi dan menjual berbagai produk lokal berkualitas, mulai dari makanan ringan, minuman, kerajinan tangan, hingga fashion. Kami berkomitmen menghadirkan produk terbaik langsung dari tangan pengrajin lokal.",
  lokasi: "Jl. Merdeka No. 45, Denpasar, Bali, Indonesia",
  email: "info@sikalogumkm.id",
  whatsapp: "6281234567890", // ganti dengan nomor WhatsApp asli pemilik UMKM
  instagram: "https://instagram.com/sikalogumkm",
  facebook: "https://facebook.com/sikalogumkm",
  sessionTimeoutMinutes: 30 // sesi admin otomatis berakhir setelah 30 menit tidak aktif
};

const DB_KEYS = {
  ADMIN: "sikalog_admin",
  KATEGORI: "sikalog_kategori",
  PRODUK: "sikalog_produk",
  SESSION: "sikalog_session"
};

/* ---------- Util umum ---------- */
function readDB(key) {
  const raw = localStorage.getItem(key);
  return raw ? JSON.parse(raw) : null;
}

function writeDB(key, value) {
  localStorage.setItem(key, JSON.stringify(value));
}

function generateId(prefix) {
  return prefix + "_" + Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
}

function formatRupiah(angka) {
  return "Rp " + Number(angka).toLocaleString("id-ID");
}

function formatTanggal(iso) {
  return new Date(iso).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" });
}

function escapeHtml(str) {
  if (str === undefined || str === null) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

/* Hash password sederhana (SHA-256) — simulasi penyimpanan password
   terenkripsi di sisi klien karena tidak ada backend/server pada
   deployment GitHub Pages. */
async function hashPassword(text) {
  const enc = new TextEncoder().encode(text);
  const buf = await crypto.subtle.digest("SHA-256", enc);
  return Array.from(new Uint8Array(buf)).map((b) => b.toString(16).padStart(2, "0")).join("");
}

function daysAgo(n) {
  const d = new Date();
  d.setDate(d.getDate() - n);
  return d.toISOString();
}

/* ---------- Seeding data awal (hanya berjalan sekali) ---------- */
async function seedDatabase() {
  if (!readDB(DB_KEYS.ADMIN)) {
    const hashed = await hashPassword("admin123");
    writeDB(DB_KEYS.ADMIN, [
      {
        id_admin: generateId("adm"),
        username: "admin",
        password: hashed,
        nama_admin: "Pemilik SiKalog UMKM",
        created_at: new Date().toISOString()
      }
    ]);
  }

  if (!readDB(DB_KEYS.KATEGORI)) {
    writeDB(DB_KEYS.KATEGORI, [
      { id_kategori: "kat_1", nama_kategori: "Makanan Ringan", deskripsi: "Camilan dan makanan ringan khas lokal", created_at: daysAgo(60) },
      { id_kategori: "kat_2", nama_kategori: "Minuman", deskripsi: "Minuman kemasan dan olahan tradisional", created_at: daysAgo(60) },
      { id_kategori: "kat_3", nama_kategori: "Kerajinan Tangan", deskripsi: "Produk kerajinan buatan tangan pengrajin lokal", created_at: daysAgo(60) },
      { id_kategori: "kat_4", nama_kategori: "Fashion", deskripsi: "Pakaian dan aksesoris fashion lokal", created_at: daysAgo(60) }
    ]);
  }

  if (!readDB(DB_KEYS.PRODUK)) {
    // Gambar produk lokal (file statis di folder img/produk/), dipakai berulang
    // sebagai galeri karena hanya tersedia satu foto dasar per produk.
    const g = (file) => [`img/produk/${file}`, `img/produk/${file}`, `img/produk/${file}`];

    writeDB(DB_KEYS.PRODUK, [
      { id_produk: generateId("prd"), nama_produk: "Keripik Singkong Balado", deskripsi: "Keripik singkong renyah dengan bumbu balado pedas manis, diolah dari singkong pilihan tanpa bahan pengawet.", harga: 15000, stok: 50, gambar: g("keripik-singkong-balado.jpg"), is_active: true, id_kategori: "kat_1", created_at: daysAgo(30), update_at: daysAgo(30) },
      { id_produk: generateId("prd"), nama_produk: "Kue Kering Nastar", deskripsi: "Kue kering nastar isi selai nanas asli, cocok untuk oleh-oleh atau hidangan hari raya.", harga: 45000, stok: 30, gambar: g("kue-kering-nastar.jpg"), is_active: true, id_kategori: "kat_1", created_at: daysAgo(28), update_at: daysAgo(28) },
      { id_produk: generateId("prd"), nama_produk: "Emping Melinjo Original", deskripsi: "Emping melinjo tipis dan gurih, digoreng segar tanpa MSG berlebih.", harga: 20000, stok: 40, gambar: g("emping-melinjo-original.jpg"), is_active: true, id_kategori: "kat_1", created_at: daysAgo(25), update_at: daysAgo(25) },
      { id_produk: generateId("prd"), nama_produk: "Kopi Robusta Bubuk 200gr", deskripsi: "Kopi robusta asli hasil panen petani lokal, disangrai medium untuk cita rasa yang kuat dan aromatik.", harga: 35000, stok: 60, gambar: g("kopi-robusta-bubuk-200gr.jpg"), is_active: true, id_kategori: "kat_2", created_at: daysAgo(20), update_at: daysAgo(20) },
      { id_produk: generateId("prd"), nama_produk: "Teh Herbal Rempah", deskripsi: "Teh herbal campuran rempah alami, baik untuk menghangatkan tubuh dan menjaga stamina.", harga: 28000, stok: 45, gambar: g("teh-herbal-rempah.jpg"), is_active: true, id_kategori: "kat_2", created_at: daysAgo(18), update_at: daysAgo(18) },
      { id_produk: generateId("prd"), nama_produk: "Sirup Markisa 500ml", deskripsi: "Sirup markisa asli tanpa pemanis buatan, segar dan cocok untuk campuran es atau minuman dingin.", harga: 32000, stok: 25, gambar: g("sirup-markisa-500ml.jpg"), is_active: true, id_kategori: "kat_2", created_at: daysAgo(15), update_at: daysAgo(15) },
      { id_produk: generateId("prd"), nama_produk: "Tas Anyaman Rotan", deskripsi: "Tas anyaman rotan handmade dengan motif khas, cocok untuk kegiatan sehari-hari maupun santai.", harga: 120000, stok: 15, gambar: g("tas-anyaman-rotan.jpg"), is_active: true, id_kategori: "kat_3", created_at: daysAgo(12), update_at: daysAgo(12) },
      { id_produk: generateId("prd"), nama_produk: "Gantungan Kunci Kayu Ukir", deskripsi: "Gantungan kunci dari kayu jati dengan ukiran khas daerah, cocok untuk oleh-oleh atau suvenir.", harga: 10000, stok: 100, gambar: g("gantungan-kunci-kayu-ukir.jpg"), is_active: true, id_kategori: "kat_3", created_at: daysAgo(10), update_at: daysAgo(10) },
      { id_produk: generateId("prd"), nama_produk: "Anyaman Tikar Pandan", deskripsi: "Tikar dari daun pandan yang dianyam rapi, adem dan nyaman digunakan untuk bersantai.", harga: 85000, stok: 20, gambar: g("anyaman-tikar-pandan.jpg"), is_active: true, id_kategori: "kat_3", created_at: daysAgo(9), update_at: daysAgo(9) },
      { id_produk: generateId("prd"), nama_produk: "Kemeja Batik Pria Lengan Panjang", deskripsi: "Kemeja batik motif klasik dengan bahan katun adem, tersedia berbagai ukuran.", harga: 175000, stok: 18, gambar: g("kemeja-batik-pria-lengan-panjang.jpg"), is_active: true, id_kategori: "kat_4", created_at: daysAgo(7), update_at: daysAgo(7) },
      { id_produk: generateId("prd"), nama_produk: "Tote Bag Kanvas Motif Lokal", deskripsi: "Tote bag berbahan kanvas tebal dengan sablon motif budaya lokal, kuat dan ramah lingkungan.", harga: 55000, stok: 35, gambar: g("tote-bag-kanvas-motif-lokal.jpg"), is_active: true, id_kategori: "kat_4", created_at: daysAgo(5), update_at: daysAgo(5) },
      { id_produk: generateId("prd"), nama_produk: "Selendang Tenun Ikat", deskripsi: "Selendang tenun ikat tradisional, ditenun manual oleh pengrajin dengan pewarna alami.", harga: 150000, stok: 12, gambar: g("selendang-tenun-ikat.jpg"), is_active: true, id_kategori: "kat_4", created_at: daysAgo(3), update_at: daysAgo(3) }
    ]);
  }
}

/* ---------- Admin ---------- */
function getAdminByUsername(username) {
  return (readDB(DB_KEYS.ADMIN) || []).find((a) => a.username === username);
}

/* ---------- Kategori ---------- */
function getAllKategori() {
  return readDB(DB_KEYS.KATEGORI) || [];
}

function getKategoriById(id) {
  return getAllKategori().find((k) => k.id_kategori === id);
}

function insertKategori({ nama_kategori, deskripsi }) {
  const list = getAllKategori();
  list.push({
    id_kategori: generateId("kat"),
    nama_kategori,
    deskripsi: deskripsi || "",
    created_at: new Date().toISOString()
  });
  writeDB(DB_KEYS.KATEGORI, list);
}

function deleteKategori(id) {
  const dipakai = getAllProduk().some((p) => p.id_kategori === id);
  if (dipakai) {
    return { ok: false, message: "Kategori tidak dapat dihapus karena masih digunakan oleh salah satu produk." };
  }
  const list = getAllKategori().filter((k) => k.id_kategori !== id);
  writeDB(DB_KEYS.KATEGORI, list);
  return { ok: true };
}

/* ---------- Produk ---------- */
function getAllProduk() {
  return readDB(DB_KEYS.PRODUK) || [];
}

function getProdukAktif() {
  return getAllProduk().filter((p) => p.is_active);
}

function getProdukById(id) {
  return getAllProduk().find((p) => p.id_produk === id);
}

function insertProduk({ nama_produk, id_kategori, harga, stok, deskripsi, is_active, gambar }) {
  const list = getAllProduk();
  const now = new Date().toISOString();
  list.push({
    id_produk: generateId("prd"),
    nama_produk,
    deskripsi,
    harga: Number(harga),
    stok: Number(stok),
    gambar,
    is_active: !!is_active,
    id_kategori,
    created_at: now,
    update_at: now
  });
  writeDB(DB_KEYS.PRODUK, list);
}

function updateProduk(id, { nama_produk, id_kategori, harga, stok, deskripsi, is_active, gambar }) {
  const list = getAllProduk();
  const idx = list.findIndex((p) => p.id_produk === id);
  if (idx === -1) return;
  list[idx] = {
    ...list[idx],
    nama_produk,
    deskripsi,
    harga: Number(harga),
    stok: Number(stok),
    gambar,
    is_active: !!is_active,
    id_kategori,
    update_at: new Date().toISOString()
  };
  writeDB(DB_KEYS.PRODUK, list);
}

function deleteProduk(id) {
  const list = getAllProduk().filter((p) => p.id_produk !== id);
  writeDB(DB_KEYS.PRODUK, list);
}

/* Pencarian produk berdasarkan kata kunci (nama/deskripsi) + filter kategori.
   Hanya menampilkan produk aktif ke pengunjung publik. */
function searchProduk(keyword, kategoriId) {
  let list = getProdukAktif();
  const kw = (keyword || "").trim().toLowerCase();
  if (kw !== "") {
    list = list.filter(
      (p) => p.nama_produk.toLowerCase().includes(kw) || (p.deskripsi || "").toLowerCase().includes(kw)
    );
  }
  if (kategoriId && kategoriId !== "ALL") {
    list = list.filter((p) => p.id_kategori === kategoriId);
  }
  return list;
}

/* Produk terkait: produk aktif lain dalam kategori yang sama */
function getProdukTerkait(excludeId, kategoriId, limit) {
  return getProdukAktif()
    .filter((p) => p.id_kategori === kategoriId && p.id_produk !== excludeId)
    .slice(0, limit);
}

/* Jalankan seeding otomatis setiap kali halaman dimuat (idempoten) */
seedDatabase();
