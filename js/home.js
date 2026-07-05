/* =========================================================
   SiKalog UMKM - home.js
   Mengisi konten dinamis halaman Beranda: produk unggulan
   dan seksi Tentang Kami.
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("heroTitle").textContent = UMKM_CONFIG.slogan;
  document.getElementById("heroSlogan").textContent =
    "Temukan produk pilihan dari " + UMKM_CONFIG.nama + " — dari makanan ringan hingga kerajinan tangan, langsung dari tangan pelaku UMKM lokal.";

  document.getElementById("aboutDesc").textContent = UMKM_CONFIG.deskripsi;
  document.getElementById("aboutLokasi").textContent = UMKM_CONFIG.lokasi;
  document.getElementById("aboutEmail").textContent = UMKM_CONFIG.email;
  document.getElementById("aboutWA").textContent = "+" + UMKM_CONFIG.whatsapp;

  const featured = getProdukAktif().slice(0, 6);
  const grid = document.getElementById("featuredGrid");
  grid.innerHTML = featured.map(renderProductCard).join("");
});
