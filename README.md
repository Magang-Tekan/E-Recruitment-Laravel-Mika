# 💼 E-Recruitment System (Laravel & Livewire)

Aplikasi web sistem rekrutmen terpadu, seleksi berkas, asesmen psikotes (DISC Test), PAPI Kostick dan manajemen kandidat berbasis **Laravel 13**, **Livewire 3**, dan **TailwindCSS**.

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
- [Migrasi VPS, Domain, dan CI/CD Production](#-migrasi-vps-domain-dan-cicd-production)
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
   - Kalkulasi otomatis hasil tes kepribadian **PAPI Kostick** beserta download hasil laporan PDF.
4. **Notifikasi Email**: Pemberitahuan otomatis status lamaran (Diterima, Wawancara, Ditolak) menggunakan antrean background (*queue*).
5. **RESTful API**: Endpoint publik lowongan pekerjaan untuk integrasi dengan frontend eksternal (Next.js, Nuxt.js, mobile app).

---

## 🛠 Teknologi yang Digunakan
- **Backend Framework**: [Laravel 13](https://laravel.com/)
- **PHP Version**: PHP 8.4.1+ (sesuai dependency pada `composer.lock`)
- **Reactive UI**: [Laravel Livewire 3](https://livewire.laravel.com/) & [Livewire Volt](https://livewire.laravel.com/docs/volt)
- **Frontend / Styling**: [TailwindCSS](https://tailwindcss.com/) & [Vite](https://vitejs.dev/)
- **Authentication**: Laravel Breeze & Laravel Socialite (Google Login)
- **PDF Generator**: [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf)
- **Excel Importer**: [maatwebsite/excel](https://maatwebsite.nl/laravel-excel)
- **Database**: PostgreSQL

---

## 💻 Persyaratan Sistem (Prerequisites)
Pastikan komputer/laptop Anda telah terpasang:
- **PHP**: Versi **8.4.1** atau lebih baru
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
> - Set `APP_URL=https://career.mikacares.co.id`.
>
> Contoh nilai penting di `.env.docker` untuk domain ini:
> ```env
> APP_ENV=production
> APP_DEBUG=false
> APP_URL=https://career.mikacares.co.id
> GOOGLE_REDIRECT_URI=https://career.mikacares.co.id/auth/google/callback
> ```
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
   Isi konfigurasi berikut untuk `career.mikacares.co.id`:
   ```nginx
   server {
       listen 80;
       server_name career.mikacares.co.id;

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
   sudo certbot --nginx -d career.mikacares.co.id
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


## 🔄 Migrasi VPS, Domain, dan CI/CD Production

Bagian ini adalah runbook produksi untuk memindahkan aplikasi:

- VPS lama: `212.85.24.19`
- VPS baru: `72.60.76.18`
- Domain lama: `recruitment.tekna.id`
- Domain baru: `career.mikacares.co.id`
- Direktori project VPS lama: `/root/project/E-Recruitment-Laravel-Mika`
- Direktori deployment VPS baru: `/root/project/career`
- Port aplikasi pada host: `127.0.0.1:8097`
- Port PostgreSQL pada host: `127.0.0.1:5441`

Port `8097` dan `5441` tidak bertabrakan dengan port container yang sudah berjalan pada VPS baru (`3001`, `3010`, `8001`, `8080`, `3308`, dan `5437`). `docker-compose.yml` mengikat kedua port tersebut hanya ke `127.0.0.1`, sehingga web hanya dapat diakses melalui reverse proxy Nginx dan PostgreSQL tidak terbuka ke internet.

> Lakukan satu kali migrasi percobaan sebelum cutover final. Pada cutover final, aktifkan maintenance mode di VPS lama terlebih dahulu agar tidak ada lamaran atau perubahan data yang tertinggal setelah backup terakhir.

### A. Checklist sebelum migrasi

1. Turunkan TTL DNS `career.mikacares.co.id` menjadi `300` detik setidaknya beberapa jam sebelum cutover.
2. Pastikan record `A` untuk domain baru dapat diubah ke `72.60.76.18`.
3. Catat seluruh nilai `.env.docker` dari VPS lama. Jangan memasukkan file ini ke Git karena berisi password dan API key.
4. Siapkan akses SSH key dari workstation/admin ke kedua VPS dengan langkah pada bagian A.1.
5. Pastikan ruang disk VPS baru cukup untuk image Docker, database, berkas upload, dan backup deployment.
6. Simpan backup di lokasi lain sebelum menghapus VPS lama.

### A.1. Setup akses SSH ke kedua VPS

Ada tiga koneksi yang berbeda:

1. workstation/admin → VPS lama, untuk mengambil backup;
2. workstation/admin → VPS baru, untuk setup dan troubleshooting;
3. GitHub Actions → VPS baru, untuk deployment otomatis.

Gunakan key terpisah untuk akses admin dan GitHub Actions. Jangan mengirim private key ke VPS atau menyimpan private key di repository.

#### 1. Buat SSH key admin pada workstation

Jalankan pada komputer/workstation Anda, bukan pada VPS:

```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
ssh-keygen -t ed25519 -a 100 \
  -C "mika-career-vps-admin" \
  -f ~/.ssh/mika-career-vps-admin
```

Perintah tersebut menghasilkan:

- `~/.ssh/mika-career-vps-admin`: private key; jangan dibagikan;
- `~/.ssh/mika-career-vps-admin.pub`: public key; file ini yang dipasang di VPS.

Tampilkan public key untuk disalin:

```bash
cat ~/.ssh/mika-career-vps-admin.pub
```

#### 2. Aktifkan SSH pada masing-masing VPS

Karena saat ini belum ada akses SSH, masuk dahulu melalui web console/VNC/serial console dari provider VPS atau console yang sekarang digunakan. Jalankan blok berikut pada **VPS lama dan VPS baru**:

```bash
apt update
apt install -y openssh-server
systemctl enable --now ssh
ss -ltnp | grep ':22'
```

Jika UFW aktif, izinkan SSH:

```bash
ufw status
ufw allow OpenSSH
```

Pastikan firewall pada dashboard provider VPS juga mengizinkan TCP port `22`. Selama setup manual, port dapat dibatasi hanya untuk IP publik workstation/admin. GitHub-hosted runner memakai alamat sumber yang dapat berubah; agar workflow dapat deploy, gunakan salah satu dari dua pendekatan berikut:

- izinkan TCP `22` ke VPS baru, gunakan autentikasi key-only, dan aktifkan proteksi seperti Fail2ban; atau
- gunakan self-hosted runner/static egress bila kebijakan firewall wajib memakai allowlist IP.

Untuk proteksi dasar saat port SSH dapat dijangkau publik:

```bash
apt install -y fail2ban
systemctl enable --now fail2ban
```

Izinkan login `root` menggunakan public key tanpa mengaktifkan login root berbasis password:

```bash
mkdir -p /etc/ssh/sshd_config.d
nano /etc/ssh/sshd_config.d/60-mika-career.conf
```

Isi file tersebut:

```text
PubkeyAuthentication yes
PermitRootLogin prohibit-password
```

Validasi lalu reload SSH:

```bash
sshd -t
systemctl reload ssh
```

Jangan menutup console provider sebelum koneksi SSH key berhasil diuji.

#### 3. Pasang public key admin pada kedua VPS

Masih melalui console provider, jalankan pada **masing-masing VPS**:

```bash
install -d -m 700 /root/.ssh
nano /root/.ssh/authorized_keys
```

Paste satu baris lengkap dari `mika-career-vps-admin.pub`, simpan, kemudian atur permission:

```bash
chmod 700 /root/.ssh
chmod 600 /root/.ssh/authorized_keys
chown -R root:root /root/.ssh
```

Jika login SSH menggunakan password sudah tersedia, `ssh-copy-id` dapat digunakan sebagai alternatif dari workstation:

```bash
ssh-copy-id -i ~/.ssh/mika-career-vps-admin.pub root@212.85.24.19
ssh-copy-id -i ~/.ssh/mika-career-vps-admin.pub root@72.60.76.18
```

#### 4. Buat alias kedua server pada workstation

Edit `~/.ssh/config` pada workstation:

```sshconfig
Host career-old
    HostName 212.85.24.19
    User root
    Port 22
    IdentityFile ~/.ssh/mika-career-vps-admin
    IdentitiesOnly yes

Host career-new
    HostName 72.60.76.18
    User root
    Port 22
    IdentityFile ~/.ssh/mika-career-vps-admin
    IdentitiesOnly yes
```

Atur permission dan uji kedua koneksi:

```bash
chmod 600 ~/.ssh/config
ssh career-old
ssh career-new
```

Saat pertama kali terhubung, cocokkan fingerprint yang tampil dengan fingerprint host dari console VPS:

```bash
ssh-keygen -lf /etc/ssh/ssh_host_ed25519_key.pub
```

Jangan menerima host key jika fingerprint berbeda.

Audit port pada VPS baru:

```bash
sudo ss -ltnp
docker ps --format 'table {{.Names}}\t{{.Ports}}'
```

Jika `8097` atau `5441` ternyata sudah dipakai saat deployment dilakukan, pilih port kosong lain di `.env.docker`, misalnya:

```env
WEB_PORT=8098
FORWARD_DB_PORT=5442
```

Jika `WEB_PORT` diubah, samakan tujuan `proxy_pass` Nginx dengan port baru tersebut.

### B. Persiapan VPS baru (`72.60.76.18`)

Install Docker, Git, Nginx, dan Certbot:

```bash
sudo apt update
sudo apt install -y git nginx certbot python3-certbot-nginx
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
```

Karena lokasi project yang digunakan adalah `/root/project/career`, perintah deployment dijalankan sebagai user `root`. Pastikan repository sudah benar:

```bash
mkdir -p /root/project
cd /root/project/career
git status
git remote -v
test -f .env.docker || cp .env.docker.example .env.docker
chmod 600 .env.docker
```

Jika `/root/project/career` belum berisi repository, clone terlebih dahulu:

```bash
git clone https://github.com/Magang-Tekan/E-Recruitment-Laravel-Mika.git \
  /root/project/career
```

Untuk repository private, pasang GitHub deploy key read-only pada user `root`, lalu gunakan remote SSH (`git@github.com:Magang-Tekan/E-Recruitment-Laravel-Mika.git`). Koneksi Git dari VPS diperlukan karena workflow menjalankan `git pull --ff-only` di server.

Edit `/root/project/career/.env.docker` dan gunakan minimal konfigurasi produksi berikut. Salin nilai rahasia SMTP, Google, Cloudinary, dan integrasi lain dari environment lama; jangan memakai placeholder di produksi.

```env
APP_NAME=MikaCareer
APP_ENV=production
APP_KEY=base64:PASTE_APP_KEY_LAMA_DI_SINI
APP_DEBUG=false
APP_URL=https://career.mikacares.co.id
APP_TIMEZONE=Asia/Jakarta

COMPOSE_PROJECT_NAME=erecruitment
WEB_PORT=8097
FORWARD_DB_PORT=5441

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=rekruitmen_db
DB_USERNAME=postgres
DB_PASSWORD=GANTI_DENGAN_PASSWORD_DATABASE_YANG_KUAT

SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=career.mikacares.co.id
QUEUE_CONNECTION=database

GOOGLE_REDIRECT_URI=https://career.mikacares.co.id/auth/google/callback
```

Pertahankan `APP_KEY` lama agar session/data terenkripsi yang sudah ada tetap dapat dibaca. Jika key lama tidak tersedia, generate key baru dengan `docker compose run --rm app php artisan key:generate --show`, tetapi session lama dan data terenkripsi tertentu tidak lagi valid.

Validasi konfigurasi dan pastikan port masih kosong:

```bash
cd /root/project/career
docker compose config --quiet
sudo ss -ltn | grep -E ':(8097|5441)[[:space:]]' || true
```

### C. Backup data pada VPS lama (`212.85.24.19`)

Masuk ke folder project lama. Sesuaikan path bila instalasi lama menggunakan lokasi berbeda:

```bash
cd /root/project/E-Recruitment-Laravel-Mika
mkdir -p /tmp/e-recruitment-migration
```

Backup PostgreSQL dalam bentuk SQL terkompresi. Perintah membaca username dan nama database langsung dari environment container sehingga tidak menampilkan password:

```bash
docker compose exec -T db sh -lc \
  'pg_dump --clean --if-exists --no-owner --no-privileges -U "$POSTGRES_USER" "$POSTGRES_DB"' \
  | gzip > /tmp/e-recruitment-migration/database.sql.gz
```

Backup seluruh file upload Laravel dari volume `storage/app`:

```bash
docker compose exec -T app tar -C /var/www/storage/app -czf - . \
  > /tmp/e-recruitment-migration/storage-app.tar.gz
```

Pastikan hasil backup tidak kosong:

```bash
ls -lh /tmp/e-recruitment-migration/
gzip -t /tmp/e-recruitment-migration/database.sql.gz
gzip -t /tmp/e-recruitment-migration/storage-app.tar.gz
```

### D. Salin dan restore ke VPS baru

Cara yang direkomendasikan adalah memindahkan backup melalui workstation. Ini tidak memerlukan private key VPS lama untuk disimpan di VPS baru:

```bash
mkdir -p /tmp/e-recruitment-migration
scp career-old:/tmp/e-recruitment-migration/database.sql.gz \
  /tmp/e-recruitment-migration/
scp career-old:/tmp/e-recruitment-migration/storage-app.tar.gz \
  /tmp/e-recruitment-migration/
scp /tmp/e-recruitment-migration/database.sql.gz career-new:/tmp/
scp /tmp/e-recruitment-migration/storage-app.tar.gz career-new:/tmp/
```

Pada VPS baru, hidupkan database dan restore backup. Pastikan `.env.docker` sudah benar sebelum menjalankan langkah ini:

```bash
cd /root/project/career
docker compose up -d db
docker compose ps
gunzip -c /tmp/database.sql.gz \
  | docker compose exec -T db sh -lc \
      'psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" "$POSTGRES_DB"'
```

Build aplikasi, hidupkan service `app`, kemudian restore file upload:

```bash
docker compose build --pull
docker compose up -d app
gunzip -c /tmp/storage-app.tar.gz \
  | docker compose exec -T -u root app tar -C /var/www/storage/app -xvf -
docker compose exec -T -u root app chown -R www-data:www-data /var/www/storage
```

Jalankan migration yang belum terdapat di database lama, siapkan storage link/cache, kemudian hidupkan seluruh service:

```bash
docker compose exec -T app php artisan migrate --force
docker compose up -d --remove-orphans
docker compose exec -T app php artisan storage:link --force
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan optimize
docker compose exec -T app php artisan queue:restart
docker compose ps
```

Periksa log dan endpoint lokal:

```bash
docker compose logs --tail=100 app web queue db
curl -I http://127.0.0.1:8097
```

### E. Cutover final tanpa kehilangan data

Setelah rehearsal berhasil, lakukan cutover final pada waktu maintenance:

1. Pada VPS lama, hentikan perubahan data baru:

   ```bash
   cd /root/project/E-Recruitment-Laravel-Mika
   docker compose exec -T app php artisan down --retry=60
   docker compose exec -T queue php artisan queue:restart
   ```

2. Buat ulang backup database dan storage dengan perintah pada bagian C. Gunakan backup final ini untuk meng-overwrite hasil rehearsal di VPS baru.
3. Restore ulang database dan storage pada VPS baru dengan perintah bagian D.
4. Jalankan `php artisan migrate --force`, lalu verifikasi jumlah data penting seperti user, kandidat, lamaran, dan lowongan.
5. Jangan matikan atau menghapus VPS lama sampai domain, login, upload, email, queue, dan data produksi telah diverifikasi.

### F. Reverse proxy Nginx dan SSL domain baru

Buat `/etc/nginx/sites-available/career.mikacares.co.id` pada VPS baru:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name career.mikacares.co.id;

    client_max_body_size 50M;

    location / {
        proxy_pass http://127.0.0.1:8097;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Aktifkan site dan validasi Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/career.mikacares.co.id \
  /etc/nginx/sites-enabled/career.mikacares.co.id
sudo nginx -t
sudo systemctl reload nginx
```

Ubah record DNS `A` `career.mikacares.co.id` menjadi `72.60.76.18`. Setelah DNS resolve ke IP baru, terbitkan SSL:

```bash
dig +short career.mikacares.co.id
sudo certbot --nginx -d career.mikacares.co.id
sudo certbot renew --dry-run
```

Perbarui **Authorized redirect URI** pada Google OAuth menjadi:

```text
https://career.mikacares.co.id/auth/google/callback
```

Jika domain lama masih harus melayani bookmark pengguna, arahkan DNS `recruitment.tekna.id` ke VPS baru dan tambahkan redirect permanen setelah sertifikat domain lama tersedia:

```nginx
server {
    listen 80;
    listen 443 ssl;
    server_name recruitment.tekna.id;
    return 301 https://career.mikacares.co.id$request_uri;
}
```

### G. Menyiapkan secret GitHub Actions

Workflow berada di `.github/workflows/deploy.yml`. Deployment hanya berjalan setelah job test dan frontend build berhasil pada push ke branch `main`. Pull request hanya menjalankan test/build dan tidak menyentuh VPS.

Buat key khusus GitHub Actions pada komputer admin. Jangan gunakan private key personal atau private key milik VPS:

```bash
ssh-keygen -t ed25519 -a 100 \
  -C "github-actions-e-recruitment" \
  -f ~/.ssh/github-actions-e-recruitment
```

Pasang public key GitHub Actions hanya pada VPS baru. Perintah berikut login memakai key admin alias `career-new`, kemudian menambahkan public key CI/CD ke `/root/.ssh/authorized_keys`:

```bash
ssh-copy-id -i ~/.ssh/github-actions-e-recruitment.pub career-new
```

Tampilkan private key untuk disalin ke secret `VPS_SSH_KEY`:

```bash
cat ~/.ssh/github-actions-e-recruitment
```

Simpan private key hanya sebagai GitHub secret. Jangan menyalinnya ke VPS lama, VPS baru, repository, chat, atau dokumentasi.

Ambil SSH host key dari koneksi tepercaya dan cocokkan fingerprint dengan key di VPS:

```bash
ssh-keyscan -p 22 72.60.76.18
ssh-keyscan -p 22 72.60.76.18 2>/dev/null | ssh-keygen -lf -
```

Di repository GitHub buka **Settings → Environments → New environment**, buat environment bernama `production`, lalu tambahkan:

| Jenis | Nama | Nilai |
| :--- | :--- | :--- |
| Environment secret | `VPS_HOST` | `72.60.76.18` |
| Environment secret | `VPS_PORT` | `22` atau port SSH yang dipakai |
| Environment secret | `VPS_USERNAME` | `root` |
| Environment secret | `VPS_SSH_KEY` | Seluruh isi private key `github-actions-e-recruitment` |
| Environment secret | `VPS_KNOWN_HOSTS` | Seluruh output `ssh-keyscan` yang fingerprint-nya sudah diverifikasi |
| Environment variable | `DEPLOY_PATH` | `/root/project/career` |

Jangan memasukkan `APP_KEY`, `DB_PASSWORD`, SMTP password, atau Cloudinary secret ke workflow ini. Nilai tersebut disimpan di `/root/project/career/.env.docker` pada VPS dan file tersebut di-ignore oleh Git.

Direkomendasikan juga untuk mengatur **required reviewer** pada environment `production` jika deployment harus mendapat persetujuan manual.

### H. Cara kerja deployment otomatis

Untuk setiap push ke `main`, workflow akan:

1. Validasi Composer, install dependency PHP/Node, build asset Vite, dan menjalankan test Laravel menggunakan SQLite in-memory. Unit test bersifat wajib; feature test tetap dilaporkan tetapi sementara non-blocking sampai test redirect role-based yang lama diselaraskan dengan perilaku aplikasi saat ini.
2. Terhubung ke VPS memakai SSH dengan verifikasi `known_hosts`.
3. Menjalankan `git pull --ff-only`, validasi Compose, lalu build image terbaru.
4. Mengaktifkan maintenance mode jika aplikasi lama sedang berjalan.
5. Menunggu PostgreSQL healthy dan membuat backup ke `/root/project/e-recruitment-backups/pre-deploy-<timestamp>.sql.gz`.
6. Menjalankan `php artisan migrate --force`. Perintah ini aman dijalankan setiap deploy karena Laravel hanya menjalankan migration berstatus pending.
7. Menjalankan container baru, membuat storage link, menyegarkan cache, me-restart queue worker, menonaktifkan maintenance mode, dan melakukan health check internal.

Backup deployment tidak dihapus otomatis. Pantau kapasitas disk dan pindahkan backup penting ke object storage; hapus backup lama hanya setelah kebijakan retensi disepakati.

Deployment manual dapat dimulai dari tab **Actions → CI and production deployment → Run workflow**. Karena menggunakan commit branch `main`, pastikan perubahan yang akan dirilis sudah di-merge terlebih dahulu.

### I. Verifikasi sesudah deployment

```bash
cd /root/project/career
docker compose ps
docker compose exec -T app php artisan migrate:status
docker compose exec -T app php artisan about
docker compose logs --tail=100 queue
curl -fsS https://career.mikacares.co.id/ > /dev/null
```

Uji secara manual:

- halaman publik dan detail lowongan;
- login seluruh role dan login Google;
- upload/download CV atau foto;
- pembuatan lamaran dan pembacaan data lama;
- email notifikasi dan pemrosesan queue;
- sertifikat HTTPS serta redirect HTTP ke HTTPS.

### J. Recovery jika deployment gagal

Jika gagal sebelum migration berjalan, aplikasi lama dapat dibuka kembali:

```bash
cd /root/project/career
docker compose exec -T app php artisan up
docker compose ps
```

Jika migration sudah mulai atau selesai, jangan menjalankan `migrate:rollback` secara otomatis. Periksa migration yang berhasil dan log error lebih dahulu:

```bash
docker compose exec -T app php artisan migrate:status
docker compose logs --tail=200 app db
ls -lh /root/project/e-recruitment-backups/
```

Restore backup pre-deploy hanya bila perubahan database memang harus dibatalkan dan dampaknya sudah dipahami:

```bash
cd /root/project/career
docker compose exec -T app php artisan down --retry=60
gunzip -c /root/project/e-recruitment-backups/pre-deploy-TIMESTAMP.sql.gz \
  | docker compose exec -T db sh -lc \
      'psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" "$POSTGRES_DB"'
docker compose exec -T app php artisan up
```

Untuk deployment production berikutnya, buat migration yang backward-compatible bila memungkinkan: tambahkan kolom/table lebih dahulu, deploy kode yang dapat membaca struktur lama dan baru, baru hapus struktur lama pada rilis berikutnya.

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
