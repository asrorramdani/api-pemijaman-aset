# Sistem Peminjaman Aset (API)

## Deskripsi Singkat
Sistem Peminjaman Aset adalah REST API berbasis Laravel yang digunakan
untuk mengelola peminjaman barang/aset, mulai dari autentikasi user,
pembuatan order, pengelolaan item order, hingga pencatatan activity log.

## Fitur Utama
- Autentikasi JWT (Register & Login)
- Manajemen User & Role (Admin & User)
- Peminjaman Aset (Order & Order Item)
- Pengembalian & Checkout Order
- Activity Log setiap aksi user
- Dokumentasi API menggunakan Postman

## Teknologi
- Laravel 10
- MySQL
- JWT Auth
- Postman

## Cara Menjalankan Sistem

### 1. Clone Repository
```bash
git clone https://github.com/username/peminjaman-aset.git
cd peminjaman-aset
2. Install Dependency
composer install
3. Konfigurasi Environment
cp .env.example .env
php artisan key:generate
4. Migrasi Database
php artisan migrate
5. Jalankan Server
php artisan serve
Akses API di:

http://127.0.0.1:8000

Akun Uji Coba
Admin

Email: admin@test.com

Password: admin123

User

Email: user1@test.com

Password: 123456

📄 Dokumentasi API (Postman)
Dokumentasi API dapat diakses melalui link berikut:

🔗 Postman Documentation:
https://documenter.getpostman.com/view/50811860/2sBXVfjBJ3

Dokumentasi mencakup:

Endpoint

Method (GET, POST, PUT, DELETE)

Parameter

Contoh request & response

 Struktur Project
app/
routes/
database/
resources/
README.md

