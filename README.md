# Backend Aplikasi Pengajuan Izin

Selamat datang!Proyek ini adalah aplikasi backend sederhana untuk sistem pengajuan izin dengan 3 level pengguna:

- **Admin**
- **Verifikator**
- **Pengguna Biasa (Ordinary User)**

---

## 🎯 Fitur Utama

### Admin

- Melihat semua user
- Menambahkan user verifikator
- Mengubah user biasa menjadi verifikator
- Melihat seluruh izin yang diajukan
- Reset password user

### Verifikator

- Melihat daftar user (filter: terverifikasi/belum)
- Verifikasi pendaftaran user
- Melihat daftar izin (filter: belum diproses, ditolak, direvisi, diterima)
- ACC / Tolak izin dengan komentar

### Pengguna Biasa

- Registrasi dan login
- Mengajukan izin (cuti, sakit, dll.)
- Melihat dan mengedit izin yang diajukan
- Membatalkan atau menghapus izin
- Melihat status izin
- Update password

---

## 🗄️ Struktur Proyek

![struktur proyek](https://github.com/user-attachments/assets/016c3d39-13a2-453f-b9ca-02e2577c2a7f)

---

## ⚙️ Persyaratan Sistem

- PHP 8.0+
- MySQL 8.0+
- Composer
- Postman (untuk testing)

---

## 🧪 Instalasi dan Menjalankan

### 1. Clone repository

```bash
git clone https://github.com/username/perizinan-api.git
cd perizinan-api
```

### 2. Install dependencies

```bash
composer install
```

### 3. Buat database dan sesuaikan konfigurasi

- Buat database MySQL baru
- Sesuaikan konfigurasi koneksi di file:

```
app/config/Database.php
```

### 4. Jalankan server lokal

```bash
php -S localhost:8000 -t public
```

---

## 📬 Dokumentasi API

### 🔐 Autentikasi

| Endpoint           | Method | Deskripsi            |
| ------------------ | ------ | -------------------- |
| `/auth/register` | POST   | Registrasi user baru |
| `/auth/login`    | POST   | Login user           |
| `/auth/logout`   | POST   | Logout user          |

### 👤 User Biasa

| Endpoint                     | Method | Deskripsi                |
| ---------------------------- | ------ | ------------------------ |
| `/profile`                 | GET    | Lihat profil user        |
| `/profile`                 | PUT    | Update profil user       |
| `/users/password`          | PUT    | Ganti password user      |
| `/permissions`             | GET    | Daftar pengajuan izin    |
| `/permissions/{id}`        | GET    | Detail pengajuan izin    |
| `/permissions`             | POST   | Buat pengajuan izin baru |
| `/permissions/{id}`        | PUT    | Update pengajuan izin    |
| `/permissions/cancel/{id}` | PUT    | Batalkan pengajuan izin  |
| `/permissions/{id}`        | DELETE | Hapus pengajuan izin     |

### ✅ Verifikator

| Endpoint                     | Method | Deskripsi                       |
| ---------------------------- | ------ | ------------------------------- |
| `/users/verified`          | GET    | Daftar user (filter verifikasi) |
| `/users/verify/{id}`       | PUT    | Verifikasi user                 |
| `/permissions/all`         | GET    | Lihat semua pengajuan izin      |
| `/permissions/{id}/status` | PUT    | Update status pengajuan izin    |

### 🛠️ Admin

| Endpoint                 | Method | Deskripsi                          |
| ------------------------ | ------ | ---------------------------------- |
| `/users`               | GET    | Lihat semua user                   |
| `/users`               | POST   | Tambah verifikator baru            |
| `/users/role/{id}`     | PUT    | Ubah role user menjadi verifikator |
| `/users/password/{id}` | PUT    | Reset password user tertentu       |

---

## 🔍 Testing dengan Postman

1. Import file collection dan environment dari folder `/postman/`
2. Ubah environment variable sesuai kebutuhan

### Urutan Testing:

```text
Register → Login → Create Permission → Get Permissions → dll.
```

### Variabel Environment:

| Variable              | Deskripsi                                     |
| --------------------- | --------------------------------------------- |
| `base_url`          | URL dasar API (contoh: http://localhost:8000) |
| `auth_token`        | Token autentikasi pengguna biasa              |
| `verifikator_token` | Token autentikasi verifikator                 |
| `admin_token`       | Token autentikasi admin                       |
| `user_id`           | ID user yang sedang login                     |

---

## ✅ Validasi & Keamanan

- Data dikirim dan diterima dalam format **JSON**
- Validasi data wajib dilakukan sebelum pemrosesan
- Akses endpoint dibatasi berdasarkan **level autentikasi**

---

## 📬 Kontribusi

Pull request dan saran pengembangan sangat disambut!
Pastikan branch Anda up-to-date dan tuliskan deskripsi perubahan secara jelas.

---

## 🧑‍💻 Lisensi

Proyek ini bersifat open-source, lisensi disesuaikan dengan kebutuhan pengembang.
