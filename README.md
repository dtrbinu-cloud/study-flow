<h1 align="center">📚 StudyFlow API</h1>
<p align="center">
  Backend REST API untuk manajemen jadwal belajar, dibangun dengan Laravel & MySQL.
</p>
<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Sanctum-Auth-FF6B35?style=for-the-badge&logo=laravel&logoColor=white" alt="Sanctum">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
</p>

---

## 📖 Deskripsi

**StudyFlow** adalah backend API berbasis Laravel yang membantu pengguna mengelola jadwal belajar mereka. Mendukung autentikasi berbasis token (Laravel Sanctum), manajemen mata pelajaran (subjects), dan penjadwalan sesi belajar (schedules).

---

## 🛠️ Tech Stack

| Teknologi | Versi | Kegunaan |
|---|---|---|
| **Laravel** | 12.x | PHP Framework |
| **MySQL** | 8.0+ | Database |
| **Laravel Sanctum** | 4.x | API Token Authentication |
| **PHP** | 8.2+ | Server-side Language |

---

## 🚀 Instalasi

### Prasyarat
Pastikan sudah terinstall: **PHP 8.2+**, **Composer**, **MySQL**, **Git**

### Langkah-langkah

```bash
# 1. Clone repository
git clone https://github.com/dtrbinu-cloud/study-flow.git
cd study-flow

# 2. Install dependensi PHP
composer install

# 3. Salin file konfigurasi environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate
```

### Setup Database

```bash
# 5. Buat database MySQL baru bernama 'studyflow'
#    (bisa via phpMyAdmin atau MySQL CLI)
mysql -u root -p -e "CREATE DATABASE studyflow;"

# 6. Sesuaikan konfigurasi database di file .env
#    DB_DATABASE=studyflow
#    DB_USERNAME=root
#    DB_PASSWORD=your_password

# 7. Jalankan migrasi database
php artisan migrate
```

### Jalankan Server

```bash
# 8. Jalankan development server
php artisan serve
```

API akan berjalan di: `http://127.0.0.1:8000`

---

## 📡 API Endpoints

Base URL: `http://127.0.0.1:8000/api`

### 🔓 Auth (Public — tidak butuh token)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/register` | Registrasi pengguna baru |
| `POST` | `/api/login` | Login dan dapatkan token |

### 🔐 Auth (Private — butuh token)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/logout` | Logout dan hapus token |
| `GET` | `/api/user` | Lihat data pengguna yang sedang login |

### 📚 Subjects — Mata Pelajaran (Private)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/subjects` | Ambil semua mata pelajaran milik user |
| `POST` | `/api/subjects` | Tambah mata pelajaran baru |
| `PUT` | `/api/subjects/{id}` | Perbarui mata pelajaran |
| `DELETE` | `/api/subjects/{id}` | Hapus mata pelajaran |

### 🗓️ Schedules — Jadwal Belajar (Private)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/schedules` | Ambil semua jadwal belajar milik user |
| `POST` | `/api/schedules` | Tambah jadwal belajar baru |
| `GET` | `/api/schedules/{id}` | Lihat detail jadwal tertentu |
| `PUT` | `/api/schedules/{id}` | Perbarui jadwal |
| `DELETE` | `/api/schedules/{id}` | Hapus jadwal |

> [!NOTE]
> **Endpoint privat** memerlukan header `Authorization` di setiap request:
> ```
> Authorization: Bearer <your_token>
> ```
> Token didapatkan dari response endpoint `/api/login` atau `/api/register`.

---

### 📬 Contoh Request

**Register:**
```json
POST /api/register
Content-Type: application/json

{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Tambah Jadwal Belajar:**
```json
POST /api/schedules
Authorization: Bearer <token>
Content-Type: application/json

{
  "subject_id": 1,
  "study_date": "2026-08-01",
  "start_time": "09:00",
  "end_time": "11:00",
  "notes": "Bab 3 - Aljabar Linear"
}
```

---

## 📁 Struktur Project

```
studyflow/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/   # AuthController, ScheduleController, SubjectController
│   │   ├── Requests/          # Form Request Validation
│   │   └── Resources/         # API Resource Transformers
│   └── Models/                # User, Subject, Schedule
├── database/
│   ├── migrations/            # Schema database
│   └── seeders/
├── routes/
│   └── api.php                # Definisi semua API route
└── .env.example               # Template konfigurasi environment
```

---

## 🔧 Variabel Environment Penting

Salin `.env.example` ke `.env` dan sesuaikan nilai berikut:

```env
APP_NAME=StudyFlow
APP_KEY=           # Di-generate otomatis via php artisan key:generate

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studyflow
DB_USERNAME=root
DB_PASSWORD=       # Isi dengan password MySQL Anda
```

---

## 📄 Lisensi

Project ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).

---

<p align="center">Dibuat dengan ❤️ menggunakan Laravel</p>
