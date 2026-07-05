/* =========================================================
   SiKalog UMKM - katalog.js
   Menangani pencarian produk berdasarkan kata kunci, filter
   kategori, dan pagination (9 item per halaman).
   ========================================================= */

const ITEMS_PER_PAGE = 9;
let state = {
  keyword: "",
  kategori: "ALL",
  page: 1,
};

document.addEventListener("DOMContentLoaded", () => {
  renderCategoryFilters();
  renderProducts();

  document.getElementById("searchBtn").addEventListener("click", doSearch);
  document.getElementById("searchInput").addEventListener("keydown", (e) => {
    if (e.key === "Enter") doSearch();
  });
});

function doSearch() {
  state.keyword = document.getElementById("searchInput").value;
  state.page = 1;
  renderProducts();
}

function renderCategoryFilters() {
  const kategoriList = getAllKategori();
  const container = document.getElementById("categoryFilters");

  let html = `<button class="filter-btn ${state.kategori === "ALL" ? "active" : ""}" data-id="ALL">Semua Produk</button>`;
  kategoriList.forEach((k) => {
    html += `<button class="filter-btn ${state.kategori === k.id_kategori ? "active" : ""}" data-id="${k.id_kategori}">${escapeHtml(k.nama_kategori)}</button>`;
  });
  container.innerHTML = html;

  container.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      state.kategori = btn.dataset.id;
      state.page = 1;
      renderCategoryFilters();
      renderProducts();
    });
  });
}

function renderProducts() {
  const results = searchProduk(state.keyword, state.kategori);
  const grid = document.getElementById("productGrid");
  const emptyState = document.getElementById("emptyState");
  const totalPages = Math.max(1, Math.ceil(results.length / ITEMS_PER_PAGE));

  if (state.page > totalPages) state.page = totalPages;

  const start = (state.page - 1) * ITEMS_PER_PAGE;
  const pageItems = results.slice(start, start + ITEMS_PER_PAGE);

  if (pageItems.length === 0) {
    grid.innerHTML = "";
    emptyState.style.display = "block";
  } else {
    emptyState.style.display = "none";
    grid.innerHTML = pageItems.map(renderProductCard).join("");
  }

  renderPagination(totalPages);
}

function renderPagination(totalPages) {
  const container = document.getElementById("pagination");
  if (totalPages <= 1) {
    container.innerHTML = "";
    return;
  }

  let html = `<button ${state.page === 1 ? "disabled" : ""} data-action="prev">&lt; Previous</button>`;
  for (let i = 1; i <= totalPages; i++) {
    html += `<button class="${i === state.page ? "active" : ""}" data-page="${i}">${i}</button>`;
  }
  html += `<button ${state.page === totalPages ? "disabled" : ""} data-action="next">Next &gt;</button>`;
  container.innerHTML = html;

  container.querySelectorAll("button[data-page]").forEach((btn) => {
    btn.addEventListener("click", () => {
      state.page = Number(btn.dataset.page);
      renderProducts();
      window.scrollTo({ top: document.querySelector(".katalog-layout").offsetTop - 100, behavior: "smooth" });
    });
  });
  const prevBtn = container.querySelector('[data-action="prev"]');
  if (prevBtn) prevBtn.addEventListener("click", () => { state.page--; renderProducts(); });
  const nextBtn = container.querySelector('[data-action="next"]');
  if (nextBtn) nextBtn.addEventListener("click", () => { state.page++; renderProducts(); });
}
