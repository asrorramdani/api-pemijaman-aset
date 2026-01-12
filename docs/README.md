# 📚 API Documentation – Sistem Peminjaman Aset

Dokumentasi ini menjelaskan seluruh endpoint API yang tersedia pada **Sistem Peminjaman Aset**, termasuk autentikasi, manajemen user, order, order item, dan activity log.

---

## 🔐 Authentication

API ini menggunakan **JWT (JSON Web Token)**.

### Login

**Body:**
```json
{
  "email": "user@mail.com",
  "password": "password"
}
{
  "message": "Login berhasil",
  "token": "jwt_token"
}

POST /api/register
{
  "name": "User",
  "email": "user@mail.com",
  "password": "password"
}

👤 Profile

Get Profile (User Login)
GET /api/profile

Header:

Authorization: Bearer <token>

📦 Orders
Create Order (User)
POST /api/orders
Body:

{
  "borrow_date": "2026-01-12"
}

Get My Orders (User)

GET /api/my-orders

Get All Orders (Admin)
GET /api/orders

Order Detail

GET /api/orders/{id}

Checkout Order

POST /api/orders/{id}/checkout

Update Order (Pengembalian – Admin)

PUT /api/orders/{id}

Delete Order (Admin)

DELETE /api/orders/{id}

🧾 Order Items

Get Order Items (User)

GET /api/order-items

Add Order Item

POST /api/order-items

Body:

{
  "order_id": 1,
  "product_id": 2,
  "quantity": 1
}

Update Order Item

PUT /api/order-items/{id}

Delete Order Item

DELETE /api/order-items/{id}

📝 Activity Logs

My Activity Logs (User)

GET /api/my-activity-logs

All Activity Logs (Admin)

GET /api/activity-logs

Activity Log Detail

GET /api/activity-logs/{id}

Delete Activity Log (Admin)

DELETE /api/activity-logs/{id}

📌 Postman Documentation
Dokumentasi API lengkap beserta contoh request & response dapat diakses melalui Postman:

🔗 Postman API Documentation
https://documenter.getpostman.com/view/50811860/2sBXVfjBJ3

⚙️ Authorization Format
Semua endpoint yang membutuhkan login wajib menggunakan header:

Authorization: Bearer <JWT_TOKEN>
✅ Notes
Role admin memiliki akses penuh

Role user hanya bisa mengakses data miliknya sendiri

Semua aktivitas dicatat di Activity Log

