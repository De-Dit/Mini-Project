/* =========================================================
   SiKalog UMKM - admin-produk.js
   Menampilkan seluruh data produk untuk dikelola admin:
   pencarian, hapus (dengan konfirmasi), dan pagination.
   ========================================================= */

const ADMIN_ITEMS_PER_PAGE = 8;
let adminState = { keyword: "", page: 1 };

document.addEventListener("DOMContentLoaded", () => {
  const session = requireAuth();
  if (!session) return;

  renderAdminProdukTable();

  document.getElementById("searchAdmin").addEventListener("input", (e) => {
    adminState.keyword = e.target.value;
    adminState.page = 1;
    renderAdminProdukTable();
  });

  document.getElementById("logoutBtn").addEventListener("click", (e) => {
    e.preventDefault();
    if (confirm("Yakin ingin logout dari sistem?")) logoutAdmin();
  });
});

function renderAdminProdukTable() {
  let list = getAllProduk();
  if (adminState.keyword.trim() !== "") {
    const kw = adminState.keyword.trim().toLowerCase();
    list = list.filter((p) => p.nama_produk.toLowerCase().includes(kw));
  }

  const totalPages = Math.max(1, Math.ceil(list.length / ADMIN_ITEMS_PER_PAGE));
  if (adminState.page > totalPages) adminState.page = totalPages;
  const start = (adminState.page - 1) * ADMIN_ITEMS_PER_PAGE;
  const pageItems = list.slice(start, start + ADMIN_ITEMS_PER_PAGE);

  const tbody = document.getElementById("produkBody");
  if (pageItems.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;color:var(--muted);">Tidak ada data produk.</td></tr>`;
  } else {
    tbody.innerHTML = pageItems
      .map((p) => {
        const kategori = getKategoriById(p.id_kategori);
        const gambar = Array.isArray(p.gambar) ? p.gambar[0] : p.gambar;
        return `
          <tr>
            <td><img class="table-thumb" src="${gambar}" alt="${escapeHtml(p.nama_produk)}" /></td>
            <td>${escapeHtml(p.nama_produk)}</td>
            <td>${kategori ? escapeHtml(kategori.nama_kategori) : "-"}</td>
            <td>${formatRupiah(p.harga)}</td>
            <td>${p.stok}</td>
            <td><span class="status-pill ${p.is_active ? "status-active" : "status-inactive"}">${p.is_active ? "Aktif" : "Nonaktif"}</span></td>
            <td class="action-btns">
              <a class="btn btn-sm btn-edit" href="admin-produk-form.html?id=${p.id_produk}">Edit</a>
              <button class="btn btn-sm btn-delete" onclick="hapusProdukAdmin('${p.id_produk}')">Hapus</button>
            </td>
          </tr>`;
      })
      .join("");
  }

  renderAdminPagination(totalPages);
}

function renderAdminPagination(totalPages) {
  const container = document.getElementById("pagination");
  if (totalPages <= 1) {
    container.innerHTML = "";
    return;
  }
  let html = `<button ${adminState.page === 1 ? "disabled" : ""} id="prevPage">&lt; Prev</button>`;
  for (let i = 1; i <= totalPages; i++) {
    html += `<button class="${i === adminState.page ? "active" : ""}" data-page="${i}">${i}</button>`;
  }
  html += `<button ${adminState.page === totalPages ? "disabled" : ""} id="nextPage">Next &gt;</button>`;
  container.innerHTML = html;

  container.querySelectorAll("button[data-page]").forEach((btn) => {
    btn.addEventListener("click", () => {
      adminState.page = Number(btn.dataset.page);
      renderAdminProdukTable();
    });
  });
  const prev = document.getElementById("prevPage");
  if (prev) prev.addEventListener("click", () => { adminState.page--; renderAdminProdukTable(); });
  const next = document.getElementById("nextPage");
  if (next) next.addEventListener("click", () => { adminState.page++; renderAdminProdukTable(); });
}

function hapusProdukAdmin(id) {
  if (confirm("Yakin ingin menghapus produk ini? Data yang dihapus tidak dapat dikembalikan.")) {
    deleteProduk(id);
    renderAdminProdukTable();
  }
}
