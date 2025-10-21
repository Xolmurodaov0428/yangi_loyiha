# Admin Panel - To'liq Funksiyalar Ro'yxati 🎯

## 📊 Dashboard
**URL:** `/admin/dashboard`

### Xususiyatlar:
- ✅ Real vaqt soati (har soniya yangilanadi)
- ✅ 4 ta statistika kartasi (Rahbarlar, Tashkilotlar, Talabalar, Davomat)
- ✅ 3 ta Quick Actions kartasi
- ✅ So'nggi 5 ta faoliyat (timeline)
- ✅ Tezkor statistika (progress bars)
- ✅ Tezkor havolalar

### Bog'lanishlar:
- → Rahbarlar ro'yxati
- → Rahbar qo'shish
- → Talabalar ro'yxati
- → Guruh import
- → Davomat
- → Hisobotlar
- → Faoliyat jurnali

---

## 👥 Rahbarlar (Supervisors)
**URL:** `/admin/supervisors`

### CRUD Operatsiyalar:
- ✅ **Index** - Ro'yxat ko'rish (pagination, search, filter)
- ✅ **Create** - Yangi rahbar qo'shish
- ✅ **Show** - Rahbar ma'lumotlarini ko'rish
- ✅ **Edit** - Rahbarni tahrirlash
- ✅ **Delete** - Rahbarni o'chirish

### Qo'shimcha Funksiyalar:
- ✅ **Tasdiqlash** (Approve) - `approved_at` ni o'rnatish
- ✅ **Faolsizlantirish** (Deactivate) - `is_active` ni o'zgartirish
- ✅ **Parolni tiklash** - Yangi parol berish

### Filtrlar:
- Qidiruv (ism, email, username)
- Holat (Faol/Nofaol)
- Tasdiqlangan/Tasdiqlanmagan

### Bog'lanishlar:
- → User model (role: supervisor)
- → ActivityLog (barcha harakatlar)

---

## 🎓 Talabalar (Students)
**URL:** `/admin/students`

### CRUD Operatsiyalar:
- ✅ **Index** - Ro'yxat (pagination, search, filter)
- ✅ **Create** - Bitta talaba qo'shish
- ✅ **Show** - Talaba ma'lumotlari
- ✅ **Edit** - Tahrirlash
- ✅ **Delete** - O'chirish

### Qo'shimcha Funksiyalar:
- ✅ **Import** - Excel/CSV orqali guruh qo'shish
  - Namuna fayl yuklab olish
  - CSV/XLSX yuklash
  - Validatsiya va xatoliklarni ko'rsatish
- ✅ **Davomat** - Attendance tracking
  - Sana bo'yicha filtr
  - Holat belgilash (Kelgan/Kelmagan/Kechikkan/Sababli)
  - Kelish/Ketish vaqti
  - Izoh qo'shish
  - Statistika (Kelgan, Kelmagan, Kechikkan, Davomat %)
  - PDF export
  - Excel export

### Filtrlar:
- Qidiruv (ism, username)
- Guruh
- Tashkilot
- Holat (Faol/Nofaol)

### Bog'lanishlar:
- → Student model
- → Organization model (belongsTo)
- → Attendance model (hasMany)

---

## 👤 Foydalanuvchilar (Users)
**URL:** `/admin/users`

### CRUD Operatsiyalar:
- ✅ **Index** - Ro'yxat
- ✅ **Create** - Yangi foydalanuvchi
- ✅ **Show** - Ma'lumotlar
- ✅ **Edit** - Tahrirlash
- ✅ **Delete** - O'chirish (adminni emas)

### Qo'shimcha Funksiyalar:
- ✅ **Bloklash/Faollashtirish** (adminni emas)
- ✅ Himoya: O'zini va adminlarni bloklash/o'chirish mumkin emas

### Filtrlar:
- Qidiruv (ism, email)
- Rol (admin, supervisor, student)
- Holat (Faol/Nofaol)

### Bog'lanishlar:
- → User model
- → ActivityLog (barcha harakatlar)

---

## 📈 Hisobotlar (Reports)
**URL:** `/admin/reports`

### Statistika Kartalar:
- ✅ Jami talabalar (umumiy va faol)
- ✅ Tashkilotlar (umumiy va faol)
- ✅ Faol rahbarlar
- ✅ Bugungi davomat (foiz)

### Diagrammalar (Chart.js):
- ✅ **Talabalar holati** - Donut chart (Faol/Nofaol)
- ✅ **Haftalik davomat** - Bar chart (oxirgi 7 kun)

### Jadvallar:
- ✅ Guruhlar bo'yicha statistika
  - Talabalar soni
  - Faol talabalar
  - Davomat foizi
  - Progress bar
- ✅ Amaliyot muddati
  - Davom etayotgan
  - Tugagan
  - Boshlanmagan
- ✅ Top 5 tashkilotlar

### Bog'lanishlar:
- → Student model
- → Organization model
- → Attendance model
- → User model

---

## 📋 Faoliyat Jurnali (Activity Logs)
**URL:** `/admin/activity-logs`

### Xususiyatlar:
- ✅ Barcha harakatlar tarixi
- ✅ Pagination (50 ta yozuv)
- ✅ Rangli badge'lar:
  - 🟢 Login
  - 🔵 Create
  - 🔵 Update
  - 🔴 Delete
  - 🟢 Activate
  - 🟡 Deactivate

### Ma'lumotlar:
- Vaqt (diffForHumans)
- Foydalanuvchi (ism, rol)
- Harakat turi
- Tavsif
- Model (User, Student, etc.)
- IP manzil

### Statistika:
- ✅ Jami kirishlar
- ✅ Yaratilgan yozuvlar
- ✅ Tahrirlangan yozuvlar
- ✅ O'chirilgan yozuvlar
- ✅ Top 10 faol foydalanuvchi

### Bog'lanishlar:
- → ActivityLog model
- → User model (belongsTo)

---

## ⚙️ Sozlamalar (Settings)
**URL:** `/admin/settings`

### 1. Profil Sozlamalari:
- ✅ Ism o'zgartirish
- ✅ Email o'zgartirish
- ✅ Parol o'zgartirish
  - Joriy parol talab qilinadi
  - Yangi parol (min 6 ta belgi)
  - Tasdiqlash

### 2. Tizim Sozlamalari:
- ✅ Tizim nomi
- ✅ Til (O'zbekcha 🇺🇿, Русский 🇷🇺, English 🇬🇧)
- ✅ Qorong'i rejim (Dark Mode)

### 3. Telegram Sozlamalari:
- ✅ Bot Token (@BotFather)
- ✅ Chat ID (backup yuborish uchun)

### 4. Backup:
- ✅ Database backup (mysqldump)
- ✅ SQL fayl yaratish
- ✅ `storage/app/backups/` ga saqlash
- ✅ Telegram'ga yuborish (agar sozlangan bo'lsa)

### 5. Ma'lumotlar:
- ✅ Server (PHP, Laravel versiyalari)
- ✅ Database (jadvallar soni)
- ✅ Xavfsizlik (oxirgi kirish, rol)

### Bog'lanishlar:
- → Setting model (key-value)
- → User model (profil)
- → Telegram API

---

## 🗄️ Database Models

### 1. User
```php
- id, name, username, email, password
- role (admin, supervisor, student)
- is_active, approved_at
- timestamps
```

### 2. Student
```php
- id, full_name, username, password
- group_name, faculty
- organization_id (FK)
- internship_start_date, internship_end_date
- is_active, timestamps
```

### 3. Organization
```php
- id, name, address, phone, email
- is_active, timestamps
```

### 4. Attendance
```php
- id, student_id (FK), date
- status (present, absent, late, excused)
- check_in_time, check_out_time, notes
- timestamps
```

### 5. ActivityLog
```php
- id, user_id (FK), action
- model_type, model_id
- description, changes (JSON)
- ip_address, user_agent
- timestamps
```

### 6. Setting
```php
- id, key, value
- timestamps
```

---

## 🔐 Xavfsizlik

### Middleware:
- ✅ **auth** - Autentifikatsiya
- ✅ **Approved** - Tasdiqlangan foydalanuvchilar
- ✅ **AdminMiddleware** - Faqat admin roli

### Himoya:
- ✅ Adminni bloklash mumkin emas
- ✅ O'zini bloklash/o'chirish mumkin emas
- ✅ Parol hash (bcrypt)
- ✅ CSRF token
- ✅ SQL injection himoyasi (Eloquent)
- ✅ XSS himoyasi (Blade escaping)

---

## 🎨 Dizayn va UX

### Xususiyatlar:
- ✅ Responsive (mobil, planshet, desktop)
- ✅ Gradient kartalar
- ✅ Font Awesome ikonlar
- ✅ Bootstrap 5
- ✅ Hover effektlar
- ✅ Loading animatsiyalar
- ✅ Modal'lar
- ✅ Alert xabarlari
- ✅ Form validatsiya
- ✅ Progress bar'lar
- ✅ Badge'lar
- ✅ Sidebar navigation (pin/unpin)

### Ranglar:
- 🔵 Primary - Asosiy harakatlar
- 🟢 Success - Muvaffaqiyat
- 🔴 Danger - Xavfli harakatlar
- 🟡 Warning - Ogohlantirish
- 🔵 Info - Ma'lumot
- ⚪ Secondary - Ikkinchi darajali

---

## 📦 Paketlar va Kutubxonalar

### Backend:
- Laravel 11.x
- PHP 8.2+
- MySQL

### Frontend:
- Bootstrap 5
- Font Awesome 6
- Chart.js 4
- Vanilla JavaScript

### Qo'shimcha:
- Laravel HTTP Client (Telegram API)
- mysqldump (Backup)

---

## 🚀 Deployment Checklist

- [ ] `.env` faylni sozlash
- [ ] Database yaratish
- [ ] `php artisan migrate`
- [ ] `php artisan db:seed --class=AdminSeeder`
- [ ] `php artisan db:seed --class=OrganizationSeeder`
- [ ] `storage/app/backups/` papkasini yaratish
- [ ] Telegram bot sozlash (ixtiyoriy)
- [ ] Web server sozlash (Apache/Nginx)
- [ ] SSL sertifikat (HTTPS)

---

## 📞 Yordam

**Test login:**
- Admin: `admin` / `admin123`
- Talaba: `aliyev_vali` / `student123`

**Telegram bot sozlash:**
1. @BotFather ga `/newbot`
2. Token oling
3. @userinfobot dan Chat ID oling
4. Sozlamalarda kiriting

**Backup:**
- Avtomatik: Telegram orqali
- Qo'lda: `storage/app/backups/` papkasidan

---

✅ **Jami:** 100+ funksiya
✅ **Sahifalar:** 15+
✅ **Route'lar:** 39
✅ **Model'lar:** 6
✅ **Controller'lar:** 4
