import os
import re
import unicodedata
from PIL import Image, ImageDraw, ImageFont, ImageEnhance, ImageFilter

# Jalankan dari dalam folder tools/: python3 generate_images.py
# Ganti source-photo.png dengan foto lain jika ingin membuat ulang
# gambar produk (butuh: pip install Pillow)
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(SCRIPT_DIR, "source-photo.png")
OUT_DIR = os.path.join(SCRIPT_DIR, "..", "img", "produk")
SIZE = (700, 560)

FONT_BOLD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
FONT_REG = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"

PRODUCTS = [
    ("Keripik Singkong Balado", "Makanan Ringan"),
    ("Kue Kering Nastar", "Makanan Ringan"),
    ("Emping Melinjo Original", "Makanan Ringan"),
    ("Kopi Robusta Bubuk 200gr", "Minuman"),
    ("Teh Herbal Rempah", "Minuman"),
    ("Sirup Markisa 500ml", "Minuman"),
    ("Tas Anyaman Rotan", "Kerajinan Tangan"),
    ("Gantungan Kunci Kayu Ukir", "Kerajinan Tangan"),
    ("Anyaman Tikar Pandan", "Kerajinan Tangan"),
    ("Kemeja Batik Pria Lengan Panjang", "Fashion"),
    ("Tote Bag Kanvas Motif Lokal", "Fashion"),
    ("Selendang Tenun Ikat", "Fashion"),
]

# Warna aksen per kategori (selaras dengan tema hijau/terracotta situs)
CATEGORY_COLOR = {
    "Makanan Ringan": (200, 106, 43),     # terracotta
    "Minuman": (47, 111, 79),             # hijau tua (primary)
    "Kerajinan Tangan": (139, 94, 52),    # cokelat kayu
    "Fashion": (168, 60, 74),             # merah marun lembut
}


def slugify(text):
    text = unicodedata.normalize("NFKD", text).encode("ascii", "ignore").decode()
    text = re.sub(r"[^a-zA-Z0-9]+", "-", text).strip("-").lower()
    return text


def wrap_text(draw, text, font, max_width):
    words = text.split()
    lines, current = [], ""
    for w in words:
        test = (current + " " + w).strip()
        if draw.textlength(test, font=font) <= max_width:
            current = test
        else:
            if current:
                lines.append(current)
            current = w
    if current:
        lines.append(current)
    return lines


def make_product_image(base_img, nama_produk, kategori, out_path):
    img = base_img.resize(SIZE).convert("RGB")

    # Sedikit menggelapkan & menambah kontras agar teks putih terbaca
    img = ImageEnhance.Brightness(img).enhance(0.97)
    img = ImageEnhance.Contrast(img).enhance(1.03)

    draw = ImageDraw.Draw(img, "RGBA")
    w, h = img.size
    accent = CATEGORY_COLOR.get(kategori, (47, 111, 79))

    # Pita kategori di pojok kiri atas
    font_tag = ImageFont.truetype(FONT_BOLD, 20)
    tag_text = kategori.upper()
    tag_pad_x, tag_pad_y = 16, 10
    tag_w = draw.textlength(tag_text, font=font_tag) + tag_pad_x * 2
    tag_h = 20 + tag_pad_y * 2
    draw.rounded_rectangle([20, 20, 20 + tag_w, 20 + tag_h], radius=8, fill=(*accent, 235))
    draw.text((20 + tag_pad_x, 20 + tag_pad_y - 2), tag_text, font=font_tag, fill=(255, 255, 255, 255))

    # Panel gradasi gelap di bagian bawah untuk nama produk
    overlay_h = 150
    gradient = Image.new("RGBA", (w, overlay_h), (0, 0, 0, 0))
    gdraw = ImageDraw.Draw(gradient)
    for y in range(overlay_h):
        alpha = int(190 * (y / overlay_h))
        gdraw.line([(0, y), (w, y)], fill=(20, 16, 12, alpha))
    img.paste(gradient, (0, h - overlay_h), gradient)

    # Nama produk (wrap otomatis jika panjang)
    font_title = ImageFont.truetype(FONT_BOLD, 34)
    max_text_width = w - 60
    lines = wrap_text(draw, nama_produk, font_title, max_text_width)
    if len(lines) > 2:
        lines = lines[:2]
        lines[-1] = lines[-1] + "…"

    line_height = 40
    total_text_h = line_height * len(lines)
    start_y = h - 30 - total_text_h

    for i, line in enumerate(lines):
        tx = 30
        ty = start_y + i * line_height
        # bayangan halus agar kontras di semua warna latar
        draw.text((tx + 2, ty + 2), line, font=font_title, fill=(0, 0, 0, 160))
        draw.text((tx, ty), line, font=font_title, fill=(255, 255, 255, 255))

    # Watermark kecil brand di pojok kanan bawah
    font_brand = ImageFont.truetype(FONT_REG, 16)
    brand_text = "SiKalog UMKM"
    bw = draw.textlength(brand_text, font=font_brand)
    draw.text((w - bw - 20, 16), brand_text, font=font_brand, fill=(255, 255, 255, 210))

    img.save(out_path, quality=88)


def main():
    os.makedirs(OUT_DIR, exist_ok=True)
    base_img = Image.open(SRC)

    mapping = []
    for nama, kategori in PRODUCTS:
        slug = slugify(nama)
        filename = f"{slug}.jpg"
        out_path = os.path.join(OUT_DIR, filename)
        make_product_image(base_img, nama, kategori, out_path)
        mapping.append((nama, f"img/produk/{filename}"))
        print(f"OK: {nama} -> {filename}")

    return mapping


if __name__ == "__main__":
    main()
