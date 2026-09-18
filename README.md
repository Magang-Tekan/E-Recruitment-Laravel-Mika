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
- [Panduan Setup Menggunakan Docker](#-panduan-setup-menggunakan-docker)
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
git clone https://github.com/IlhamTaruprasetyo/E-Recruitment-Laravel.git
cd E-Recruitment-Laravel
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
Jalankan migrasi tabel beserta data awal (roles, default accounts, master data perusahaan, bank data pendidikan, jurusan, dan soal DISC):

```bash
php artisan migrate --seed
```

*(Opsional) Jika ingin menyertakan seeder pertanyaan DISC lengkap atau data master profil perusahaan:*
```bash
php artisan db:seed --class=DiscQuestionSeeder
php artisan db:seed --class=CompanyProfileSeeder
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

## 🐳 Panduan Setup Menggunakan Docker

Jika Anda ingin menjalankan aplikasi secara terisolasi di dalam container Docker tanpa perlu menginstal PHP, Node.js, Composer, atau database secara manual di sistem lokal:

### 1. Prasyarat
Pastikan sistem Anda telah terpasang:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (untuk Windows / macOS) atau Docker Engine & Docker Compose (untuk Linux).

### 2. Siapkan File Environment Docker
Salin file template `.env.docker.example` menjadi `.env.docker`:

**Untuk Windows (PowerShell / CMD):**
```powershell
copy .env.docker.example .env.docker
```

**Untuk Git Bash / Linux / macOS:**
```bash
cp .env.docker.example .env.docker
```

> 🔒 **Catatan Keamanan & Konfigurasi:**
> - Buka file `.env.docker` dan sesuaikan nilainya dengan kebutuhan lokal Anda.
> - **Jangan pernah memasukkan atau membagikan kredensial rahasia/asli** (seperti password email, Google Client Secret, atau Cloudinary API Key) ke repositori publik.
> - Tentukan nilai `DB_PASSWORD` Anda sendiri untuk database PostgreSQL di dalam file `.env.docker`.
> - Konfigurasi mail dan OAuth Google dapat diisi sesuai kebutuhan pengujian masing-masing.

### 3. Build & Jalankan Container
Jalankan Docker Compose dalam mode background (*detached*):

```bash
docker compose up -d --build
```

Container yang akan dibuat dan dijalankan:
- **`app`**: Runtime PHP 8.4-FPM beserta seluruh ekstensi & Composer dependencies.
- **`web`**: Nginx web server (port bawaan: `8080`).
- **`db`**: Database PostgreSQL 16 (port bawaan host: `5433`).

### 4. Inisialisasi Aplikasi di Dalam Container
Jalankan perintah-perintah Artisan berikut melalui container `app`:

```bash
# 1. Generate Application Key (jika belum terisi di .env.docker)
docker compose exec app php artisan key:generate

# 2. Jalankan migrasi database beserta data seeder
docker compose exec app php artisan migrate --seed

# 3. Buat symbolic link untuk storage publik
docker compose exec app php artisan storage:link
```

### 5. Akses Aplikasi
Buka peramban (browser) dan akses aplikasi melalui:
👉 **[http://localhost:8080](http://localhost:8080)**

*(Port web dapat diubah sesuai preferensi melalui variabel `WEB_PORT` di `.env.docker`)*

### 6. Perintah Operasional Docker yang Berguna
- **Melihat status container yang sedang berjalan:**
  ```bash
  docker compose ps
  ```
- **Melihat log container secara live:**
  ```bash
  docker compose logs -f app
  docker compose logs -f web
  ```
- **Masuk ke terminal/shell container PHP:**
  ```bash
  docker compose exec app sh
  ```
- **Menjalankan queue worker di background:**
  ```bash
  docker compose exec -d app php artisan queue:work
  ```
- **Menghentikan container:**
  ```bash
  docker compose down
  ```
- **Menghentikan container sekaligus menghapus volume database (reset data):**
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
