/* =========================================================
   SiKalog UMKM - detail.js
   Menampilkan detail satu produk berdasarkan parameter ?id=
   di URL, termasuk galeri gambar, tombol pesan WhatsApp
   dengan pesan otomatis, dan produk terkait.
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  const produk = id ? getProdukById(id) : null;
  const container = document.getElementById("detailContent");

  if (!produk) {
    container.innerHTML = `
      <div class="empty-state" style="grid-column:1/-1;">
        Produk tidak ditemukan. <a href="katalog.html">Kembali ke Katalog</a>
      </div>`;
    document.getElementById("relatedGrid").innerHTML = "";
    return;
  }

  document.title = produk.nama_produk + " | SiKalog UMKM";
  document.getElementById("breadcrumbNama").textContent = produk.nama_produk;

  const kategori = getKategoriById(produk.id_kategori);
  const gambarList = Array.isArray(produk.gambar) ? produk.gambar : [produk.gambar];

  container.innerHTML = `
    <div>
      <div class="detail-main-img"><img id="mainImg" src="${gambarList[0]}" alt="${escapeHtml(produk.nama_produk)}" /></div>
      <div class="thumb-row" id="thumbRow">
        ${gambarList
          .map(
            (g, i) => `<div class="thumb ${i === 0 ? "active" : ""}" data-src="${g}"><img src="${g}" alt="thumbnail ${i + 1}" /></div>`
          )
          .join("")}
      </div>
    </div>
    <div class="detail-info">
      <span class="card-cat">${kategori ? escapeHtml(kategori.nama_kategori) : "-"}</span>
      <h1>${escapeHtml(produk.nama_produk)}</h1>
      <div class="detail-price">${formatRupiah(produk.harga)}</div>
      <p class="detail-desc">${escapeHtml(produk.deskripsi)}</p>
      <p class="stock-badge">Stok tersedia: ${produk.stok}</p>
      <a class="btn btn-wa" id="btnWA" href="#" target="_blank" rel="noopener">&#128241; Pesan via WhatsApp</a>
    </div>
  `;

  // Galeri thumbnail
  document.querySelectorAll("#thumbRow .thumb").forEach((thumb) => {
    thumb.addEventListener("click", () => {
      document.getElementById("mainImg").src = thumb.dataset.src;
      document.querySelectorAll("#thumbRow .thumb").forEach((t) => t.classList.remove("active"));
      thumb.classList.add("active");
    });
  });

  // Tombol WhatsApp dengan pesan otomatis
  const pesan = `Halo, saya tertarik memesan produk "${produk.nama_produk}" seharga ${formatRupiah(produk.harga)}. Apakah masih tersedia?`;
  document.getElementById("btnWA").href = buildWhatsAppLink(pesan);

  // Produk terkait
  const terkait = getProdukTerkait(produk.id_produk, produk.id_kategori, 4);
  const relatedGrid = document.getElementById("relatedGrid");
  relatedGrid.innerHTML = terkait.length
    ? terkait.map(renderProductCard).join("")
    : `<p style="color:var(--muted);">Belum ada produk lain di kategori ini.</p>`;
});
