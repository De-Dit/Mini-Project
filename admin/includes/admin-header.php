<?php
/**
 * Layout admin (sidebar + topbar) — dipakai di semua halaman /admin/*.php
 * Variabel yang harus di-set sebelum include:
 *   $pageTitle   : judul tab browser
 *   $activeMenu  : 'dashboard' | 'produk' | 'tambah' | 'kategori'
 *   $topbarTitle : judul yang tampil di topbar
 */
wajibLogin();
if (!isset($activeMenu)) $activeMenu = '';
if (!isset($topbarTitle)) $topbarTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle ?? 'Admin — SiKalog UMKM') ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="admin-wrap">

  <aside class="admin-sidebar">
    <a href="<?= BASE_URL ?>/index.php" class="brand">
      <span class="logo-mark" style="width:30px;height:30px;font-size:12px;">SK</span>
      SiKalog UMKM
    </a>
    <nav>
      <a href="dashboard.php" class="<?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
        <span class="icon">&#8962;</span> Dashboard
      </a>
      <a href="produk.php" class="<?= $activeMenu === 'produk' ? 'active' : '' ?>">
        <span class="icon">&#128722;</span> Daftar Produk
      </a>
      <a href="tambah-produk.php" class="<?= $activeMenu === 'tambah' ? 'active' : '' ?>">
        <span class="icon">&#43;</span> Tambah Produk
      </a>
      <a href="kategori.php" class="<?= $activeMenu === 'kategori' ? 'active' : '' ?>">
        <span class="icon">&#9776;</span> Kategori
      </a>
      <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">
        <span class="icon">&#10162;</span> Logout
      </a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <button class="admin-toggle" style="display:none;background:none;border:none;font-size:20px;cursor:pointer;margin-right:10px;">&#9776;</button>
      <?= h($topbarTitle) ?>
      <span style="float:right;font-weight:500;font-size:13.5px;color:var(--text-muted);">
        Halo, <?= h($_SESSION['admin_nama'] ?? 'Admin') ?>
      </span>
    </div>
    <div class="admin-content">
