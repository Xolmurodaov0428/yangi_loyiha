# Admin Panel Test Checklist ✅

## 1. Kirish (Authentication)
- [ ] Login sahifasi ochiladi: `/login`
- [ ] Admin bilan kirish: `admin` / `admin123`
- [ ] Xato parol bilan kirish (xatolik ko'rsatilishi kerak)
- [ ] Dashboard'ga yo'naltiriladi

## 2. Dashboard (`/admin/dashboard`)
- [ ] Real vaqt soati ishlaydi (har soniya yangilanadi)
- [ ] Statistika kartalar to'g'ri:
  - [ ] Rahbarlar soni
  - [ ] Tashkilotlar soni (5 ta)
  - [ ] Talabalar soni (3 ta)
  - [ ] Bugungi davomat foizi
- [ ] Quick Actions tugmalari ishlaydi
- [ ] So'nggi faoliyat ko'rsatiladi
- [ ] Tezkor statistika progress bar'lar
- [ ] Tezkor havolalar ishlaydi

## 3. Rahbarlar (`/admin/supervisors`)
- [ ] Ro'yxat ko'rsatiladi
- [ ] **Yangi qo'shish** tugmasi ishlaydi
- [ ] Forma to'ldiriladi va saqlash
- [ ] **Tasdiqlash** tugmasi ishlaydi
- [ ] **Tahrirlash** ishlaydi
- [ ] **Parolni tiklash** ishlaydi
- [ ] **O'chirish** ishlaydi
- [ ] Qidiruv va filtrlar ishlaydi

## 4. Talabalar (`/admin/students`)
- [ ] Ro'yxat ko'rsatiladi (3 ta talaba)
- [ ] **Bitta qo'shish** - forma ishlaydi
- [ ] **Guruh qo'shish** - import sahifasi ochiladi
  - [ ] Namuna fayl yuklab olinadi
  - [ ] CSV fayl yuklash ishlaydi
  - [ ] Import jarayoni ishlaydi
- [ ] **Davomat** sahifasi ochiladi
  - [ ] Talabalar ro'yxati
  - [ ] Davomat belgilash (modal)
  - [ ] Statistika yangilanadi
  - [ ] PDF export (tugma mavjud)
  - [ ] Excel export (tugma mavjud)
- [ ] **Ko'rish** - talaba ma'lumotlari
- [ ] **Tahrirlash** ishlaydi
- [ ] **O'chirish** ishlaydi
- [ ] Qidiruv va filtrlar ishlaydi

## 5. Foydalanuvchilar (`/admin/users`)
- [ ] Ro'yxat ko'rsatiladi
- [ ] **Yangi qo'shish** ishlaydi
- [ ] **Tahrirlash** ishlaydi
- [ ] **Bloklash/Faollashtirish** ishlaydi
  - [ ] Adminni bloklash mumkin emas
  - [ ] O'zini bloklash mumkin emas
- [ ] **O'chirish** ishlaydi
  - [ ] O'zini o'chirish mumkin emas
  - [ ] Adminni o'chirish mumkin emas
- [ ] Qidiruv va filtrlar ishlaydi

## 6. Hisobotlar (`/admin/reports`)
- [ ] Sahifa ochiladi
- [ ] Asosiy statistika kartalar:
  - [ ] Jami talabalar
  - [ ] Tashkilotlar
  - [ ] Faol rahbarlar
  - [ ] Bugungi davomat
- [ ] **Diagrammalar**:
  - [ ] Talabalar holati (Donut chart)
  - [ ] Haftalik davomat (Bar chart)
- [ ] Guruhlar bo'yicha statistika jadvali
- [ ] Amaliyot muddati statistikasi
- [ ] Top tashkilotlar ro'yxati

## 7. Faoliyat Jurnali (`/admin/activity-logs`)
- [ ] Sahifa ochiladi
- [ ] Barcha harakatlar ko'rsatiladi
- [ ] Rangli badge'lar (login, create, update, delete)
- [ ] Foydalanuvchi ma'lumotlari
- [ ] Vaqt ko'rsatiladi
- [ ] IP manzil ko'rsatiladi
- [ ] Statistika kartalar
- [ ] Foydalanuvchilar bo'yicha faoliyat
- [ ] Pagination ishlaydi

## 8. Sozlamalar (`/admin/settings`)
- [ ] Sahifa ochiladi
- [ ] **Profil sozlamalari**:
  - [ ] Ism o'zgartirish
  - [ ] Email o'zgartirish
  - [ ] Parol o'zgartirish (joriy parol kerak)
  - [ ] Saqlash ishlaydi
- [ ] **Tizim sozlamalari**:
  - [ ] Tizim nomi o'zgartirish
  - [ ] Til tanlash (UZ/RU/EN)
  - [ ] Qorong'i rejim (checkbox)
  - [ ] Saqlash ishlaydi
- [ ] **Telegram sozlamalari**:
  - [ ] Bot Token kiritish
  - [ ] Chat ID kiritish
  - [ ] Saqlash ishlaydi
- [ ] **Backup**:
  - [ ] Backup yaratish tugmasi
  - [ ] SQL fayl yaratiladi
  - [ ] Telegram'ga yuboriladi (agar sozlangan bo'lsa)
- [ ] Server ma'lumotlari ko'rsatiladi
- [ ] Database ma'lumotlari
- [ ] Xavfsizlik ma'lumotlari

## 9. Sidebar Navigation
- [ ] Logo ko'rsatiladi
- [ ] Dashboard havolasi ishlaydi
- [ ] Rahbarlar menyusi ochiladi
- [ ] Talabalar submenu ishlaydi
- [ ] Hisobotlar havolasi
- [ ] Faoliyat jurnali havolasi
- [ ] Sozlamalar havolasi
- [ ] Menyu pin/unpin ishlaydi
- [ ] Hover effektlar ishlaydi

## 10. Xavfsizlik
- [ ] Admin middleware ishlaydi
- [ ] Approved middleware ishlaydi
- [ ] Adminni bloklash mumkin emas
- [ ] O'zini o'chirish mumkin emas
- [ ] Parol hash qilinadi
- [ ] CSRF himoyasi ishlaydi

## 11. Dizayn va UX
- [ ] Responsive (mobil, planshet, desktop)
- [ ] Gradient kartalar chiroyli
- [ ] Ikonlar to'g'ri
- [ ] Alert xabarlari ko'rsatiladi
- [ ] Loading animatsiyalar
- [ ] Hover effektlar
- [ ] Modal'lar ishlaydi
- [ ] Form validatsiya

## 12. Database Relationships
- [ ] Student -> Organization (belongsTo)
- [ ] Student -> Attendances (hasMany)
- [ ] Organization -> Students (hasMany)
- [ ] User -> ActivityLogs (hasMany)
- [ ] ActivityLog -> User (belongsTo)

## Test natijasi:
- **Jami testlar:** 100+
- **O'tgan:** ___
- **Muvaffaqiyatsiz:** ___
- **Izohlar:** ___

---

## Tezkor test buyruqlari:

```bash
# Ma'lumotlarni tekshirish
php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Students: ' . App\Models\Student::count() . PHP_EOL;
echo 'Organizations: ' . App\Models\Organization::count() . PHP_EOL;
echo 'Attendances: ' . App\Models\Attendance::count() . PHP_EOL;
echo 'Activity Logs: ' . App\Models\ActivityLog::count() . PHP_EOL;
"

# Route'larni ko'rish
php artisan route:list --path=admin

# Cache tozalash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear

# Server ishga tushirish
php artisan serve
```

## Login ma'lumotlari:
- **Admin:** `admin` / `admin123`
- **Talaba:** `aliyev_vali` / `student123`
