/* =========================================================
   SiKalog UMKM - main.js
   Fungsi bersama untuk halaman publik: toggle menu mobile,
   mengisi footer & info kontak dari UMKM_CONFIG, dan helper
   membuka link WhatsApp.
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const navToggle = document.getElementById("navToggle");
  const navLinks = document.getElementById("navLinks");
  if (navToggle && navLinks) {
    navToggle.addEventListener("click", () => navLinks.classList.toggle("open"));
  }

  const tahunEl = document.getElementById("tahun");
  if (tahunEl) tahunEl.textContent = new Date().getFullYear();

  const footerNama = document.getElementById("footerNama");
  if (footerNama) footerNama.textContent = UMKM_CONFIG.nama;

  const linkIG = document.getElementById("linkIG");
  if (linkIG) linkIG.href = UMKM_CONFIG.instagram;

  const linkFB = document.getElementById("linkFB");
  if (linkFB) linkFB.href = UMKM_CONFIG.facebook;

  const linkWA = document.getElementById("linkWA");
  if (linkWA) linkWA.href = buildWhatsAppLink("Halo, saya ingin bertanya tentang produk di " + UMKM_CONFIG.nama + ".");

  setupHiddenAdminAccess();
});

/* Akses tersembunyi ke halaman Login Admin: klik teks copyright di
   footer sebanyak 5 kali dalam rentang waktu singkat. Ditujukan agar
   pengunjung biasa tidak melihat tautan login di navigasi publik,
   namun admin tetap bisa masuk tanpa mengetik URL secara manual. */
function setupHiddenAdminAccess() {
  const trigger = document.getElementById("copyrightTrigger");
  if (!trigger) return;

  const REQUIRED_CLICKS = 5;
  const RESET_DELAY_MS = 2000;
  let clickCount = 0;
  let resetTimer = null;

  trigger.style.cursor = "pointer";
  trigger.style.userSelect = "none";

  trigger.addEventListener("click", () => {
    clickCount++;
    clearTimeout(resetTimer);

    if (clickCount >= REQUIRED_CLICKS) {
      clickCount = 0;
      window.location.href = "login.html";
      return;
    }

    resetTimer = setTimeout(() => {
      clickCount = 0;
    }, RESET_DELAY_MS);
  });
}

/* Membangun URL wa.me dengan pesan default */
function buildWhatsAppLink(message) {
  const nomor = UMKM_CONFIG.whatsapp.replace(/[^0-9]/g, "");
  return `https://wa.me/${nomor}?text=${encodeURIComponent(message)}`;
}

/* Membuat kartu produk (dipakai di beranda, katalog, produk terkait) */
function renderProductCard(produk) {
  const kategori = getKategoriById(produk.id_kategori);
  const gambar = Array.isArray(produk.gambar) ? produk.gambar[0] : produk.gambar;
  return `
    <div class="card">
      <a href="produk-detail.html?id=${produk.id_produk}">
        <div class="card-img"><img src="${gambar}" alt="${escapeHtml(produk.nama_produk)}" loading="lazy" /></div>
      </a>
      <div class="card-body">
        <span class="card-cat">${kategori ? escapeHtml(kategori.nama_kategori) : "-"}</span>
        <h3><a href="produk-detail.html?id=${produk.id_produk}">${escapeHtml(produk.nama_produk)}</a></h3>
        <div class="card-price">${formatRupiah(produk.harga)}</div>
        <a class="btn btn-outline btn-sm" href="produk-detail.html?id=${produk.id_produk}">Lihat Detail</a>
      </div>
    </div>
  `;
}
