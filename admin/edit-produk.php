<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
wajibLogin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = :id");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Produk tidak ditemukan.'];
    header('Location: produk.php');
    exit;
}

$kategoriList = getAllKategori($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama_produk'] ?? '');
    $desk  = trim($_POST['deskripsi'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $stok  = trim($_POST['stok'] ?? '');
    $kat   = trim($_POST['id_kategori'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($nama === '') $errors[] = 'Nama produk wajib diisi.';
    if ($harga === '' || !is_numeric($harga) || (float)$harga < 0) $errors[] = 'Harga wajib diisi dengan angka yang valid.';
    if ($stok === '' || !ctype_digit((string)$stok)) $errors[] = 'Stok wajib diisi dengan angka.';

    // ── Proses upload gambar baru (jika ada) — kalau tidak, pertahankan gambar lama ──
    $gambarBaru = [$produk['gambar'], $produk['gambar_2'], $produk['gambar_3']];
    $fileFields = ['gambar_utama', 'gambar_2', 'gambar_3'];
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $maxSize = 2 * 1024 * 1024;

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
                // Hapus gambar lama dari server (jika ada & bukan placeholder)
                if (!empty($gambarBaru[$idx])) {
                    $old = __DIR__ . '/../uploads/' . $gambarBaru[$idx];
                    if (file_exists($old)) @unlink($old);
                }
                $gambarBaru[$idx] = $namaBaru;
            } else {
                $errors[] = "Gagal mengunggah gambar ($field).";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE produk SET
            nama_produk = :nama, deskripsi = :desk, harga = :harga, stok = :stok,
            gambar = :g1, gambar_2 = :g2, gambar_3 = :g3,
            id_kategori = :kat, is_active = :aktif
            WHERE id_produk = :id");
        $stmt->execute([
            ':nama'  => $nama, ':desk' => $desk, ':harga' => $harga, ':stok' => $stok,
            ':g1' => $gambarBaru[0], ':g2' => $gambarBaru[1], ':g3' => $gambarBaru[2],
            ':kat' => $kat !== '' ? (int)$kat : null,
            ':aktif' => $isActive,
            ':id' => $id,
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Produk "' . $nama . '" berhasil diperbarui.'];
        header('Location: produk.php');
        exit;
    } else {
        // supaya form tetap menampilkan input yang baru diketik walau gagal
        $produk = array_merge($produk, ['nama_produk' => $nama, 'deskripsi' => $desk, 'harga' => $harga, 'stok' => $stok, 'id_kategori' => $kat, 'is_active' => $isActive]);
    }
}

$pageTitle   = 'Edit Produk — SiKalog UMKM';
$activeMenu  = 'produk';
$topbarTitle = 'Edit Produk | SiKalog UMKM';
require __DIR__ . '/includes/admin-header.php';
?>

<div class="panel">
  <div class="panel-head"><h3>Form Edit Produk</h3></div>
  <div style="padding:24px 22px;">

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e): ?> &bull; <?= h($e) ?><br><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="edit-produk.php?id=<?= (int)$id ?>" enctype="multipart/form-data" class="form-grid">
      <input type="hidden" name="id" value="<?= (int)$id ?>">

      <div>
        <label for="nama_produk">Nama Produk</label>
        <input type="text" id="nama_produk" name="nama_produk" value="<?= h($produk['nama_produk']) ?>">
      </div>

      <div>
        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi"><?= h($produk['deskripsi']) ?></textarea>
      </div>

      <div class="form-row-2">
        <div>
          <label for="harga">Harga (Rp)</label>
          <input type="number" id="harga" name="harga" min="0" step="100" value="<?= h($produk['harga']) ?>">
        </div>
        <div>
          <label for="stok">Stok</label>
          <input type="number" id="stok" name="stok" min="0" step="1" value="<?= h($produk['stok']) ?>">
        </div>
      </div>

      <div>
        <label for="id_kategori">Kategori</label>
        <select id="id_kategori" name="id_kategori">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($kategoriList as $k): ?>
            <option value="<?= (int)$k['id_kategori'] ?>" <?= $produk['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>>
              <?= h($k['nama_kategori']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label style="display:flex;align-items:center;gap:8px;font-weight:600;">
          <input type="checkbox" name="is_active" value="1" style="width:auto;" <?= $produk['is_active'] ? 'checked' : '' ?>>
          Tampilkan produk ini di katalog (Aktif)
        </label>
      </div>

      <div>
        <label>Gambar Saat Ini</label>
        <div style="display:flex;gap:10px;margin-bottom:10px;">
          <img src="<?= gambarProduk($produk['gambar']) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
          <?php if (!empty($produk['gambar_2'])): ?><img src="<?= gambarProduk($produk['gambar_2']) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid var(--border);"><?php endif; ?>
          <?php if (!empty($produk['gambar_3'])): ?><img src="<?= gambarProduk($produk['gambar_3']) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid var(--border);"><?php endif; ?>
        </div>
        <label for="gambar_utama">Ganti Gambar Utama <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <input type="file" id="gambar_utama" name="gambar_utama" accept="image/jpeg,image/png,image/webp">
        <div class="help-text">Kosongkan jika tidak ingin mengganti gambar. Maks 2 MB, format JPG/PNG/WEBP.</div>
      </div>

      <div class="form-row-2">
        <div>
          <label for="gambar_2">Ganti Gambar Tambahan 1</label>
          <input type="file" id="gambar_2" name="gambar_2" accept="image/jpeg,image/png,image/webp">
        </div>
        <div>
          <label for="gambar_3">Ganti Gambar Tambahan 2</label>
          <input type="file" id="gambar_3" name="gambar_3" accept="image/jpeg,image/png,image/webp">
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
        <a href="produk.php" class="btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
