<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

// Ambil 6 produk unggulan (terbaru & aktif) untuk grid di Beranda
$stmt = $pdo->query("SELECT * FROM produk WHERE is_active = 1 ORDER BY created_at DESC LIMIT 6");
$produkUnggulan = $stmt->fetchAll();

$pageTitle = 'SiKalog UMKM — Katalog Produk UMKM Online';
$activeNav = 'beranda';
require __DIR__ . '/includes/header.php';
?>

<!-- ============ HERO SECTION ============ -->
<section class="hero">
  <div class="container">
    <h1>Temukan Produk UMKM Terbaik di Sekitar Anda</h1>
    <p>SiKalog UMKM membantu Anda menjelajahi produk kerajinan, makanan, dan fashion lokal langsung dari para pelaku usaha mikro, kecil, dan menengah.</p>
    <a href="<?= BASE_URL ?>/katalog.php" class="btn-cta">LIHAT KATALOG</a>
  </div>
</section>

<!-- ============ PRODUK UNGGULAN ============ -->
<section class="section">
  <div class="container">
    <h2 class="section-title">Produk Unggulan</h2>

    <?php if (count($produkUnggulan) === 0): ?>
      <div class="empty-state">
        <div class="icon">&#128230;</div>
        <p>Belum ada produk yang ditambahkan.</p>
      </div>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($produkUnggulan as $p): ?>
          <div class="product-card">
            <a class="thumb" href="<?= BASE_URL ?>/detail.php?id=<?= (int)$p['id_produk'] ?>">
              <img src="<?= gambarProduk($p['gambar']) ?>" alt="<?= h($p['nama_produk']) ?>" loading="lazy">
            </a>
            <div class="body">
              <div class="pname"><a href="<?= BASE_URL ?>/detail.php?id=<?= (int)$p['id_produk'] ?>"><?= h($p['nama_produk']) ?></a></div>
              <div class="pprice"><?= formatRupiah($p['harga']) ?></div>
              <a class="btn-detail" href="<?= BASE_URL ?>/detail.php?id=<?= (int)$p['id_produk'] ?>">Lihat Detail</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
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
        <div><strong>Kontak</strong><a href="https://wa.me/<?= WA_NUMBER ?>" target="_blank" rel="noopener">+<?= WA_NUMBER ?> (WhatsApp)</a></div>
        <div><strong>Jam Operasional</strong>Setiap hari, 08.00 – 20.00 WITA</div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
