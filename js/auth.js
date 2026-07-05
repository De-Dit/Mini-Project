/* =========================================================
   SiKalog UMKM - auth.js
   Menangani proses login, logout, dan session timeout admin.
   Session disimpan di localStorage berisi id_admin + waktu
   aktivitas terakhir. Jika lewat dari sessionTimeoutMinutes
   tanpa aktivitas, sesi dianggap kedaluwarsa.
   ========================================================= */

async function loginAdmin(username, password) {
  const admin = getAdminByUsername(username.trim());
  if (!admin) {
    return { ok: false, message: "Username atau password salah." };
  }
  const hashed = await hashPassword(password);
  if (hashed !== admin.password) {
    return { ok: false, message: "Username atau password salah." };
  }
  const session = {
    id_admin: admin.id_admin,
    nama_admin: admin.nama_admin,
    username: admin.username,
    last_active: Date.now(),
  };
  writeDB(DB_KEYS.SESSION, session);
  return { ok: true };
}

function logoutAdmin() {
  localStorage.removeItem(DB_KEYS.SESSION);
  window.location.href = "login.html";
}

/* Mengecek apakah sesi admin masih valid (belum timeout). */
function getActiveSession() {
  const session = readDB(DB_KEYS.SESSION);
  if (!session) return null;

  const timeoutMs = UMKM_CONFIG.sessionTimeoutMinutes * 60 * 1000;
  const idleTime = Date.now() - session.last_active;

  if (idleTime > timeoutMs) {
    localStorage.removeItem(DB_KEYS.SESSION);
    return null;
  }
  return session;
}

/* Perbarui waktu aktivitas terakhir supaya sesi tidak timeout selama admin aktif. */
function touchSession() {
  const session = readDB(DB_KEYS.SESSION);
  if (session) {
    session.last_active = Date.now();
    writeDB(DB_KEYS.SESSION, session);
  }
}

/* Wajib dipanggil di setiap halaman admin (dashboard, produk, kategori).
   Jika sesi tidak valid, admin akan dialihkan ke halaman login. */
function requireAuth() {
  const session = getActiveSession();
  if (!session) {
    window.location.href = "login.html?expired=1";
    return null;
  }
  touchSession();
  // Perbarui sesi tiap ada interaksi pengguna di halaman admin
  ["click", "keydown", "mousemove", "scroll"].forEach((evt) => {
    window.addEventListener(evt, () => touchSession(), { passive: true });
  });
  return session;
}
