<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

// ── Ambil parameter dari URL ────────────────────────────────────
$search   = trim($_GET['search'] ?? '');
$idKategori = isset($_GET['kategori']) && $_GET['kategori'] !== '' ? (int)$_GET['kategori'] : null;
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 6;
$offset   = ($page - 1) * $perPage;

// ── Bangun query secara dinamis (aman dari SQL Injection) ──────
$where  = ['is_active = 1'];
$params = [];

if ($search !== '') {
    $where[] = '(nama_produk LIKE :kw1 OR deskripsi LIKE :kw2)';
    $params[':kw1'] = '%' . $search . '%';
    $params[':kw2'] = '%' . $search . '%';
}
if ($idKategori) {
    $where[] = 'id_kategori = :kat';
    $params[':kat'] = $idKategori;
}
$whereSql = implode(' AND ', $where);

// Hitung total data (untuk pagination)
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE $whereSql");
$stmtCount->execute($params);
$totalData  = (int)$stmtCount->fetchColumn();
$totalPages = max(1, (int)ceil($totalData / $perPage));
$page       = min($page, $totalPages); // jaga-jaga page di luar batas
$offset     = ($page - 1) * $perPage;

// Ambil data produk halaman ini
$sql = "SELECT * FROM produk WHERE $whereSql ORDER BY created_at DESC LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();

$kategoriList = getAllKategori($pdo);

// Base URL untuk pagination (pertahankan search & kategori, ganti page saja)
$qs = [];
if ($search !== '') $qs['search'] = $search;
if ($idKategori) $qs['kategori'] = $idKategori;
$paginationBase = 'katalog.php' . (count($qs) ? '?' . http_build_query($qs) : '');

$pageTitle = 'Katalog Produk — SiKalog UMKM';
$activeNav = 'katalog';
require __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-bottom:20px;">
  <div class="container">

    <div class="katalog-top">
      <h2 class="section-title" style="margin:0;text-align:left;">Katalog Produk</h2>
      <form class="search-form" method="get" action="katalog.php">
        <?php if ($idKategori): ?><input type="hidden" name="kategori" value="<?= (int)$idKategori ?>"><?php endif; ?>
        <input type="text" name="search" placeholder="Cari produk..." value="<?= h($search) ?>">
        <button type="submit">Cari</button>
      </form>
    </div>

    <?php if ($search !== ''): ?>
      <p style="color:var(--text-muted);margin-top:-14px;">
        Menampilkan hasil pencarian untuk: <strong>"<?= h($search) ?>"</strong>
        &nbsp;·&nbsp; <a href="katalog.php<?= $idKategori ? '?kategori=' . (int)$idKategori : '' ?>">Hapus pencarian</a>
      </p>
    <?php endif; ?>

    <div class="katalog-layout">

      <!-- ===== SIDEBAR KATEGORI ===== -->
      <aside class="cat-sidebar">
        <h4>Kategori</h4>
        <a href="katalog.php<?= $search !== '' ? '?search=' . urlencode($search) : '' ?>"
           class="<?= !$idKategori ? 'active' : '' ?>">Semua Produk</a>
        <?php foreach ($kategoriList as $k):
          $qs2 = ['kategori' => $k['id_kategori']];
          if ($search !== '') $qs2['search'] = $search;
        ?>
          <a href="katalog.php?<?= http_build_query($qs2) ?>"
             class="<?= $idKategori == $k['id_kategori'] ? 'active' : '' ?>">
            <?= h($k['nama_kategori']) ?>
          </a>
        <?php endforeach; ?>
      </aside>

      <!-- ===== GRID PRODUK ===== -->
      <div>
        <?php if (count($produkList) === 0): ?>
          <div class="empty-state">
            <div class="icon">&#128269;</div>
            <p>Produk tidak ditemukan.<?= $search !== '' ? ' Coba kata kunci lain.' : '' ?></p>
          </div>
        <?php else: ?>
          <div class="product-grid">
            <?php foreach ($produkList as $p): ?>
              <div class="product-card">
                <a class="thumb" href="detail.php?id=<?= (int)$p['id_produk'] ?>">
                  <img src="<?= gambarProduk($p['gambar']) ?>" alt="<?= h($p['nama_produk']) ?>" loading="lazy">
                </a>
                <div class="body">
                  <div class="pname"><a href="detail.php?id=<?= (int)$p['id_produk'] ?>"><?= h($p['nama_produk']) ?></a></div>
                  <div class="pprice"><?= formatRupiah($p['harga']) ?></div>
                  <a class="btn-detail" href="detail.php?id=<?= (int)$p['id_produk'] ?>">Detail</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <?= renderPagination($page, $totalPages, $paginationBase) ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
