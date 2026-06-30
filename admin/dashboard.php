<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
wajibLogin();

// ── Statistik ────────────────────────────────────────────────
$totalProduk   = (int)$pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalKategori = (int)$pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();

// ── Produk terbaru (dengan pagination, 5 per halaman) ──────────
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$totalPages = max(1, (int)ceil($totalProduk / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT p.*, k.nama_kategori
                        FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                        ORDER BY p.created_at DESC LIMIT :lim OFFSET :off");
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkTerbaru = $stmt->fetchAll();

// ── Pesan notifikasi setelah redirect (tambah/edit/hapus) ──────
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pageTitle   = 'Dashboard Admin — SiKalog UMKM';
$activeMenu  = 'dashboard';
$topbarTitle = 'Admin Dashboard | SiKalog UMKM';
require __DIR__ . '/includes/admin-header.php';
?>

<?php if ($flash): ?>
  <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['msg']) ?></div>
<?php endif; ?>

<h3 style="margin-top:0;">Area Utama</h3>
<div class="stat-cards">
  <div class="stat-card">
    <div class="stat-icon">&#128230;</div>
    <div>
      <div class="stat-num"><?= $totalProduk ?></div>
      <div class="stat-label">Total Produk</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">&#128203;</div>
    <div>
      <div class="stat-num"><?= $totalKategori ?></div>
      <div class="stat-label">Total Kategori</div>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h3>Produk Terbaru</h3>
    <a href="tambah-produk.php" class="btn-primary">+ Tambah Produk Baru</a>
  </div>

  <?php if (count($produkTerbaru) === 0): ?>
    <div class="empty-state"><p>Belum ada produk. Klik "Tambah Produk Baru" untuk memulai.</p></div>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Gambar</th>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produkTerbaru as $p): ?>
          <tr>
            <td><img class="thumb-sm" src="<?= gambarProduk($p['gambar']) ?>" alt=""></td>
            <td><?= h($p['nama_produk']) ?></td>
            <td><?= h($p['nama_kategori'] ?? '—') ?></td>
            <td><?= formatRupiah($p['harga']) ?></td>
            <td>
              <a class="btn-edit" href="edit-produk.php?id=<?= (int)$p['id_produk'] ?>">Edit</a>
              <form class="form-hapus" data-nama="<?= h($p['nama_produk']) ?>" method="post" action="proses-hapus.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int)$p['id_produk'] ?>">
                <input type="hidden" name="redirect" value="dashboard.php">
                <button type="submit" class="btn-hapus">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div style="padding:18px 22px;">
      <?= renderPagination($page, $totalPages, 'dashboard.php') ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
