# Deploy Osis Connect ke Produksi

Panduan ini mengasumsikan **VPS/Shared Hosting dengan PHP 8.3+ dan MySQL**.
Di komputer pengembangan aplikasi memakai SQLite; di produksi gunakan MySQL.

---

## 1. Kebutuhan server

| Kebutuhan | Versi minimal |
|---|---|
| PHP | 8.3 |
| Ekstensi PHP | `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip`, `curl`, `intl` |
| MySQL / MariaDB | 8.0 / 10.6 |
| Composer | 2.x |
| Node.js | 20.x (hanya saat build aset) |

Aplikasi **tidak memakai queue worker** (notifikasi dibuat langsung saat request)
dan **tidak punya scheduled task**, jadi cron tidak wajib.

---

## 2. Ambil kode & pasang dependensi

```bash
git clone https://github.com/Dpinajhh/NeperOsisConnect.git osisconnect
cd osisconnect

composer install --no-dev --optimize-autoloader
```

`--no-dev` penting: paket pengembangan (PHPUnit, dsb.) tidak ikut terpasang.

---

## 3. Konfigurasi environment

```bash
cp .env.production.example .env
php artisan key:generate
```

Lalu isi `.env`:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://osisconnect.sekolah.sch.id   # harus https, tanpa garis miring di akhir

DB_CONNECTION=mysql
DB_DATABASE=osis_connect
DB_USERNAME=osis_connect
DB_PASSWORD=...

SESSION_SECURE_COOKIE=true

# Akun admin pertama
ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@sekolah.sch.id
ADMIN_PASSWORD=<sandi kuat minimal 8 karakter>
```

> **Jangan pernah commit `.env`.** File itu sudah masuk `.gitignore`.

---

## 4. Database

```sql
CREATE DATABASE osis_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'osis_connect'@'localhost' IDENTIFIED BY 'sandi-database-kuat';
GRANT ALL PRIVILEGES ON osis_connect.* TO 'osis_connect'@'localhost';
FLUSH PRIVILEGES;
```

Migrasi + data awal:

```bash
php artisan migrate --force --seed
```

### Apa yang di-seed di produksi?

Karena `APP_ENV=production`, `DatabaseSeeder` **otomatis** memakai
`ProduksiSeeder`:

- data acuan: jurusan, kelas, kategori, topik;
- **satu akun admin** dari `ADMIN_NAME` / `ADMIN_EMAIL` / `ADMIN_PASSWORD`
  (dilewati bila `ADMIN_EMAIL` atau `ADMIN_PASSWORD` kosong).

Yang **tidak** dibuat di produksi: akun uji `admin@osisconnect.test`,
`guru@osisconnect.test`, `siswa@osisconnect.test` (semua berpassword
`password`), 43 pengguna random, serta konten demo (post, berita, polling).

Pengaman berlapis: `UserSeeder` menolak berjalan bila `APP_ENV=production`,
jadi salah menjalankan `php artisan db:seed` pun tidak menanam akun demo.

Seeder aman diulang (memakai `updateOrCreate`).

Setelah admin berhasil dibuat:

1. hapus baris `ADMIN_PASSWORD` dari `.env`;
2. jalankan `php artisan config:cache` (atau `php artisan optimize`) lagi;
3. masuk sebagai admin dan ganti sandi lewat menu **Profil Saya**.

---

## 5. Aset & penyimpanan berkas

```bash
php artisan storage:link      # avatar & cover berita bisa diakses publik
npm ci
npm run build                 # menghasilkan public/build
```

Folder `public/build` **tidak** ikut git, jadi wajib di-build di server
(atau build di lokal lalu unggah hasilnya).

---

## 6. Optimasi & izin folder

```bash
php artisan optimize          # config + route + view cache
```

Pastikan web server bisa menulis ke:

```
storage/
storage/app/public/
storage/framework/{cache,sessions,views}/
bootstrap/cache/
```

Contoh (Linux, user web server `www-data`):

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

Setiap kali mengubah `.env` di produksi:

```bash
php artisan optimize:clear
php artisan optimize
```

---

## 7. Konfigurasi web server

### Nginx

```nginx
server {
    listen 80;
    server_name osisconnect.sekolah.sch.id;
    root /var/www/osisconnect/public;   # HARUS berakhir di /public

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

### Apache

Arahkan **DocumentRoot ke folder `public/`**, bukan ke akar proyek.
`.htaccess` di dalam `public/` sudah disiapkan Laravel.

### Shared hosting (cPanel)

1. Upload proyek ke luar `public_html`, misal `/home/user/osisconnect`.
2. Arahkan domain/subdomain ke `/home/user/osisconnect/public`.
3. Bila tidak bisa mengubah document root: isi `public_html` dengan isi
   folder `public/`, lalu sesuaikan `index.php` agar menunjuk ke
   `__DIR__.'/../osisconnect/vendor/autoload.php'` dan
   `__DIR__.'/../osisconnect/bootstrap/app.php'`.

---

## 8. HTTPS

```bash
sudo certbot --nginx -d osisconnect.sekolah.sch.id
```

Setelah HTTPS aktif pastikan:

- `APP_URL` memakai `https://`;
- `SESSION_SECURE_COOKIE=true`;
- header HSTS otomatis ditambahkan middleware `SecurityHeaders`
  (hanya pada request HTTPS).

---

## 9. Checklist setelah deploy

- [ ] `https://domain/up` membalas **200** (health check Laravel).
- [ ] Halaman `/` , `/berita` , `/pengumuman` terbuka **tanpa login**.
- [ ] `/admin` menolak tamu (redirect) dan siswa (403).
- [ ] Login admin berhasil; sandi admin sudah diganti.
- [ ] `ADMIN_PASSWORD` sudah dihapus dari `.env`.
- [ ] `APP_DEBUG=false` dan halaman error tidak menampilkan stack trace.
- [ ] Unggah avatar di **Profil Saya** berhasil dan gambarnya tampil.
- [ ] Profil OSIS diisi lewat `/admin/profil-osis` (data awal sengaja kosong).
- [ ] `/manifest.json` valid, ikon 192/512 bisa dibuka, favicon tampil di tab.
- [ ] `robots.txt` membalas aturan blokir `/admin`.
- [ ] Email reset sandi terkirim (uji `MAIL_*`), atau `MAIL_MAILER=log` bila belum siap.

---

## 10. Perawatan

**Update aplikasi**

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan optimize
```

**Backup harian (contoh cron)**

```bash
0 2 * * * mysqldump -u osis_connect -p'...' osis_connect | gzip > /backup/osis-$(date +\%F).sql.gz
0 3 * * * tar -czf /backup/storage-$(date +\%F).tar.gz /var/www/osisconnect/storage/app/public
```

**Log** ada di `storage/logs/laravel-*.log` (rotasi harian, level `warning`).

---

## 11. Troubleshooting

| Gejala | Penyebab umum | Solusi |
|---|---|---|
| 500 setelah deploy | `.env` salah / `APP_KEY` kosong | cek `storage/logs/laravel.log`, jalankan `php artisan key:generate` |
| Halaman tampil tanpa CSS | `public/build` belum ada | `npm run build`, pastikan folder bisa dibaca web server |
| Gambar avatar 404 | symlink storage belum dibuat | `php artisan storage:link` |
| Gambar tidak bisa diunggah | izin `storage/` | perbaiki kepemilikan & permission (bagian 6) |
| Login berhasil lalu terlempar lagi | cookie `Secure` diakses via HTTP, atau `APP_URL` salah | pakai HTTPS, samakan `APP_URL` & `SESSION_DOMAIN` |
| Perubahan `.env` tidak berefek | config ter-cache | `php artisan optimize:clear && php artisan optimize` |
| Error "could not find driver" | ekstensi `pdo_mysql` belum aktif | `sudo apt install php8.3-mysql` lalu restart PHP-FPM |

---

## 12. Catatan keamanan

- `.env` tidak pernah masuk git; jangan mengirimnya lewat chat/email.
- Akun uji `@osisconnect.test` hanya ada di database pengembangan.
- Rate limit sudah aktif: login/daftar/lupa sandi 5x per jam, lapor 10x per jam,
  posting 20x per jam, komentar 30x per menit, suka 120x per menit.
- Blokir akun (`is_banned`) langsung memutus sesi karena middleware `aktif`
  memanggil `Auth::logout()`.
- Semua unggahan gambar divalidasi tipe & ukuran (maks 2 MB).
