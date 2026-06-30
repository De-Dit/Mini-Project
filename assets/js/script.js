/* =====================================================================
   SIKALOG UMKM — SCRIPT UTAMA
   ===================================================================== */

// ── Toggle menu navigasi mobile ─────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  var toggleBtn = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');
  if (toggleBtn && nav) {
    toggleBtn.addEventListener('click', function () {
      nav.classList.toggle('open');
    });
  }

  // ── Toggle sidebar admin (mobile) ─────────────────────────────
  var adminToggle = document.querySelector('.admin-toggle');
  var adminSidebar = document.querySelector('.admin-sidebar');
  if (adminToggle && adminSidebar) {
    adminToggle.addEventListener('click', function () {
      adminSidebar.classList.toggle('open');
    });
  }

  // ── Gallery: klik thumbnail mengubah gambar utama produk ──────
  var thumbs = document.querySelectorAll('.thumb-row img');
  var mainImg = document.querySelector('.main-image img');
  if (thumbs.length && mainImg) {
    thumbs.forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        mainImg.setAttribute('src', thumb.getAttribute('src'));
        thumbs.forEach(function (t) { t.classList.remove('active-thumb'); });
        thumb.classList.add('active-thumb');
      });
    });
  }

  // ── Konfirmasi sebelum menghapus produk ────────────────────────
  var deleteForms = document.querySelectorAll('.form-hapus');
  deleteForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var nama = form.getAttribute('data-nama') || 'produk ini';
      if (!confirm('Hapus produk "' + nama + '"? Tindakan ini tidak dapat dibatalkan.')) {
        e.preventDefault();
      }
    });
  });

  var deleteKategoriForms = document.querySelectorAll('.form-hapus-kategori');
  deleteKategoriForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var nama = form.getAttribute('data-nama') || 'kategori ini';
      if (!confirm('Hapus kategori "' + nama + '"? Produk yang menggunakan kategori ini tidak akan terhapus.')) {
        e.preventDefault();
      }
    });
  });
});
