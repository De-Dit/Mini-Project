#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Generator situs statis SiKalog UMKM dari data yang sebelumnya ada di MySQL,
supaya bisa di-hosting di GitHub Pages (tanpa PHP / database).
"""
import os, html, json

OUT = "/home/claude/site-static"
WA_NUMBER = "628123456789"

kategori = {
    1: "Kerajinan Tangan",
    2: "Makanan & Minuman",
    3: "Pakaian & Fashion",
    4: "Pertanian",
    5: "Perikanan",
}

# urutan insert asli (created_at ASC), id_produk 1..10
produk_insert_order = [
    dict(id=1, nama="Batik Tulis Bali Motif Barong",
         deskripsi="Kain batik tulis berkualitas tinggi dengan motif Barong khas Bali. Dibuat dengan teknik tradisional menggunakan pewarna alami.",
         harga=250000, stok=15, gambar="batik_barong.jpg", kat=1),
    dict(id=2, nama="Krispi Tempe Original",
         deskripsi="Cemilan tempe krispy yang gurih dan renyah. Dibuat dari kedelai pilihan tanpa pengawet dan tanpa MSG. Kemasan 200gr.",
         harga=25000, stok=50, gambar="krispi_tempe.jpg", kat=2),
    dict(id=3, nama="Tas Anyaman Pandan",
         deskripsi="Tas cantik berbahan anyaman daun pandan asli. Dikerjakan tangan oleh pengrajin lokal Bali. Ukuran 30x25x10 cm.",
         harga=85000, stok=20, gambar="tas_pandan.jpg", kat=1),
    dict(id=4, nama="Sambal Matah Bali",
         deskripsi="Sambal matah khas Bali dengan cita rasa pedas segar. Terbuat dari bahan-bahan segar pilihan. Kemasan 150gr.",
         harga=20000, stok=100, gambar="sambal_matah.jpg", kat=2),
    dict(id=5, nama="Gelang Manik Tradisional",
         deskripsi="Gelang manik-manik buatan tangan dengan motif tradisional Bali. Bahan manik kaca berkualitas dengan benang elastis kuat.",
         harga=35000, stok=40, gambar="gelang_manik.jpg", kat=1),
    dict(id=6, nama="Kopi Robusta Kintamani",
         deskripsi="Kopi Robusta pilihan dari perkebunan dataran tinggi Kintamani, Bali. Aroma khas dengan rasa sedikit pahit dan body kuat.",
         harga=55000, stok=30, gambar="kopi_kintamani.jpg", kat=4),
    dict(id=7, nama="Ikan Tuna Asap Jimbaran",
         deskripsi="Ikan tuna asap hasil tangkapan nelayan lokal Jimbaran. Diasap dengan kayu pilihan menggunakan metode tradisional.",
         harga=65000, stok=25, gambar="tuna_asap.jpg", kat=5),
    dict(id=8, nama="Lukisan Kanvas Pemandangan Bali",
         deskripsi="Lukisan pemandangan Bali di atas kanvas ukuran 40x60 cm. Dibuat oleh seniman lokal Ubud menggunakan cat akrilik berkualitas.",
         harga=350000, stok=5, gambar="lukisan_bali.jpg", kat=1),
    dict(id=9, nama="Pie Susu Bali Original",
         deskripsi="Kue pie susu khas Bali yang terkenal dengan tekstur lembut dan manis. Isi 20 pcs per kotak.",
         harga=45000, stok=60, gambar="pie_susu.jpg", kat=2),
    dict(id=10, nama="Sarung Tenun Gringsing",
         deskripsi="Sarung tenun double ikat khas Tenganan, Bali. Proses pembuatan memerlukan waktu bertahun-tahun dengan teknik tradisional.",
         harga=500000, stok=8, gambar="tenun_gringsing.jpg", kat=3),
]

# ORDER BY created_at DESC -> kebalikan urutan insert
produk_all = list(reversed(produk_insert_order))
for p in produk_all:
    p["kategori_nama"] = kategori[p["kat"]]

def rupiah(n):
    return "Rp " + format(int(n), ",").replace(",", ".")

def h(s):
    return html.escape(str(s), quote=True)

def wa_link(nama, harga):
    teks = f"Halo, saya tertarik dengan produk *{nama}* ({rupiah(harga)}). Apakah produk ini masih tersedia?"
    from urllib.parse import quote
    return f"https://wa.me/{WA_NUMBER}?text={quote(teks)}"

HEAD = """<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{title}</title>
<link rel="stylesheet" href="{root}assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="{root}index.html" class="logo-box">
      <span class="logo-mark">SK</span>
      <span>SiKalog UMKM</span>
    </a>

    <button class="nav-toggle" aria-label="Buka menu">&#9776;</button>

    <nav class="main-nav">
      <a href="{root}index.html" class="{beranda_active}">BERANDA</a>
      <a href="{root}katalog.html" class="{katalog_active}">KATALOG</a>
      <a href="{root}index.html#tentang-kami">TENTANG KAMI</a>
      <a href="{root}index.html#kontak">KONTAK</a>
    </nav>
  </div>
</header>
"""

FOOT = """
<footer class="site-footer" id="kontak">
  <div class="container">
    <span>&copy; 2026 SiKalog UMKM. Hak Cipta Dilindungi.</span>
    <div class="footer-social">
      <a href="https://instagram.com/" target="_blank" rel="noopener">Instagram</a>
      <a href="https://facebook.com/" target="_blank" rel="noopener">Facebook</a>
    </div>
    <a class="footer-wa" href="https://wa.me/{wa}" target="_blank" rel="noopener">
      <svg viewBox="0 0 24 24"><path d="M17.6 6.3A8.86 8.86 0 0 0 12.04 4 8.94 8.94 0 0 0 4 17.9L3 21l3.2-.9a8.9 8.9 0 0 0 5.8 2.1A8.94 8.94 0 0 0 12.04 4a8.86 8.86 0 0 0 5.56 2.3zM12.04 20.4a7.4 7.4 0 0 1-3.8-1l-.27-.16-2.85.75.76-2.78-.18-.28a7.5 7.5 0 1 1 13.9-3.94 7.4 7.4 0 0 1-7.56 7.4z"/></svg>
      {wa}
    </a>
  </div>
</footer>

<script src="{root}assets/js/script.js"></script>
</body>
</html>
"""

def gambar_url(root, nama_file):
    if nama_file:
        return f"{root}uploads/{nama_file}"
    return f"{root}assets/img/placeholder.png"

def product_card(p, root):
    return f"""
          <div class="product-card">
            <a class="thumb" href="{root}detail-{p['id']}.html">
              <img src="{gambar_url(root, p['gambar'])}" alt="{h(p['nama'])}" loading="lazy">
            </a>
            <div class="body">
              <div class="pname"><a href="{root}detail-{p['id']}.html">{h(p['nama'])}</a></div>
              <div class="pprice">{rupiah(p['harga'])}</div>
              <a class="btn-detail" href="{root}detail-{p['id']}.html">Lihat Detail</a>
            </div>
          </div>"""

# ---------------------------------------------------------------
# index.html
# ---------------------------------------------------------------
unggulan = produk_all[:6]
cards = "\n".join(product_card(p, "") for p in unggulan)

index_html = HEAD.format(title="SiKalog UMKM — Katalog Produk UMKM Online", root="",
                          beranda_active="active", katalog_active="") + f"""
<!-- ============ HERO SECTION ============ -->
<section class="hero">
  <div class="container">
    <h1>Temukan Produk UMKM Terbaik di Sekitar Anda</h1>
    <p>SiKalog UMKM membantu Anda menjelajahi produk kerajinan, makanan, dan fashion lokal langsung dari para pelaku usaha mikro, kecil, dan menengah.</p>
    <a href="katalog.html" class="btn-cta">LIHAT KATALOG</a>
  </div>
</section>

<!-- ============ PRODUK UNGGULAN ============ -->
<section class="section">
  <div class="container">
    <h2 class="section-title">Produk Unggulan</h2>

      <div class="product-grid">{cards}
      </div>
  </div>
</section>

<!-- ============ TENTANG KAMI ============ -->
<section class="section" id="tentang-kami" style="padding-top:0;">
  <div class="container">
    <div class="about-box">
      <div>
        <h3>Tentang Kami</h3>
        <p>SiKalog UMKM adalah platform katalog digital yang membantu para pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) memamerkan produk mereka secara online. Kami percaya setiap produk lokal layak ditemukan oleh lebih banyak orang.</p>
      </div>
      <div class="about-meta">
        <div><strong>Lokasi</strong>Nusa Lembongan, Bali, Indonesia</div>
        <div><strong>Kontak</strong><a href="https://wa.me/{WA_NUMBER}" target="_blank" rel="noopener">+{WA_NUMBER} (WhatsApp)</a></div>
        <div><strong>Jam Operasional</strong>Setiap hari, 08.00 – 20.00 WITA</div>
      </div>
    </div>
  </div>
</section>
""" + FOOT.format(wa=WA_NUMBER, root="")

with open(os.path.join(OUT, "index.html"), "w", encoding="utf-8") as f:
    f.write(index_html)

# ---------------------------------------------------------------
# katalog.html (client-side search + filter kategori, tanpa pagination
#   karena datanya kecil semua produk ditampilkan sekaligus)
# ---------------------------------------------------------------
sidebar_links = '<a href="#" class="cat-link active" data-kat="">Semua Produk</a>\n'
for kid, kname in kategori.items():
    sidebar_links += f'        <a href="#" class="cat-link" data-kat="{kid}">{h(kname)}</a>\n'

grid_cards = ""
for p in produk_all:
    grid_cards += f"""
          <div class="product-card" data-kat="{p['kat']}" data-nama="{h(p['nama']).lower()}" data-deskripsi="{h(p['deskripsi']).lower()}">
            <a class="thumb" href="detail-{p['id']}.html">
              <img src="{gambar_url('', p['gambar'])}" alt="{h(p['nama'])}" loading="lazy">
            </a>
            <div class="body">
              <div class="pname"><a href="detail-{p['id']}.html">{h(p['nama'])}</a></div>
              <div class="pprice">{rupiah(p['harga'])}</div>
              <a class="btn-detail" href="detail-{p['id']}.html">Detail</a>
            </div>
          </div>"""

katalog_html = HEAD.format(title="Katalog Produk — SiKalog UMKM", root="",
                            beranda_active="", katalog_active="active") + f"""
<section class="section" style="padding-bottom:20px;">
  <div class="container">

    <div class="katalog-top">
      <h2 class="section-title" style="margin:0;text-align:left;">Katalog Produk</h2>
      <form class="search-form" id="search-form" onsubmit="return false;">
        <input type="text" id="search-input" placeholder="Cari produk...">
        <button type="submit">Cari</button>
      </form>
    </div>

    <p id="search-info" style="color:var(--text-muted);margin-top:-14px;display:none;"></p>

    <div class="katalog-layout">

      <!-- ===== SIDEBAR KATEGORI ===== -->
      <aside class="cat-sidebar">
        <h4>Kategori</h4>
        {sidebar_links.strip()}
      </aside>

      <!-- ===== GRID PRODUK ===== -->
      <div>
        <div class="product-grid" id="product-grid">{grid_cards}
        </div>
        <div class="empty-state" id="empty-state" style="display:none;">
          <div class="icon">&#128269;</div>
          <p>Produk tidak ditemukan. Coba kata kunci atau kategori lain.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {{
  var searchInput = document.getElementById('search-input');
  var catLinks = document.querySelectorAll('.cat-link');
  var cards = document.querySelectorAll('#product-grid .product-card');
  var emptyState = document.getElementById('empty-state');
  var searchInfo = document.getElementById('search-info');
  var activeKat = '';

  function applyFilter() {{
    var kw = searchInput.value.trim().toLowerCase();
    var visibleCount = 0;
    cards.forEach(function (card) {{
      var matchKat = !activeKat || card.getAttribute('data-kat') === activeKat;
      var matchKw = !kw || card.getAttribute('data-nama').indexOf(kw) !== -1 ||
                    card.getAttribute('data-deskripsi').indexOf(kw) !== -1;
      var show = matchKat && matchKw;
      card.style.display = show ? '' : 'none';
      if (show) visibleCount++;
    }});
    emptyState.style.display = visibleCount === 0 ? '' : 'none';
    if (kw) {{
      searchInfo.style.display = '';
      searchInfo.innerHTML = 'Menampilkan hasil pencarian untuk: <strong>"' + kw + '"</strong>';
    }} else {{
      searchInfo.style.display = 'none';
    }}
  }}

  searchInput.addEventListener('input', applyFilter);

  catLinks.forEach(function (link) {{
    link.addEventListener('click', function (e) {{
      e.preventDefault();
      catLinks.forEach(function (l) {{ l.classList.remove('active'); }});
      link.classList.add('active');
      activeKat = link.getAttribute('data-kat');
      applyFilter();
    }});
  }});
}});
</script>
""" + FOOT.format(wa=WA_NUMBER, root="")

with open(os.path.join(OUT, "katalog.html"), "w", encoding="utf-8") as f:
    f.write(katalog_html)

# ---------------------------------------------------------------
# detail-{id}.html
# ---------------------------------------------------------------
for p in produk_all:
    related = [r for r in produk_all if r["kat"] == p["kat"] and r["id"] != p["id"]][:3]
    if related:
        related_html = '\n    <h3 class="related-title">Produk Terkait</h3>\n    <div class="product-grid">' + \
            "".join(product_card(r, "") for r in related) + "\n    </div>"
    else:
        related_html = ""

    stok_html = (f"&#10003; Stok tersedia: {p['stok']} pcs" if p["stok"] > 0 else "&#10007; Stok habis")

    detail_html = HEAD.format(title=f"{h(p['nama'])} — SiKalog UMKM", root="",
                               beranda_active="", katalog_active="") + f"""
<section class="section">
  <div class="container">

    <a class="back-link" href="katalog.html">&larr; Kembali ke Katalog</a>

    <div class="detail-layout">

      <!-- ===== GALERI GAMBAR ===== -->
      <div class="detail-gallery">
        <div class="main-image">
          <img src="{gambar_url('', p['gambar'])}" alt="{h(p['nama'])}">
        </div>
        <div class="thumb-row">
          <img src="{gambar_url('', p['gambar'])}" class="active-thumb" alt="Thumbnail 1">
          <img src="{gambar_url('', p['gambar'])}" alt="Thumbnail 2">
          <img src="{gambar_url('', p['gambar'])}" alt="Thumbnail 3">
        </div>
      </div>

      <!-- ===== INFORMASI PRODUK ===== -->
      <div class="detail-info">
        <span class="kategori-label">{h(p['kategori_nama'])}</span>
        <h1>{h(p['nama'])}</h1>
        <div class="harga">{rupiah(p['harga'])}</div>

        <div class="deskripsi-box">
          {h(p['deskripsi'])}
        </div>

        <div class="stok-info">
          {stok_html}
        </div>

        <a class="btn-whatsapp" href="{wa_link(p['nama'], p['harga'])}" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24"><path d="M17.6 6.3A8.86 8.86 0 0 0 12.04 4 8.94 8.94 0 0 0 4 17.9L3 21l3.2-.9a8.9 8.9 0 0 0 5.8 2.1A8.94 8.94 0 0 0 12.04 4a8.86 8.86 0 0 0 5.56 2.3zM12.04 20.4a7.4 7.4 0 0 1-3.8-1l-.27-.16-2.85.75.76-2.78-.18-.28a7.5 7.5 0 1 1 13.9-3.94 7.4 7.4 0 0 1-7.56 7.4z"/></svg>
          Pesan via WhatsApp
        </a>
      </div>

    </div>
{related_html}
  </div>
</section>
""" + FOOT.format(wa=WA_NUMBER, root="")

    with open(os.path.join(OUT, f"detail-{p['id']}.html"), "w", encoding="utf-8") as f:
        f.write(detail_html)

print("Selesai. File dibuat:", len(produk_all) + 2, "halaman HTML")
