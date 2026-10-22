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
| **Pencarian** | Satu kotak untuk mencari postingan, berita, pengumuman, topik, dan siswa. |
| **Pengumuman** | Pengumuman resmi dari pengelola, bertarget sekolah/jurusan/kelas, bisa disematkan (pin), dan ditandai sudah dibaca. Terbuka untuk tamu (hanya target sekolah). |
| **Berita** | Artikel kegiatan sekolah: draft/publish, headline, kategori, penghitung views. Terbuka untuk tamu. |
| **Notifikasi** | Notifikasi otomatis saat ada suka, komentar, atau pengumuman baru; bisa difilter dan ditandai dibaca. |
| **Profil** | Profil publik (post, komentar, kelas/jurusan) dan halaman **Profil Saya** untuk ubah nama, bio, nomor telepon, avatar, serta ganti kata sandi. |
| **Moderasi** | Siswa bisa melapor post/komentar; admin & guru menanganinya (open → diproses → selesai/ditolak). |
| **PWA** | Bisa dipasang di HP seperti aplikasi (manifest, ikon 192/512, maskable, warna tema). |
| **Multi peran** | `admin`, `guru` (pengelola), dan `siswa`. Menu kelola hanya untuk admin & guru. |
| **Keamanan** | Middleware `role` & `aktif`, throttle pada aksi sensitif, header keamanan, hardening mass assignment, dan validasi unggahan. |

## Teknologi

- **Laravel 13** (PHP 8.3+) · SQLite (pengembangan) / MySQL 8 (produksi)
- **Tailwind CSS v4** via Vite, **Alpine.js** (dark mode berbasis class)
- Eloquent + migration + seeder + factory lengkap
- **PHPUnit** — 136 tes, 543 asersi

## Struktur

```
app/
  Http/Controllers/      Beranda, Dashboard, Feed, Post, Topik, Cari,
                         Pengumuman, Berita, Polling, Event, ProgramKerja,
                         ProfilOsis, Notifikasi, Profil, Halaman, Auth/*, Admin/*
  Http/Middleware/       RoleMiddleware, EnsureUserIsActive, SecurityHeaders
  Http/Requests/Auth/    LoginRequest (login via email atau NIS)
  Models/                User, Kelas, Jurusan, Post, Komentar, Kategori, Topik,
                         Pengumuman, Berita, Notifikasi, Laporan, Polling, Event, ...
config/osis.php          akun admin awal untuk instalasi produksi
database/
  migrations/            14 tabel: users, kelas, jurusan, posts, post_suka,
                         komentar, kategori, pengumuman, pengumuman_baca, berita,
                         laporan, notifikasi, cache, jobs (+ topik, polling, event)
  seeders/               DatabaseSeeder (dev) & ProduksiSeeder (aman untuk produksi)
resources/views/         layouts (app, auth), auth/*, beranda, dashboard, halaman/*, admin/*
routes/web.php           publik → guest → auth → admin (prefix /admin)
tests/Feature/           auth, akses, feed, info, polling, moderasi, cari,
                         notifikasi, profil, keamanan, seeder
public/                  manifest.json, favicon.ico, ikon PWA, robots.txt
```

## Menjalankan (pengembangan)

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

## Akun uji (pengembangan, password: `password`)

| Peran | Email / NIS |
|---|---|
| Admin (pengelola) | `admin@osisconnect.test` |
| Guru / Pembina OSIS | `guru@osisconnect.test` |
| Siswa | `siswa@osisconnect.test` atau NIS `SW001` |

> Akun di atas **tidak pernah dibuat di produksi** — lihat bagian Deploy.

## Deploy

Panduan lengkap ada di **[DEPLOY.md](DEPLOY.md)**: kebutuhan server, MySQL,
migrasi + seeding produksi (akun admin diambil dari `.env`, tanpa akun demo),
`storage:link`, build aset, konfigurasi Nginx/Apache, HTTPS, backup, dan
checklist pasca-deploy.

Ringkasnya:

```bash
composer install --no-dev --optimize-autoloader
cp .env.production.example .env     # isi DB_*, APP_URL, ADMIN_*
php artisan key:generate
php artisan migrate --force --seed  # APP_ENV=production -> ProduksiSeeder
php artisan storage:link
npm ci && npm run build
php artisan optimize
```

## Status pengembangan

Semua fase inti selesai:

- [x] Fase 0 — Setup Laravel 13 + Tailwind v4 + Vite
- [x] Fase 1 — Skema database, model, factory, seeder
- [x] Fase 2 — Routing lengkap, autentikasi (email/NIS), dashboard, landing page
- [x] Fase 3 — Feed siswa (teks/foto/visibilitas, suka, komentar + balasan, hashtag, trending topic)
- [x] Fase 4 — Profil OSIS (visi, misi, kepengurusan) + kelola admin
- [x] Fase 5 — Program kerja & Event (agenda, pendaftaran peserta, kelola)
- [x] Fase 6 — Polling (satu suara per siswa, hasil persentase, kelola/tutup)
- [x] Fase 7 — Pengumuman & Berita (target, pin, tandai dibaca, headline, draft)
- [x] Fase 8 — Pencarian global & halaman topik
- [x] Fase 9 — Notifikasi, laporan & moderasi, kelola pengguna
- [x] Fase 10 — Profil mandiri, PWA, penguatan keamanan, 136 tes otomatis

Langkah berikutnya: deploy produksi mengikuti [DEPLOY.md](DEPLOY.md).

---

Dibuat oleh pengurus OSIS SMK Negeri 1 Cirebon.
