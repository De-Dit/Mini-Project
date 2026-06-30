<?php
/**
 * Header publik — dipakai di index.php, katalog.php, detail.php
 * Variabel opsional yang bisa di-set sebelum include:
 *   $pageTitle  : judul tab browser
 *   $activeNav  : 'beranda' | 'katalog' | null
 */
if (!isset($pageTitle)) $pageTitle = 'SiKalog UMKM';
if (!isset($activeNav)) $activeNav = '';
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

    <button class="nav-toggle" aria-label="Buka menu">&#9776;</button>

    <nav class="main-nav">
      <a href="<?= BASE_URL ?>/index.php" class="<?= $activeNav === 'beranda' ? 'active' : '' ?>">BERANDA</a>
      <a href="<?= BASE_URL ?>/katalog.php" class="<?= $activeNav === 'katalog' ? 'active' : '' ?>">KATALOG</a>
      <a href="<?= BASE_URL ?>/index.php#tentang-kami">TENTANG KAMI</a>
      <a href="<?= BASE_URL ?>/index.php#kontak">KONTAK</a>
    </nav>
  </div>
</header>
