/* =========================================================
   SiKalog UMKM - admin-kategori.js
   Menangani insert & hapus data kategori produk. Kategori
   yang masih dipakai oleh produk tidak dapat dihapus untuk
   menjaga integritas relasi id_kategori (foreign key) pada
   tabel produk.
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const session = requireAuth();
  if (!session) return;

  renderKategoriTable();

  document.getElementById("kategoriForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const errorEl = document.getElementById("formError");
    errorEl.textContent = "";

    const nama_kategori = document.getElementById("nama_kategori").value.trim();
    const deskripsi = document.getElementById("deskripsi").value.trim();

    if (!nama_kategori) {
      errorEl.textContent = "Nama kategori wajib diisi.";
      return;
    }
    const duplikat = getAllKategori().some(
      (k) => k.nama_kategori.toLowerCase() === nama_kategori.toLowerCase()
    );
    if (duplikat) {
      errorEl.textContent = "Kategori dengan nama tersebut sudah ada.";
      return;
    }

    insertKategori({ nama_kategori, deskripsi });
    document.getElementById("kategoriForm").reset();
    showAlert("Kategori berhasil ditambahkan.", "success");
    renderKategoriTable();
  });

  document.getElementById("logoutBtn").addEventListener("click", (e) => {
    e.preventDefault();
    if (confirm("Yakin ingin logout dari sistem?")) logoutAdmin();
  });
});

function renderKategoriTable() {
  const list = getAllKategori();
  const semuaProduk = getAllProduk();
  const tbody = document.getElementById("kategoriBody");

  if (list.length === 0) {
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--muted);">Belum ada kategori.</td></tr>`;
    return;
  }

  tbody.innerHTML = list
    .map((k) => {
      const jumlah = semuaProduk.filter((p) => p.id_kategori === k.id_kategori).length;
      return `
        <tr>
          <td>${escapeHtml(k.nama_kategori)}</td>
          <td>${escapeHtml(k.deskripsi || "-")}</td>
          <td>${jumlah}</td>
          <td><button class="btn btn-sm btn-delete" onclick="hapusKategori('${k.id_kategori}')">Hapus</button></td>
        </tr>`;
    })
    .join("");
}

function hapusKategori(id) {
  if (!confirm("Yakin ingin menghapus kategori ini?")) return;
  const result = deleteKategori(id);
  if (!result.ok) {
    showAlert(result.message, "danger");
    return;
  }
  showAlert("Kategori berhasil dihapus.", "success");
  renderKategoriTable();
}

function showAlert(message, type) {
  const box = document.getElementById("alertBox");
  box.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
  setTimeout(() => (box.innerHTML = ""), 3500);
}
