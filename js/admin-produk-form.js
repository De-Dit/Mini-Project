/* =========================================================
   SiKalog UMKM - admin-produk-form.js
   Menangani form tambah & edit produk, termasuk validasi
   upload gambar (maks 2MB, format JPG/PNG/WEBP) yang disimpan
   sebagai base64 data-URL di localStorage (simulasi upload
   karena tidak ada server penyimpanan file pada GitHub Pages).
   ========================================================= */

const MAX_IMAGE_SIZE = 2 * 1024 * 1024; // 2 MB
const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/webp"];

let editingId = null;
let currentImages = [];

document.addEventListener("DOMContentLoaded", () => {
  const session = requireAuth();
  if (!session) return;

  populateKategoriOptions();

  const params = new URLSearchParams(window.location.search);
  editingId = params.get("id");

  if (editingId) {
    const produk = getProdukById(editingId);
    if (!produk) {
      alert("Produk tidak ditemukan.");
      window.location.href = "admin-produk.html";
      return;
    }
    document.getElementById("formTitle").textContent = "Edit Produk";
    document.getElementById("nama_produk").value = produk.nama_produk;
    document.getElementById("id_kategori").value = produk.id_kategori;
    document.getElementById("harga").value = produk.harga;
    document.getElementById("stok").value = produk.stok;
    document.getElementById("deskripsi").value = produk.deskripsi;
    document.getElementById("is_active").checked = !!produk.is_active;
    currentImages = Array.isArray(produk.gambar) ? produk.gambar : [produk.gambar];
    updateImagePreview();
  }

  document.getElementById("gambar").addEventListener("change", handleImageUpload);
  document.getElementById("produkForm").addEventListener("submit", handleSubmit);

  document.getElementById("logoutBtn").addEventListener("click", (e) => {
    e.preventDefault();
    if (confirm("Yakin ingin logout dari sistem?")) logoutAdmin();
  });
});

function populateKategoriOptions() {
  const select = document.getElementById("id_kategori");
  const kategoriList = getAllKategori();
  if (kategoriList.length === 0) {
    select.innerHTML = `<option value="">Belum ada kategori — tambahkan dahulu</option>`;
    return;
  }
  select.innerHTML = kategoriList
    .map((k) => `<option value="${k.id_kategori}">${escapeHtml(k.nama_kategori)}</option>`)
    .join("");
}

function updateImagePreview() {
  const preview = document.getElementById("imgPreview");
  if (currentImages.length > 0) {
    preview.innerHTML = `<img src="${currentImages[0]}" alt="preview" />`;
  } else {
    preview.innerHTML = "Belum ada gambar";
  }
}

function handleImageUpload(e) {
  const file = e.target.files[0];
  const errorEl = document.getElementById("formError");
  errorEl.textContent = "";

  if (!file) return;

  if (!ALLOWED_TYPES.includes(file.type)) {
    errorEl.textContent = "Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.";
    e.target.value = "";
    return;
  }
  if (file.size > MAX_IMAGE_SIZE) {
    errorEl.textContent = "Ukuran file melebihi 2 MB. Silakan pilih gambar lain.";
    e.target.value = "";
    return;
  }

  const reader = new FileReader();
  reader.onload = () => {
    currentImages = [reader.result];
    updateImagePreview();
  };
  reader.readAsDataURL(file);
}

function handleSubmit(e) {
  e.preventDefault();
  const errorEl = document.getElementById("formError");
  errorEl.textContent = "";

  const nama_produk = document.getElementById("nama_produk").value.trim();
  const id_kategori = document.getElementById("id_kategori").value;
  const harga = document.getElementById("harga").value;
  const stok = document.getElementById("stok").value;
  const deskripsi = document.getElementById("deskripsi").value.trim();
  const is_active = document.getElementById("is_active").checked;

  if (!nama_produk || !id_kategori || harga === "" || stok === "" || !deskripsi) {
    errorEl.textContent = "Semua field wajib diisi.";
    return;
  }
  if (Number(harga) < 0 || Number(stok) < 0) {
    errorEl.textContent = "Harga dan stok tidak boleh bernilai negatif.";
    return;
  }
  if (currentImages.length === 0) {
    errorEl.textContent = "Silakan unggah gambar produk.";
    return;
  }

  const payload = { nama_produk, id_kategori, harga, stok, deskripsi, is_active, gambar: currentImages };

  if (editingId) {
    updateProduk(editingId, payload);
  } else {
    insertProduk(payload);
  }

  window.location.href = "admin-produk.html";
}
