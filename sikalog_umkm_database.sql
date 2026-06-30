-- ================================================================
--  SCRIPT DATABASE : SiKalog UMKM
--  Sesuai struktur pada dokumen SKPL/SRS (Bab III - Basis Data)
-- ================================================================

DROP DATABASE IF EXISTS sikalog_umkm;
CREATE DATABASE sikalog_umkm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sikalog_umkm;

-- ----------------------------------------------------------------
-- TABEL: admin
-- ----------------------------------------------------------------
CREATE TABLE admin (
    id_admin    INT             NOT NULL AUTO_INCREMENT,
    username    VARCHAR(50)     NOT NULL,
    password    VARCHAR(255)    NOT NULL,
    nama_admin  VARCHAR(100)    NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin),
    UNIQUE KEY uq_username (username)
);

-- ----------------------------------------------------------------
-- TABEL: kategori
-- ----------------------------------------------------------------
CREATE TABLE kategori (
    id_kategori     INT             NOT NULL AUTO_INCREMENT,
    nama_kategori   VARCHAR(100)    NOT NULL,
    deskripsi       VARCHAR(255)    NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_kategori)
);

-- ----------------------------------------------------------------
-- TABEL: produk
-- ----------------------------------------------------------------
CREATE TABLE produk (
    id_produk       INT             NOT NULL AUTO_INCREMENT,
    nama_produk     VARCHAR(200)    NOT NULL,
    deskripsi       TEXT            NULL,
    harga           DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    stok            INT             NOT NULL DEFAULT 0,
    gambar          VARCHAR(255)    NULL,
    gambar_2        VARCHAR(255)    NULL COMMENT 'Gambar tambahan untuk galeri thumbnail (opsional)',
    gambar_3        VARCHAR(255)    NULL COMMENT 'Gambar tambahan untuk galeri thumbnail (opsional)',
    id_kategori     INT             NULL,
    is_active       TINYINT(1)      NOT NULL DEFAULT 1,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_produk),
    CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori)
        REFERENCES kategori (id_kategori) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE INDEX idx_nama_produk ON produk (nama_produk);
CREATE INDEX idx_produk_kategori ON produk (id_kategori);

-- ----------------------------------------------------------------
-- DATA AWAL
-- ----------------------------------------------------------------

-- Admin -> username: admin | password: admin123
-- (password sudah di-hash dengan bcrypt, kompatibel dengan password_verify() PHP)
INSERT INTO admin (username, password, nama_admin)
VALUES ('admin', '$2b$10$7/bK5RJdp6X5MPh3zQoAb.F6K1WNCxZSYUVqjdubQYlZb4EgW8hSG', 'Pemilik UMKM');

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Kerajinan Tangan',  'Produk hasil kerajinan tangan khas daerah'),
('Makanan & Minuman', 'Produk olahan makanan dan minuman lokal'),
('Pakaian & Fashion', 'Produk pakaian dan aksesori fashion lokal'),
('Pertanian',         'Produk hasil pertanian dan perkebunan lokal'),
('Perikanan',         'Produk hasil laut dan olahan ikan');

INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, id_kategori) VALUES
('Batik Tulis Bali Motif Barong', 'Kain batik tulis berkualitas tinggi dengan motif Barong khas Bali. Dibuat dengan teknik tradisional menggunakan pewarna alami.', 250000.00, 15, 'batik_barong.jpg', 1),
('Krispi Tempe Original', 'Cemilan tempe krispy yang gurih dan renyah. Dibuat dari kedelai pilihan tanpa pengawet dan tanpa MSG. Kemasan 200gr.', 25000.00, 50, 'krispi_tempe.jpg', 2),
('Tas Anyaman Pandan', 'Tas cantik berbahan anyaman daun pandan asli. Dikerjakan tangan oleh pengrajin lokal Bali. Ukuran 30x25x10 cm.', 85000.00, 20, 'tas_pandan.jpg', 1),
('Sambal Matah Bali', 'Sambal matah khas Bali dengan cita rasa pedas segar. Terbuat dari bahan-bahan segar pilihan. Kemasan 150gr.', 20000.00, 100, 'sambal_matah.jpg', 2),
('Gelang Manik Tradisional', 'Gelang manik-manik buatan tangan dengan motif tradisional Bali. Bahan manik kaca berkualitas dengan benang elastis kuat.', 35000.00, 40, 'gelang_manik.jpg', 1),
('Kopi Robusta Kintamani', 'Kopi Robusta pilihan dari perkebunan dataran tinggi Kintamani, Bali. Aroma khas dengan rasa sedikit pahit dan body kuat.', 55000.00, 30, 'kopi_kintamani.jpg', 4),
('Ikan Tuna Asap Jimbaran', 'Ikan tuna asap hasil tangkapan nelayan lokal Jimbaran. Diasap dengan kayu pilihan menggunakan metode tradisional.', 65000.00, 25, 'tuna_asap.jpg', 5),
('Lukisan Kanvas Pemandangan Bali', 'Lukisan pemandangan Bali di atas kanvas ukuran 40x60 cm. Dibuat oleh seniman lokal Ubud menggunakan cat akrilik berkualitas.', 350000.00, 5, 'lukisan_bali.jpg', 1),
('Pie Susu Bali Original', 'Kue pie susu khas Bali yang terkenal dengan tekstur lembut dan manis. Isi 20 pcs per kotak.', 45000.00, 60, 'pie_susu.jpg', 2),
('Sarung Tenun Gringsing', 'Sarung tenun double ikat khas Tenganan, Bali. Proses pembuatan memerlukan waktu bertahun-tahun dengan teknik tradisional.', 500000.00, 8, 'tenun_gringsing.jpg', 3);
