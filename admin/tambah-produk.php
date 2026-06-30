<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
wajibLogin();

$kategoriList = getAllKategori($pdo);
$errors = [];
$old = ['nama_produk' => '', 'deskripsi' => '', 'harga' => '', 'stok' => '', 'id_kategori' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nama_produk'] = trim($_POST['nama_produk'] ?? '');
    $old['deskripsi']   = trim($_POST['deskripsi'] ?? '');
    $old['harga']       = trim($_POST['harga'] ?? '');
    $old['stok']        = trim($_POST['stok'] ?? '');
    $old['id_kategori'] = trim($_POST['id_kategori'] ?? '');

    // ── Validasi wajib isi ───────────────────────────────────────
    if ($old['nama_produk'] === '') $errors[] = 'Nama produk wajib diisi.';
    if ($old['harga'] === '' || !is_numeric($old['harga']) || (float)$old['harga'] < 0) $errors[] = 'Harga wajib diisi dengan angka yang valid.';
    if ($old['stok'] === '' || !ctype_digit((string)$old['stok'])) $errors[] = 'Stok wajib diisi dengan angka.';

    // ── Validasi & proses upload gambar (maks 2MB, jpg/png/webp) ──
    $namaFileGambar = [null, null, null];
    $fileFields = ['gambar_utama', 'gambar_2', 'gambar_3'];
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    foreach ($fileFields as $idx => $field) {
        if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$field];

            if ($file['size'] > $maxSize) {
                $errors[] = "Ukuran gambar ($field) maksimal 2 MB.";
                continue;
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!isset($allowedTypes[$mime])) {
                $errors[] = "Format gambar ($field) harus JPG, PNG, atau WEBP.";
                continue;
            }

            $ext = $allowedTypes[$mime];
            $namaBaru = 'produk_' . time() . '_' . $idx . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $tujuan = __DIR__ . '/../uploads/' . $namaBaru;

            if (move_uploaded_file($file['tmp_name'], $tujuan)) {
                $namaFileGambar[$idx] = $namaBaru;
            } else {
                $errors[] = "Gagal mengunggah gambar ($field).";
            }
        }
    }

    // ── Simpan ke database jika tidak ada error ─────────────────
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO produk
            (nama_produk, deskripsi, harga, stok, gambar, gambar_2, gambar_3, id_kategori, is_active)
            VALUES (:nama, :desk, :harga, :stok, :g1, :g2, :g3, :kat, 1)");
        $stmt->execute([
            ':nama'  => $old['nama_produk'],
            ':desk'  => $old['deskripsi'],
            ':harga' => $old['harga'],
            ':stok'  => $old['stok'],
            ':g1'    => $namaFileGambar[0],
            ':g2'    => $namaFileGambar[1],
            ':g3'    => $namaFileGambar[2],
            ':kat'   => $old['id_kategori'] !== '' ? (int)$old['id_kategori'] : null,
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Produk "' . $old['nama_produk'] . '" berhasil ditambahkan.'];
        header('Location: produk.php');
        exit;
    }
}

$pageTitle   = 'Tambah Produk — SiKalog UMKM';
$activeMenu  = 'tambah';
$topbarTitle = 'Tambah Produk | SiKalog UMKM';
require __DIR__ . '/includes/admin-header.php';
?>

<div class="panel">
  <div class="panel-head"><h3>Form Tambah Produk</h3></div>
  <div style="padding:24px 22px;">

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e): ?> &bull; <?= h($e) ?><br><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="tambah-produk.php" enctype="multipart/form-data" class="form-grid">

      <div>
        <label for="nama_produk">Nama Produk</label>
        <input type="text" id="nama_produk" name="nama_produk" value="<?= h($old['nama_produk']) ?>" placeholder="contoh: Batik Tulis Bali">
      </div>

      <div>
        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" placeholder="Jelaskan detail produk..."><?= h($old['deskripsi']) ?></textarea>
      </div>

      <div class="form-row-2">
        <div>
          <label for="harga">Harga (Rp)</label>
          <input type="number" id="harga" name="harga" min="0" step="100" value="<?= h($old['harga']) ?>" placeholder="0">
        </div>
        <div>
          <label for="stok">Stok</label>
          <input type="number" id="stok" name="stok" min="0" step="1" value="<?= h($old['stok']) ?>" placeholder="0">
        </div>
      </div>

      <div>
        <label for="id_kategori">Kategori</label>
        <select id="id_kategori" name="id_kategori">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($kategoriList as $k): ?>
            <option value="<?= (int)$k['id_kategori'] ?>" <?= $old['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>>
              <?= h($k['nama_kategori']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="gambar_utama">Gambar Utama Produk</label>
        <input type="file" id="gambar_utama" name="gambar_utama" accept="image/jpeg,image/png,image/webp">
        <div class="help-text">Format JPG/PNG/WEBP, ukuran maksimal 2 MB.</div>
      </div>

      <div class="form-row-2">
        <div>
          <label for="gambar_2">Gambar Tambahan 1 <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
          <input type="file" id="gambar_2" name="gambar_2" accept="image/jpeg,image/png,image/webp">
        </div>
        <div>
          <label for="gambar_3">Gambar Tambahan 2 <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
          <input type="file" id="gambar_3" name="gambar_3" accept="image/jpeg,image/png,image/webp">
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Simpan Produk</button>
        <a href="produk.php" class="btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
