<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

// Jika sudah login, langsung lempar ke dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ── Validasi: kolom tidak boleh kosong ──────────────────────
    if ($username === '' || $password === '') {
        $error = 'Username dan Password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $admin = $stmt->fetch();

        // ── Verifikasi password (hash) ──────────────────────────
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id_admin'];
            $_SESSION['admin_nama'] = $admin['nama_admin'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau Password yang Anda masukkan salah.';
        }
    }
}

$pageTitle = 'Login Admin — SiKalog UMKM';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="<?= BASE_URL ?>/index.php" class="logo-box">
      <span class="logo-mark">SK</span>
      <span>SiKalog UMKM</span>
    </a>
  </div>
</header>

<div class="login-wrap">
  <div class="login-card">
    <div class="login-head">
      <div class="logo-mark" style="width:46px;height:46px;font-size:16px;">SK</div>
      <h2>Login Admin</h2>
    </div>
    <div class="login-body">
      <form method="post" action="login.php" novalidate>
        <div class="field-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Masukkan username" required
                 value="<?= h($_POST['username'] ?? '') ?>" autofocus>
        </div>
        <div class="field-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit" class="btn-login">Masuk</button>

        <?php if ($error): ?>
          <div class="login-error"><?= h($error) ?></div>
        <?php endif; ?>
      </form>
    </div>
  </div>
</div>

<footer class="site-footer">
  <div class="container" style="justify-content:center;">
    <span>&copy; <?= date('Y') ?> SiKalog UMKM. Hak Cipta Dilindungi.</span>
  </div>
</footer>

</body>
</html>
