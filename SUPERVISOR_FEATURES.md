# Rahbar (Supervisor) - Yangi Funksiyalar

## 🔔 Bildirishnoma Tizimi

### Xususiyatlar:
- **Real-time bildirishnomalar**: Har 30 soniyada avtomatik yangilanadi
- **Dropdown menyu**: Navbar'da qo'ng'iroq belgisi orqali tezkor kirish
- **Badge ko'rsatkichi**: O'qilmagan bildirishnomalar soni
- **Filtrlash**: Barchasi, O'qilmaganlar, O'qilganlar
- **Harakatlar**: 
  - Bitta bildirishnomani o'qilgan deb belgilash
  - Barchasini o'qilgan deb belgilash
  - Bildirishnomani o'chirish
  - O'qilganlarni o'chirish

### Bildirishnoma turlari:
- `attendance_marked` - Davomat kiritildi
- `evaluation_completed` - Baholash yakunlandi
- `logbook_submitted` - Kundalik yuborildi
- `student_added` - Yangi talaba qo'shildi
- `group_assigned` - Guruh biriktirildi

### Sahifalar:
- `/supervisor/notifications` - Barcha bildirishnomalar ro'yxati
- Navbar dropdown - Oxirgi 5 ta bildirishnoma

### API Endpoints:
- `GET /supervisor/notifications` - Bildirishnomalar sahifasi
- `GET /supervisor/notifications/recent` - Oxirgi bildirishnomalar (AJAX)
- `GET /supervisor/notifications/unread-count` - O'qilmaganlar soni (AJAX)
- `POST /supervisor/notifications/{id}/read` - O'qilgan deb belgilash
- `POST /supervisor/notifications/read-all` - Barchasini o'qilgan deb belgilash
- `DELETE /supervisor/notifications/{id}` - Bildirishnomani o'chirish
- `DELETE /supervisor/notifications/delete-read` - O'qilganlarni o'chirish

---

## 👤 Profil Bo'limi

### Xususiyatlar:
- **Shaxsiy ma'lumotlar**: Ism, email, username
- **Profil tahrirlash**: Ma'lumotlarni yangilash
- **Parol o'zgartirish**: Xavfsiz parol yangilash
- **Statistika**: 
  - Jami talabalar soni
  - Jami guruhlar soni
  - Jami davomat yozuvlari
  - Joriy oydagi davomat
- **Faoliyat tarixi**: Tizimda amalga oshirilgan harakatlar

### Sahifalar:
- `/supervisor/profile` - Profil sahifasi
- `/supervisor/profile/activity-logs` - Faoliyat tarixi

### Formalar:
1. **Profil ma'lumotlarini tahrirlash**
   - Ism Familiya (majburiy)
   - Username (ixtiyoriy)
   - Email (majburiy, noyob)

2. **Parolni o'zgartirish**
   - Joriy parol (majburiy)
   - Yangi parol (kamida 8 ta belgi)
   - Parolni tasdiqlash

### API Endpoints:
- `GET /supervisor/profile` - Profil sahifasi
- `POST /supervisor/profile/update` - Profilni yangilash
- `POST /supervisor/profile/password` - Parolni o'zgartirish
- `GET /supervisor/profile/activity-logs` - Faoliyat tarixi

---

## 🎨 UI/UX Yangilanishlar

### Navbar:
- **Bildirishnoma qo'ng'irog'i**: Badge bilan o'qilmaganlar soni
- **Profil dropdown**: Profil, Bildirishnomalar, Chiqish

### Sidebar:
- Yangi "SOZLAMALAR" bo'limi
- Bildirishnomalar havolasi
- Profil havolasi

### Dizayn:
- Modern va zamonaviy interfeys
- Gradient ranglar
- Smooth animatsiyalar
- Responsive dizayn
- Bootstrap 5.3.3 komponentlari

---

## 📊 Database

### Yangi jadval: `notifications`
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key -> users.id)
- type (varchar) - bildirishnoma turi
- title (varchar) - sarlavha
- message (text) - xabar matni
- data (json) - qo'shimcha ma'lumotlar
- is_read (boolean) - o'qilgan/o'qilmagan
- read_at (timestamp) - o'qilgan vaqt
- created_at (timestamp)
- updated_at (timestamp)
```

### Indekslar:
- `user_id, is_read` - tez qidiruv uchun
- `created_at` - sanaga ko'ra saralash uchun

---

## 🔧 Texnik Ma'lumotlar

### Models:
- `App\Models\Notification` - Bildirishnoma modeli

### Controllers:
- `App\Http\Controllers\Supervisor\NotificationController` - Bildirishnomalar boshqaruvi
- `App\Http\Controllers\Supervisor\ProfileController` - Profil boshqaruvi

### Views:
- `resources/views/supervisor/notifications/index.blade.php` - Bildirishnomalar ro'yxati
- `resources/views/supervisor/profile/index.blade.php` - Profil sahifasi
- `resources/views/supervisor/profile/activity-logs.blade.php` - Faoliyat tarixi
- `resources/views/layouts/supervisor.blade.php` - Yangilangan layout

### Migrations:
- `2025_10_15_051300_create_notifications_table.php`

---

## 🚀 O'rnatish

1. **Migratsiyani ishga tushirish**:
```bash
php artisan migrate
```

2. **Cache tozalash** (agar kerak bo'lsa):
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

3. **Test qilish**:
- Rahbar sifatida tizimga kiring
- Navbar'da qo'ng'iroq belgisini bosing
- Profil bo'limiga o'ting
- Davomat kiritib, bildirishnoma yaratilishini tekshiring

---

## 📝 Qo'shimcha Imkoniyatlar

### Kelajakda qo'shilishi mumkin:
- Email orqali bildirishnomalar
- Telegram bot integratsiyasi
- Push notifications
- Bildirishnoma sozlamalari (qaysi turlarini olish)
- Profil rasmi yuklash
- Ikki faktorli autentifikatsiya (2FA)
- Tizim tilini tanlash

---

## 🎯 Foydalanish

### Bildirishnomalar:
1. Navbar'dagi qo'ng'iroq belgisini bosing
2. Dropdown menyuda oxirgi bildirishnomalarni ko'ring
3. "Barchasini ko'rish" tugmasini bosib, to'liq ro'yxatga o'ting
4. Filtrlar yordamida kerakli bildirishnomalarni toping
5. Harakatlarni bajaring (o'qilgan deb belgilash, o'chirish)

### Profil:
1. Navbar'dagi profil dropdown'ini oching
2. "Profil" tugmasini bosing
3. Ma'lumotlarni tahrirlang va saqlang
4. Parolni o'zgartirish uchun alohida forma to'ldiring
5. Faoliyat tarixini ko'rish uchun "Ko'rish" tugmasini bosing

---

**Muallif**: Cascade AI  
**Sana**: 15.10.2025  
**Versiya**: 1.0
