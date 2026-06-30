<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
wajibLogin();

$id       = (int)($_POST['id'] ?? 0);
$redirect = $_POST['redirect'] ?? 'produk.php';
// Hanya izinkan redirect ke halaman yang dikenal (cegah open redirect)
$allowedRedirects = ['produk.php', 'dashboard.php'];
if (!in_array($redirect, $allowedRedirects)) $redirect = 'produk.php';

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = :id");
    $stmt->execute([':id' => $id]);
    $produk = $stmt->fetch();

    if ($produk) {
        $del = $pdo->prepare("DELETE FROM produk WHERE id_produk = :id");
        $del->execute([':id' => $id]);

        // Bersihkan file gambar terkait dari folder uploads
        foreach (['gambar', 'gambar_2', 'gambar_3'] as $field) {
            if (!empty($produk[$field])) {
                $path = __DIR__ . '/../uploads/' . $produk[$field];
                if (file_exists($path)) @unlink($path);
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Produk tidak ditemukan.'];
    }
}

header('Location: ' . $redirect);
exit;
