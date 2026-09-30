# 💼 E-Recruitment System (Laravel & Livewire)

Aplikasi web sistem rekrutmen terpadu, seleksi berkas, asesmen psikotes (DISC Test), dan manajemen kandidat berbasis **Laravel 13**, **Livewire 3**, dan **TailwindCSS**.

---

## 📋 Daftar Isi
- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Persyaratan Sistem (Prerequisites)](#-persyaratan-sistem-prerequisites)
- [Langkah-Langkah Instalasi (Dari Clone Sampai Running)](#-langkah-langkah-instalasi-dari-clone-sampai-running)
  - [1. Clone Repository](#1-clone-repository)
  - [2. Install Dependensi PHP (Composer)](#2-install-dependensi-php-composer)
  - [3. Setup File Environment (.env)](#3-setup-file-environment-env)
  - [4. Generate Application Key](#4-generate-application-key)
  - [5. Konfigurasi Database](#5-konfigurasi-database)
  - [6. Jalankan Migrasi & Database Seeder](#6-jalankan-migrasi--database-seeder)
  - [7. Buat Storage Symlink](#7-buat-storage-symlink)
  - [8. Install Dependensi Frontend & Build Asset](#8-install-dependensi-frontend--build-asset)
  - [9. Menjalankan Aplikasi](#9-menjalankan-aplikasi)
- [Panduan Setup Menggunakan Docker & Deploy VPS](#-panduan-setup-menggunakan-docker--deploy-vps)
- [Integrasi Antrean & Notifikasi (Queue & Mail)](#-integrasi-antrean--notifikasi-queue--mail)
- [REST API Endpoints](#-rest-api-endpoints)
- [Troubleshooting & Solusi Masalah Umum](#-troubleshooting--solusi-masalah-umum)

---

## ✨ Fitur Utama
1. **Portal Karir Publik**: Menampilkan lowongan pekerjaan aktif, detail posisi, kriteria, dan pengajuan lamaran langsung.
2. **Multi-Role User Dashboard**:
   - **Admin / Superadmin**: Manajemen master data (perusahaan, departemen, posisi, jurusan, kategori tes, bank soal), pengguna, dan hak akses.
   - **Recruiter**: Seleksi CV/berkas pelamar, pengelolaan kandidat, penilaian ujian/esai, generate laporan DISC (PDF), dan penjadwalan wawancara.
   - **Applicant (Pelamar)**: Melengkapi profil/CV, apply lowongan kerja, melihat status lamaran, dan mengikuti ujian online.
   - **Employee (Karyawan)**: Mengikuti asesmen & evaluasi berkala karyawan.
3. **Modul Ujian & Asesmen Online**:
   - Ujian berbasis waktu, multiple choice & essay.
   - Bank soal dengan fitur import file Excel (Maatwebsite Excel).
   - Kalkulasi otomatis hasil tes kepribadian **DISC** beserta download hasil laporan PDF.
4. **Notifikasi Email**: Pemberitahuan otomatis status lamaran (Diterima, Wawancara, Ditolak) menggunakan antrean background (*queue*).
5. **RESTful API**: Endpoint publik lowongan pekerjaan untuk integrasi dengan frontend eksternal (Next.js, Nuxt.js, mobile app).

---

## 🛠 Teknologi yang Digunakan
- **Backend Framework**: [Laravel 13](https://laravel.com/)
- **PHP Version**: PHP 8.3+
- **Reactive UI**: [Laravel Livewire 3](https://livewire.laravel.com/) & [Livewire Volt](https://livewire.laravel.com/docs/volt)
- **Frontend / Styling**: [TailwindCSS](https://tailwindcss.com/) & [Vite](https://vitejs.dev/)
- **Authentication**: Laravel Breeze & Laravel Socialite (Google Login)
- **PDF Generator**: [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf)
- **Excel Importer**: [maatwebsite/excel](https://maatwebsite.nl/laravel-excel)
- **Database**: MySQL / PostgreSQL

---

## 💻 Persyaratan Sistem (Prerequisites)
Pastikan komputer/laptop Anda telah terpasang:
- **PHP**: Versi **8.3** atau lebih baru
  - Ekstensi PHP wajib: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` (atau `imagick`), `curl`, `xml`, `zip`
- **Composer**: Versi **2.x** ([Download Composer](https://getcomposer.org/))
- **Node.js & NPM**: Versi **18.x** atau **20.x+** ([Download Node.js](https://nodejs.org/))
- **Web Server & Database**: Laragon / XAMPP / MySQL Server lokal
- **Git**: Untuk cloning repository

---

## 🚀 Langkah-Langkah Instalasi (Dari Clone Sampai Running)

### 1. Clone Repository
Buka terminal (PowerShell / Git Bash / Command Prompt), arahkan ke folder web server Anda (misal `c:\laragon\www`), lalu jalankan:

```bash
git clone https://github.com/Magang-Tekan/E-Recruitment-Laravel-Mika.git
cd E-Recruitment-Laravel-Mika
```

---

### 2. Install Dependensi PHP (Composer)
Unduh seluruh package PHP yang dibutuhkan:

```bash
composer install
```

---

### 3. Setup File Environment (.env)
Salin berkas template `.env.example` menjadi `.env`:

**Untuk Windows (PowerShell / CMD):**
```powershell
copy .env.example .env
```

**Untuk Git Bash / Linux / macOS:**
```bash
cp .env.example .env
```

---

### 4. Generate Application Key
Buat kunci enkripsi aplikasi:

```bash
php artisan key:generate
```

---

### 5. Konfigurasi Database
Buka file `.env` yang baru dibuat dengan teks editor (VS Code, dsb.) dan sesuaikan konfigurasi database:

#### Opsi A: Menggunakan MySQL (Disarankan untuk Laragon / XAMPP)
Buat database baru di phpMyAdmin atau HeidiSQL, misalnya bernama `e_recruitment`. Kemudian sesuaikan konfigurasi di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_recruitment
DB_USERNAME=root
DB_PASSWORD=
```
*(Catatan: Jika menggunakan Laragon, password default MySQL biasanya kosong `""`)*

#### Opsi B: Menggunakan SQLite
Jika tidak ingin membuat database MySQL manual, Anda bisa menggunakan SQLite:
```env
DB_CONNECTION=sqlite
```
Lalu buat file database kosong:
- Windows PowerShell: `New-Item database\database.sqlite -ItemType File`
- Git Bash / Linux: `touch database/database.sqlite`

---

### 6. Jalankan Migrasi & Database Seeder
Jalankan migrasi tabel beserta seluruh data awal (roles, akun demo, master organisasi MIKA & AKA, profil perusahaan, master pendidikan, psikotes DISC & PAPI Kostick, serta showcase kegiatan):

```bash
php artisan migrate --seed
```

*(Opsional) Jika ingin menjalankan seeder tertentu secara terpisah:*
```bash
# Men-seed ulang posisi & departemen MIKA & AKA:
php artisan db:seed --class=CompanyDepartmentPositionSeeder

# Men-seed bank soal DISC:
php artisan db:seed --class=DiscQuestionSeeder
```

---

### 7. Buat Storage Symlink
Aplikasi memerlukan symbolic link agar berkas berkas yang diunggah (CV dokumen PDF, foto profil, sertifikat) dapat diakses oleh publik:

```bash
php artisan storage:link
```

---

### 8. Install Dependensi Frontend & Build Asset
Install dependensi JavaScript & compile asset TailwindCSS menggunakan Vite:

```bash
npm install
npm run build
```

---

### 9. Menjalankan Aplikasi

Anda dapat menjalankan aplikasi menggunakan salah satu cara berikut:

#### Opsi A: Menjalankan Sekaligus (All-in-One Dev Runner - Direkomendasikan)
Proyek ini dilengkapi script concurrently untuk menjalankan web server, antrean queue, log, dan Vite compiler sekaligus dalam satu terminal:

```bash
composer run dev
```

#### Opsi B: Menjalankan Secara Manual (Multi-Terminal)
Buka 3 terminal terpisah pada direktori proyek:

- **Terminal 1 (Web Server):**
  ```bash
  php artisan serve
  ```
  Aplikasi akan berjalan di: `http://localhost:8000`

- **Terminal 2 (Vite Dev Server untuk Hot Reloading):**
  ```bash
  npm run dev
  ```

- **Terminal 3 (Worker Antrean Email & Background Job):**
  ```bash
  php artisan queue:listen
  ```
  *(Wajib dijalankan agar pengiriman email status seleksi lamaran dapat diproses)*

Akses aplikasi di browser favorit Anda melalui:
👉 **[http://localhost:8000](http://localhost:8000)** atau domain lokal Laragon Anda (misal: `http://e-recruitment-laravel.test`).

---

## 🐳 Panduan Setup Menggunakan Docker & Deploy VPS

Panduan ini mencakup cara menjalankan aplikasi secara terisolasi di lokal maupun **deployment produksi di server VPS** (Ubuntu/Debian) menggunakan Docker & Docker Compose.

Container yang disediakan oleh `docker-compose.yml`:
- **`app`**: Runtime PHP 8.4-FPM beserta ekstensi lengkap, Composer dependencies, dan kode aplikasi.
- **`web`**: Nginx web server teroptimasi (port default host: `8097`).
- **`db`**: Database PostgreSQL 16 (port default host: `5441`).
- **`queue`**: Background worker otomatis untuk memproses antrean email status seleksi lamaran kandidat.

---

### 1. Persiapan Server VPS
Jika Anda menggunakan VPS baru (misal Ubuntu 22.04 / 24.04 LTS), pastikan Docker Engine & Docker Compose plugin sudah terpasang:

```bash
# Update paket sistem
sudo apt update && sudo apt upgrade -y

# Install Docker engine & Docker Compose plugin secara resmi
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Tambahkan user saat ini ke grup docker (agar dapat menjalankan docker tanpa sudo)
sudo usermod -aG docker $USER
newgrp docker
```

---

### 2. Clone Repository & Siapkan Environment Docker
Arahkan ke folder web server di VPS Anda (misalnya `/var/www`):

```bash
git clone https://github.com/Magang-Tekan/E-Recruitment-Laravel-Mika.git
cd E-Recruitment-Laravel-Mika
```

Salin berkas template `.env.docker.example` menjadi `.env.docker`:

**Untuk Linux / macOS (VPS):**
```bash
cp .env.docker.example .env.docker
```

**Untuk Windows (PowerShell / CMD):**
```powershell
copy .env.docker.example .env.docker
```

Edit berkas `.env.docker` menggunakan `nano` atau teks editor:
```bash
nano .env.docker
```

> 🔒 **Poin Kritis Konfigurasi VPS Produksi:**
> - Ubah `APP_ENV=production` dan `APP_DEBUG=false`.
> - Sesuaikan `APP_URL` dengan domain resmi Anda (misal `https://karir.perusahaan.com` atau `http://IP_VPS:8097`).
> - Ganti `DB_PASSWORD` dengan kata sandi acak yang kuat.
> - Masukkan konfigurasi SMTP email (Gmail / Mailgun / Brevo) agar notifikasi pembaruan status pelamar dapat terkirim secara otomatis.

---

### 3. Generate APP_KEY
Jalankan perintah berikut untuk meng-generate key enkripsi:

```bash
docker compose run --rm app php artisan key:generate --show
```
Salin string `base64:...` yang muncul di terminal, lalu buka kembali `.env.docker` dan tempelkan pada baris:
```env
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

### 4. Build & Jalankan Container
Jalankan seluruh service dalam mode background (*detached*):

```bash
docker compose up -d --build
```

Pastikan seluruh container (`app`, `web`, `db`, `queue`) berstatus *running* / *healthy*:
```bash
docker compose ps
```

---

### 5. Inisialisasi Database, Seeder, dan Storage
Jalankan migrasi tabel, seeder akun master, dan pembuatan storage symlink di container `app`:

```bash
# 1. Jalankan migrasi tabel beserta seluruh data awal (roles, akun demo, master data, DISC & PAPI, serta kegiatan showcase)
docker compose exec app php artisan migrate --seed

# 2. Buat symbolic link untuk storage upload berkas publik (CV dokumen, foto, dll)
docker compose exec app php artisan storage:link

# 3. Optimasi performa Laravel untuk mode produksi (cache config, routes, & views)
docker compose exec app php artisan optimize
```

---

### 6. Import Database dari File SQL ke Container
Gunakan langkah ini jika Anda sudah memiliki dump database PostgreSQL seperti `initial_db.sql` dan ingin memasukkan seluruh isi file tersebut ke database container `db`. Jalankan perintah dari root project, yaitu folder yang berisi `docker-compose.yml` dan `initial_db.sql`.

> Peringatan: langkah import bersih di bawah akan menghapus semua tabel dan data lama pada schema `public` database `rekruitmen_db`, lalu menggantinya dengan isi dari file SQL. Gunakan hanya jika Anda memang ingin reset database container.

#### Opsi A: Clean database lalu import langsung dari host (direkomendasikan)
Karena `initial_db.sql` adalah dump dari PostgreSQL 18.3 sementara container memakai PostgreSQL 16, buat dulu salinan yang menghapus baris yang tidak kompatibel:

```bash
sed '/^SET transaction_timeout = 0;/d;/^\\restrict /d;/^\\unrestrict /d' initial_db.sql > initial_db.pg16.sql
```

Bersihkan semua object lama di schema `public`:

```bash
docker compose exec -T db psql -U postgres -d rekruitmen_db -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public; GRANT ALL ON SCHEMA public TO postgres; GRANT ALL ON SCHEMA public TO public;"
```

Import file SQL yang sudah dibersihkan:

```bash
cat initial_db.pg16.sql | docker compose exec -T db psql -v ON_ERROR_STOP=1 -U postgres -d rekruitmen_db
```

#### Opsi B: Copy file SQL ke container, lalu clean dan import dari dalam container
Jika Anda ingin menyalin file SQL ke container terlebih dahulu:

```bash
sed '/^SET transaction_timeout = 0;/d;/^\\restrict /d;/^\\unrestrict /d' initial_db.sql > initial_db.pg16.sql
docker compose cp initial_db.pg16.sql db:/tmp/initial_db.pg16.sql
docker compose exec -T db psql -U postgres -d rekruitmen_db -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public; GRANT ALL ON SCHEMA public TO postgres; GRANT ALL ON SCHEMA public TO public;"
docker compose exec db psql -v ON_ERROR_STOP=1 -U postgres -d rekruitmen_db -f /tmp/initial_db.pg16.sql
```

#### Alternatif: Reset volume database Docker total
Jika Anda ingin menghapus volume database Docker sepenuhnya, gunakan perintah berikut dengan hati-hati karena seluruh data PostgreSQL pada project Compose ini akan hilang:

```bash
docker compose down -v
docker compose up -d db
sed '/^SET transaction_timeout = 0;/d;/^\\restrict /d;/^\\unrestrict /d' initial_db.sql > initial_db.pg16.sql
cat initial_db.pg16.sql | docker compose exec -T db psql -v ON_ERROR_STOP=1 -U postgres -d rekruitmen_db
docker compose up -d
```

Setelah import selesai, jalankan ulang command Laravel yang dibutuhkan:

```bash
docker compose exec app php artisan storage:link
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize
docker compose restart app queue web
```

Catatan: jika Anda memakai dump penuh seperti `initial_db.sql`, biasanya **tidak perlu** menjalankan `php artisan migrate --seed` lagi karena struktur tabel dan data sudah ikut di dalam dump.

---

### 7. Pengaturan Reverse Proxy Nginx & SSL HTTPS di VPS Host (Direkomendasikan)
Agar aplikasi dapat diakses publik melalui domain resmi menggunakan port standar 80/443 dan sertifikat SSL gratis (Let's Encrypt):

1. **Install Nginx & Certbot di VPS Host:**
   ```bash
   sudo apt install nginx certbot python3-certbot-nginx -y
   ```

2. **Buat file konfigurasi Nginx:**
   ```bash
   sudo nano /etc/nginx/sites-available/erecruitment
   ```
   Isi konfigurasi berikut (sesuaikan `domain-anda.com`):
   ```nginx
   server {
       listen 80;
       server_name domain-anda.com www.domain-anda.com;

       client_max_body_size 50M;

       location / {
           proxy_pass http://127.0.0.1:8097;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto $scheme;
       }
   }
   ```

3. **Aktifkan konfigurasi & terbitkan SSL Certbot:**
   ```bash
   sudo ln -s /etc/nginx/sites-available/erecruitment /etc/nginx/sites-enabled/
   sudo nginx -t && sudo systemctl reload nginx
   sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com
   ```

---

### 8. Perintah Operasional & Maintenance di VPS
- **Melihat status container:**
  ```bash
  docker compose ps
  ```
- **Melihat live logs:**
  ```bash
  docker compose logs -f app
  docker compose logs -f web
  docker compose logs -f queue
  ```
- **Membersihkan cache setelah update kode:**
  ```bash
  docker compose exec app php artisan optimize:clear
  docker compose exec app php artisan optimize
  ```
- **Backup database PostgreSQL:**
  ```bash
  docker compose exec -t db pg_dump -U postgres rekruitmen_db > backup_$(date +%F).sql
  ```
- **Restore database dari backup:**
  ```bash
  cat backup_xxx.sql | docker compose exec -T db psql -U postgres -d rekruitmen_db
  ```
- **Restart semua service:**
  ```bash
  docker compose restart
  ```
- **Menghentikan container:**
  ```bash
  docker compose down
  ```
- **Menghentikan container sekaligus mereset database:**
  ```bash
  docker compose down -v
  ```

---


## 📬 Integrasi Antrean & Notifikasi (Queue & Mail)

Aplikasi menggunakan antrean berbasis database untuk pengiriman email notifikasi pembaruan status lamaran (`ApplicationStatusUpdatedMail`).

Pastikan variabel berikut ada pada file `.env`:
```env
QUEUE_CONNECTION=database
```

Untuk pengujian email lokal tanpa mengirim email nyata ke internet, atur:
```env
MAIL_MAILER=log
```
Setiap email yang dikirim akan dicatat pada file log `storage/logs/laravel.log`.

Jika ingin menguji menggunakan **Mailtrap** atau **SMTP Gmail**:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@e-recruitment.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🌐 REST API Endpoints

Aplikasi menyediakan endpoint REST API untuk integrasi data lowongan pekerjaan dengan client eksternal (misalnya Next.js / Mobile App):

| Metode | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/v1/jobs` atau `/api/jobs` | Mengambil daftar lowongan pekerjaan aktif |
| `GET` | `/api/v1/jobs/{id}` atau `/api/jobs/{id}` | Mengambil detail spesifik satu lowongan pekerjaan |
| `GET` | `/api/v1/departments` | Mengambil daftar departemen perusahaan |

---

## 🧪 Menjalankan Pengujian (Testing)

Proyek ini telah dilengkapi dengan unit test dan feature test menggunakan **Pest PHP**:

```bash
php artisan test
```
Atau:
```bash
composer test
```

---

## ❓ Troubleshooting & Solusi Masalah Umum

1. **Error: `Target class [xxx] does not exist` atau Class not found**
   Jalankan:
   ```bash
   composer dump-autoload
   php artisan optimize:clear
   ```

2. **File CV atau Foto Tidak Muncul (404 Not Found)**
   Pastikan symbolic link storage sudah dibuat dengan benar:
   ```bash
   php artisan storage:link
   ```
   *Jika di Windows mengalami error akses symlink, jalankan terminal/PowerShell dengan mode "Run as Administrator".*

3. **Status Lamaran Berubah tapi Email Tidak Terkirim**
   Pastikan queue worker sedang aktif di terminal Anda:
   ```bash
   php artisan queue:listen
   ```

4. **Vite Manifest Missing / Tampilan Berantakan**
   Pastikan asset sudah dibuild:
   ```bash
   npm run build
   ```
   atau jalankan `npm run dev` selama masa pengembangan.

---

## 📄 Lisensi
Proyek ini dibuat untuk keperluan rekrutmen internal dan dirilis di bawah lisensi [MIT License](LICENSE).
