/* =========================================================
   SiKalog UMKM - login.js
   Menangani proses submit form login admin, validasi input
   kosong, dan pesan galat saat kredensial salah.
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  // Jika sudah login dan sesi masih aktif, langsung ke dashboard
  if (getActiveSession()) {
    window.location.href = "dashboard.html";
    return;
  }

  const params = new URLSearchParams(window.location.search);
  if (params.get("expired") === "1") {
    document.getElementById("loginError").textContent = "Sesi Anda telah berakhir. Silakan login kembali.";
  }

  const form = document.getElementById("loginForm");
  const errorEl = document.getElementById("loginError");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorEl.textContent = "";

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value;

    if (!username || !password) {
      errorEl.textContent = "Username dan password wajib diisi.";
      return;
    }

    const submitBtn = form.querySelector("button[type=submit]");
    submitBtn.disabled = true;

    const result = await loginAdmin(username, password);

    if (result.ok) {
      window.location.href = "dashboard.html";
    } else {
      errorEl.textContent = result.message;
      submitBtn.disabled = false;
    }
  });
});
