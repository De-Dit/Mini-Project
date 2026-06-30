<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
wajibLogin();

$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;

$where  = [];
$params = [];
if ($search !== '') {
    $where[] = 'nama_produk LIKE :kw';
    $params[':kw'] = '%' . $search . '%';
}
$whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM produk $whereSql");
$stmtCount->execute($params);
$totalData  = (int)$stmtCount->fetchColumn();
$totalPages = max(1, (int)ceil($totalData / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$sql = "SELECT p.*, k.nama_kategori FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        $whereSql ORDER BY p.created_at DESC LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$paginationBase = 'produk.php' . ($search !== '' ? '?search=' . urlencode($search) : '');

$pageTitle   = 'Daftar Produk — SiKalog UMKM';
$activeMenu  = 'produk';
$topbarTitle = 'Daftar Produk | SiKalog UMKM';
require __DIR__ . '/includes/admin-header.php';
?>

<?php if ($flash): ?>
  <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['msg']) ?></div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h3>Semua Produk (<?= $totalData ?>)</h3>
    <a href="tambah-produk.php" class="btn-primary">+ Tambah Produk Baru</a>
  </div>

  <div style="padding:18px 22px 0;">
    <form class="search-form" method="get" action="produk.php">
      <input type="text" name="search" placeholder="Cari nama produk..." value="<?= h($search) ?>">
      <button type="submit">Cari</button>
    </form>
  </div>

  <?php if (count($produkList) === 0): ?>
    <div class="empty-state"><p>Tidak ada produk ditemukan.</p></div>
  <?php else: ?>
    <table class="admin-table" style="margin-top:14px;">
      <thead>
        <tr>
          <th>Gambar</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produkList as $p): ?>
          <tr>
            <td><img class="thumb-sm" src="<?= gambarProduk($p['gambar']) ?>" alt=""></td>
            <td><?= h($p['nama_produk']) ?></td>
            <td><?= h($p['nama_kategori'] ?? '—') ?></td>
            <td><?= formatRupiah($p['harga']) ?></td>
            <td><?= (int)$p['stok'] ?></td>
            <td><?= $p['is_active'] ? '<span style="color:var(--accent-green-dark);font-weight:600;">Aktif</span>' : '<span style="color:var(--danger);font-weight:600;">Nonaktif</span>' ?></td>
            <td>
              <a class="btn-edit" href="edit-produk.php?id=<?= (int)$p['id_produk'] ?>">Edit</a>
              <form class="form-hapus" data-nama="<?= h($p['nama_produk']) ?>" method="post" action="proses-hapus.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int)$p['id_produk'] ?>">
                <input type="hidden" name="redirect" value="produk.php">
                <button type="submit" class="btn-hapus">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div style="padding:18px 22px;">
      <?= renderPagination($page, $totalPages, $paginationBase) ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
