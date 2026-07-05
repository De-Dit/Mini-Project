/* =========================================================
   SiKalog UMKM - dashboard.js
   Menampilkan statistik total produk & kategori, serta tabel
   5 produk terbaru beserta aksi edit/hapus.
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const session = requireAuth();
  if (!session) return;

  document.getElementById("adminName").textContent = "Halo, " + session.nama_admin;

  const semuaProduk = getAllProduk();
  const semuaKategori = getAllKategori();

  document.getElementById("statProduk").textContent = semuaProduk.length;
  document.getElementById("statKategori").textContent = semuaKategori.length;

  renderRecentTable();

  document.getElementById("logoutBtn").addEventListener("click", (e) => {
    e.preventDefault();
    if (confirm("Yakin ingin logout dari sistem?")) {
      logoutAdmin();
    }
  });
});

function renderRecentTable() {
  const produkTerbaru = [...getAllProduk()]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 5);

  const tbody = document.getElementById("recentProdukBody");

  if (produkTerbaru.length === 0) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--muted);">Belum ada data produk.</td></tr>`;
    return;
  }

  tbody.innerHTML = produkTerbaru
    .map((p) => {
      const kategori = getKategoriById(p.id_kategori);
      const gambar = Array.isArray(p.gambar) ? p.gambar[0] : p.gambar;
      return `
        <tr>
          <td><img class="table-thumb" src="${gambar}" alt="${escapeHtml(p.nama_produk)}" /></td>
          <td>${escapeHtml(p.nama_produk)}</td>
          <td>${kategori ? escapeHtml(kategori.nama_kategori) : "-"}</td>
          <td>${formatRupiah(p.harga)}</td>
          <td class="action-btns">
            <a class="btn btn-sm btn-edit" href="admin-produk-form.html?id=${p.id_produk}">Edit</a>
            <button class="btn btn-sm btn-delete" data-id="${p.id_produk}" onclick="hapusProdukDashboard('${p.id_produk}')">Hapus</button>
          </td>
        </tr>`;
    })
    .join("");
}

function hapusProdukDashboard(id) {
  if (confirm("Yakin ingin menghapus produk ini? Data yang dihapus tidak dapat dikembalikan.")) {
    deleteProduk(id);
    const semuaProduk = getAllProduk();
    document.getElementById("statProduk").textContent = semuaProduk.length;
    renderRecentTable();
  }
}
