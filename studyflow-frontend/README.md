# StudyFlow — Frontend (Buku Rencana Belajar)

Frontend sederhana untuk backend Laravel StudyFlow. Dibuat pakai HTML, CSS, dan JavaScript murni
(tanpa framework, tanpa build step) — tinggal buka langsung di browser.

## Isi folder
- `index.html` — struktur halaman (login/register + dashboard)
- `style.css` — semua styling, tema "buku catatan/planner"
- `app.js` — logika: koneksi ke API, render data, form, notifikasi

## Cara menjalankan

### 1. Pastikan backend Laravel StudyFlow sudah jalan
```bash
php artisan serve
```
Backend harus aktif di `http://127.0.0.1:8000` (kalau beda, ubah `API_BASE` di baris pertama `app.js`).

### 2. Izinkan CORS di backend (WAJIB, kalau belum pernah disetup)
Karena frontend ini dibuka sebagai file terpisah (bukan dari domain yang sama dengan backend),
Laravel perlu diizinkan menerima request dari luar. Buka `config/cors.php` di project Laravel kamu,
pastikan bagian `paths` mencakup `api/*` dan `allowed_origins` mengizinkan asal frontend kamu
(untuk development, `'allowed_origins' => ['*']` paling praktis).

Kalau file `config/cors.php` belum ada, jalankan:
```bash
php artisan config:publish cors
```

### 3. Buka `index.html`
Cukup buka file `index.html` langsung di browser (double-click atau lewat ekstensi "Live Server" di
VS Code untuk pengalaman lebih baik). Tidak perlu `npm install` atau proses build apapun.

### 4. Coba pakai
- Daftar akun baru lewat tab "Daftar Baru"
- Setelah masuk, tambahkan mata pelajaran dulu lewat panel kiri
- Baru tambahkan jadwal belajar lewat panel kanan (perlu minimal 1 mata pelajaran)
- Klik ikon pensil untuk mengubah data, ikon silang untuk menghapus
- Semua aksi langsung memanggil backend lewat `fetch()`, tanpa reload halaman

## Kalau ada error CORS di console browser
Itu tandanya backend belum mengizinkan origin frontend ini. Cek kembali langkah 2 di atas,
dan pastikan `php artisan serve` sedang berjalan sebelum membuka `index.html`.
