<p align="center">
    <strong>Osis Connect</strong> — Platform siswa <strong>SMK Negeri 1 Cirebon</strong>
</p>

<p align="center">
    <em>Sampurasun, Wargi Neper!</em>
</p>

---

## Tentang

**Osis Connect** adalah platform internal untuk siswa <a href="https://www.smkn1-cirebon.sch.id" target="_blank">SMK Negeri 1 Cirebon</a>
(SMK tertua di Kota Cirebon, berdiri sejak 1955, dulunya STM Negeri Cirebon).

Siswa dapat berbagi cerita, membaca pengumuman resmi dari pengurus OSIS & guru,
serta mengikuti berita kegiatan sekolah — dalam satu ruang digital yang aman dan ter-moderasi.

## Informasi Sekolah

| | |
|---|---|
| **Nama** | SMK Negeri 1 Cirebon |
| **Julukan siswa** | Neper |
| **Alamat** | Jl. Perjuangan–Sunyaragi, Karyamulya, Kesambi, Kota Cirebon, Jawa Barat |
| **Website resmi** | https://www.smkn1-cirebon.sch.id |

## Fitur utama

| Modul | Keterangan |
|---|---|
| **Feed siswa** | Post cerita/kegiatan dengan visibilitas: seluruh sekolah, satu jurusan, atau satu kelas. Bisa suka & komentar (termasuk balasan). |
| **Pengumuman** | Pengumuman resmi dari pengelola, bertarget sekolah/jurusan/kelas, bisa disematkan (pin), dan ditandai sudah dibaca. |
| **Berita** | Artikel kegiatan sekolah: draft/publish, headline, kategori, penghitung views. |
| **Notifikasi** | Notifikasi otomatis saat ada suka, komentar, atau pengumuman baru. |
| **Moderasi** | Siswa bisa melapor post/komentar; admin & guru menanganinya (open → diproses → selesai/ditolak). |
| **Multi peran** | `admin`, `guru` (pengelola), dan `siswa`. Menu kelola hanya untuk admin & guru. |
| **Keamanan** | Middleware `role` (batas peran) & `aktif` (tolak akun nonaktif/diblokir), throttle percobaan login. |

## Teknologi

- **Laravel 13** (PHP 8.3+) · SQLite (dev)
- **Tailwind CSS v4** via Vite, **Alpine.js** (dark mode berbasis class)
- Eloquent + migration + seeder + factory lengkap

## Struktur

```
app/
  Http/Controllers/      Beranda, Dashboard, Halaman, Auth/*
  Http/Middleware/       RoleMiddleware, EnsureUserIsActive
  Http/Requests/Auth/    LoginRequest (login via email atau NIS)
  Models/                User, Kelas, Jurusan, Post, Komentar, Kategori,
                         Pengumuman, Berita, Notifikasi, Laporan
database/
  migrations/            14 tabel: users, kelas, jurusan, posts, post_suka,
                         komentar, kategori, pengumuman, pengumuman_baca,
                         berita, laporan, notifikasi, cache, jobs
  seeders/               Jurusan → Kelas → Kategori → User → Post → Pengumuman → Berita
resources/views/         layouts (app, auth), auth/*, beranda, dashboard, halaman/*
routes/web.php           publik → guest → auth → admin (prefix /admin)
```

## Menjalankan

```bash
composer install
copy .env.example .env      # (Windows) — sesuaikan DB_CONNECTION=sqlite
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Lalu buka http://localhost:8000.

## Akun uji (password: `password`)

| Peran | Email / NIS |
|---|---|
| Admin (pengelola) | `admin@osisconnect.test` |
| Guru / Pembina OSIS | `guru@osisconnect.test` |
| Siswa | `siswa@osisconnect.test` atau NIS `SW001` |

## Status pengembangan

- [x] Fase 0 — Setup Laravel 13 + Tailwind v4
- [x] Fase 1 — Skema database (14 tabel), model, factory, seeder
- [x] Fase 2 — Routing lengkap, autentikasi, dashboard, landing page, identitas SMK Negeri 1 Cirebon
- [ ] Fase 3 — Feed post (buat, suka, komentar, balasan)
- [ ] Fase 4 — Pengumuman & berita (kelola, terbit, tandai baca)
- [ ] Fase 5 — Notifikasi, laporan & moderasi
- [ ] Fase 6 — Profil pengguna
- [ ] Fase 7 — Dashboard admin & kelola pengguna

---

Dibuat oleh pengurus OSIS SMK Negeri 1 Cirebon.
