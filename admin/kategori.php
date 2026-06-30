<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
wajibLogin();

$errors = [];

// ── Tambah kategori baru ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $nama = trim($_POST['nama_kategori'] ?? '');
    $desk = trim($_POST['deskripsi'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama kategori wajib diisi.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (:n, :d)");
        $stmt->execute([':n' => $nama, ':d' => $desk]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Kategori "' . $nama . '" berhasil ditambahkan.'];
        header('Location: kategori.php');
        exit;
    }
}

// ── Hapus kategori ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'hapus') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Kategori berhasil dihapus. Produk terkait tidak ikut terhapus.'];
    }
    header('Location: kategori.php');
    exit;
}

$kategoriList = $pdo->query("SELECT k.*, (SELECT COUNT(*) FROM produk p WHERE p.id_kategori = k.id_kategori) AS jumlah_produk
                              FROM kategori k ORDER BY k.nama_kategori ASC")->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pageTitle   = 'Kelola Kategori — SiKalog UMKM';
$activeMenu  = 'kategori';
$topbarTitle = 'Kelola Kategori | SiKalog UMKM';
require __DIR__ . '/includes/admin-header.php';
?>

<?php if ($flash): ?>
  <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['msg']) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="alert alert-error"><?php foreach ($errors as $e): ?> &bull; <?= h($e) ?><br><?php endforeach; ?></div>
<?php endif; ?>

<div class="panel" style="margin-bottom:22px;max-width:520px;">
  <div class="panel-head"><h3>Tambah Kategori Baru</h3></div>
  <div style="padding:22px;">
    <form method="post" action="kategori.php" class="form-grid">
      <input type="hidden" name="aksi" value="tambah">
      <div>
        <label for="nama_kategori">Nama Kategori</label>
        <input type="text" id="nama_kategori" name="nama_kategori" placeholder="contoh: Kerajinan Tangan">
      </div>
      <div>
        <label for="deskripsi">Deskripsi <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <input type="text" id="deskripsi" name="deskripsi" placeholder="Deskripsi singkat kategori">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-primary">+ Tambah Kategori</button>
      </div>
    </form>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Daftar Kategori (<?= count($kategoriList) ?>)</h3></div>
  <?php if (count($kategoriList) === 0): ?>
    <div class="empty-state"><p>Belum ada kategori.</p></div>
  <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>Nama Kategori</th><th>Deskripsi</th><th>Jumlah Produk</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($kategoriList as $k): ?>
          <tr>
            <td><?= h($k['nama_kategori']) ?></td>
            <td><?= h($k['deskripsi'] ?: '—') ?></td>
            <td><?= (int)$k['jumlah_produk'] ?> produk</td>
            <td>
              <form class="form-hapus-kategori" data-nama="<?= h($k['nama_kategori']) ?>" method="post" action="kategori.php" style="display:inline;">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="id" value="<?= (int)$k['id_kategori'] ?>">
                <button type="submit" class="btn-hapus">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
