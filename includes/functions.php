<?php
/**
 * =================================================================
 * FUNGSI-FUNGSI BANTUAN (HELPER FUNCTIONS)
 * =================================================================
 */

/** Format angka ke Rupiah, contoh: 250000 -> "Rp 250.000" */
function formatRupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/** Cegah XSS saat menampilkan data ke HTML */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/** Path URL gambar produk. Jika file tidak ada, pakai gambar placeholder. */
function gambarProduk($namaFile) {
    $path = __DIR__ . '/../uploads/' . $namaFile;
    if ($namaFile && file_exists($path)) {
        return BASE_URL . '/uploads/' . rawurlencode($namaFile);
    }
    return BASE_URL . '/assets/img/placeholder.png';
}

/** Ambil semua kategori dari database */
function getAllKategori($pdo) {
    $stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
    return $stmt->fetchAll();
}

/** Buat pesan default WhatsApp untuk sebuah produk */
function pesanWhatsApp($namaProduk, $harga) {
    $teks = "Halo, saya tertarik dengan produk *{$namaProduk}* (" . formatRupiah($harga) . "). "
          . "Apakah produk ini masih tersedia?";
    return 'https://wa.me/' . WA_NUMBER . '?text=' . rawurlencode($teks);
}

/**
 * Render komponen pagination (dipakai di katalog publik & dashboard admin)
 * $baseUrl harus sudah termasuk query string lain (search/kategori) tanpa parameter 'page'.
 */
function renderPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    $sep = (strpos($baseUrl, '?') === false) ? '?' : '&';
    $html = '<nav class="pagination" aria-label="Navigasi halaman">';

    // Tombol Previous
    if ($currentPage > 1) {
        $html .= '<a class="page-btn" href="' . $baseUrl . $sep . 'page=' . ($currentPage - 1) . '">&lsaquo; Previous</a>';
    } else {
        $html .= '<span class="page-btn disabled">&lsaquo; Previous</span>';
    }

    // Nomor halaman
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="page-num active">' . $i . '</span>';
        } else {
            $html .= '<a class="page-num" href="' . $baseUrl . $sep . 'page=' . $i . '">' . $i . '</a>';
        }
    }

    // Tombol Next
    if ($currentPage < $totalPages) {
        $html .= '<a class="page-btn" href="' . $baseUrl . $sep . 'page=' . ($currentPage + 1) . '">Next &rsaquo;</a>';
    } else {
        $html .= '<span class="page-btn disabled">Next &rsaquo;</span>';
    }

    $html .= '</nav>';
    return $html;
}

/** Pastikan hanya admin yang sudah login yang boleh akses halaman ini */
function wajibLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}
