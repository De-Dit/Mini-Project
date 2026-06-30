<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT p.*, k.nama_kategori
                        FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                        WHERE p.id_produk = :id AND p.is_active = 1");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    http_response_code(404);
    $pageTitle = 'Produk Tidak Ditemukan — SiKalog UMKM';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="empty-state"><div class="icon">&#10060;</div>
          <p>Produk yang Anda cari tidak ditemukan atau sudah tidak tersedia.</p>
          <p><a href="' . BASE_URL . '/katalog.php" class="btn-cta" style="margin-top:10px;">Kembali ke Katalog</a></p>
          </div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Kumpulkan gambar untuk galeri (utama + 2 tambahan jika ada, fallback ke gambar utama)
$galeri = array_filter([$produk['gambar'], $produk['gambar_2'], $produk['gambar_3']]);
if (empty($galeri)) $galeri = [null];
$galeri = array_values($galeri);
// Pastikan selalu ada 3 thumbnail (ulangi gambar utama jika kurang)
while (count($galeri) < 3) $galeri[] = $galeri[0];

// Produk terkait: kategori sama, kecuali produk ini sendiri
$related = [];
if ($produk['id_kategori']) {
    $stmtR = $pdo->prepare("SELECT * FROM produk
                             WHERE id_kategori = :kat AND id_produk != :id AND is_active = 1
                             ORDER BY created_at DESC LIMIT 3");
    $stmtR->execute([':kat' => $produk['id_kategori'], ':id' => $id]);
    $related = $stmtR->fetchAll();
}

$pageTitle = $produk['nama_produk'] . ' — SiKalog UMKM';
require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">

    <a class="back-link" href="<?= BASE_URL ?>/katalog.php">&larr; Kembali ke Katalog</a>

    <div class="detail-layout">

      <!-- ===== GALERI GAMBAR ===== -->
      <div class="detail-gallery">
        <div class="main-image">
          <img src="<?= gambarProduk($galeri[0]) ?>" alt="<?= h($produk['nama_produk']) ?>">
        </div>
        <div class="thumb-row">
          <?php foreach ($galeri as $i => $g): ?>
            <img src="<?= gambarProduk($g) ?>" class="<?= $i === 0 ? 'active-thumb' : '' ?>" alt="Thumbnail <?= $i + 1 ?>">
          <?php endforeach; ?>
        </div>
      </div>

      <!-- ===== INFORMASI PRODUK ===== -->
      <div class="detail-info">
        <?php if ($produk['nama_kategori']): ?>
          <span class="kategori-label"><?= h($produk['nama_kategori']) ?></span>
        <?php endif; ?>
        <h1><?= h($produk['nama_produk']) ?></h1>
        <div class="harga"><?= formatRupiah($produk['harga']) ?></div>

        <div class="deskripsi-box">
          <?= nl2br(h($produk['deskripsi'])) ?>
        </div>

        <div class="stok-info">
          <?= $produk['stok'] > 0 ? '&#10003; Stok tersedia: ' . (int)$produk['stok'] . ' pcs' : '&#10007; Stok habis' ?>
        </div>

        <a class="btn-whatsapp" href="<?= pesanWhatsApp($produk['nama_produk'], $produk['harga']) ?>" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24"><path d="M17.6 6.3A8.86 8.86 0 0 0 12.04 4 8.94 8.94 0 0 0 4 17.9L3 21l3.2-.9a8.9 8.9 0 0 0 5.8 2.1A8.94 8.94 0 0 0 12.04 4a8.86 8.86 0 0 0 5.56 2.3zM12.04 20.4a7.4 7.4 0 0 1-3.8-1l-.27-.16-2.85.75.76-2.78-.18-.28a7.5 7.5 0 1 1 13.9-3.94 7.4 7.4 0 0 1-7.56 7.4z"/></svg>
          Pesan via WhatsApp
        </a>
      </div>

    </div>

    <!-- ===== PRODUK TERKAIT ===== -->
    <?php if (count($related) > 0): ?>
      <h3 class="related-title">Produk Terkait</h3>
      <div class="product-grid">
        <?php foreach ($related as $p): ?>
          <div class="product-card">
            <a class="thumb" href="<?= BASE_URL ?>/detail.php?id=<?= (int)$p['id_produk'] ?>">
              <img src="<?= gambarProduk($p['gambar']) ?>" alt="<?= h($p['nama_produk']) ?>" loading="lazy">
            </a>
            <div class="body">
              <div class="pname"><a href="<?= BASE_URL ?>/detail.php?id=<?= (int)$p['id_produk'] ?>"><?= h($p['nama_produk']) ?></a></div>
              <div class="pprice"><?= formatRupiah($p['harga']) ?></div>
              <a class="btn-detail" href="<?= BASE_URL ?>/detail.php?id=<?= (int)$p['id_produk'] ?>">Detail</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
