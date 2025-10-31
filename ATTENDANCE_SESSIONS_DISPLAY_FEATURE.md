# Davomat Seanslar Sonini Ko'rsatish

## 🎯 Maqsad

Guruhlar va Davomat sahifalarida kuniga necha marta davomat olinishi haqida ma'lumotni ko'rsatish.

---

## ✨ Amalga Oshirilgan

### 1. Guruhlar Sahifasida Info Card

**Fayl:** [`resources/views/admin/catalogs/groups.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\catalogs\groups.blade.php)

**Joylashuv:** Sahifa yuqori qismida, sarlavha va tugmalar ostida

```blade
@php
    $dailySessions = \App\Models\Setting::get('daily_attendance_sessions', 3);
@endphp
<div class="alert alert-info d-flex align-items-center mb-3" role="alert">
    <i class="fa fa-calendar-check fs-4 me-3"></i>
    <div>
        <strong>Davomat sozlamalari:</strong>
        Kuniga <span class="badge bg-primary fs-6 mx-1">{{ $dailySessions }}</span> marta davomat qabul qilinadi
        <small class="d-block text-muted mt-1">
            <i class="fa fa-info-circle me-1"></i>Sozlamalardan o'zgartirish mumkin
        </small>
    </div>
</div>
```

**Ko'rinishi:**
```
┌────────────────────────────────────────────────────────────┐
│ 📅  Davomat sozlamalari: Kuniga [3] marta davomat qabul    │
│     qilinadi                                               │
│     ℹ️ Sozlamalardan o'zgartirish mumkin                    │
└────────────────────────────────────────────────────────────┘
```

---

### 2. Davomat Sahifasida Info Card

**Fayl:** [`resources/views/admin/students/attendance.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\students\attendance.blade.php)

**Joylashuv:** Sahifa yuqori qismida, sarlavha ostida

```blade
@php
    $dailySessions = \App\Models\Setting::get('daily_attendance_sessions', 3);
@endphp
<div class="alert alert-info d-flex align-items-center mb-3" role="alert">
    <i class="fa fa-calendar-check fs-4 me-3"></i>
    <div class="flex-grow-1">
        <strong>Davomat sozlamalari:</strong>
        Kuniga <span class="badge bg-primary fs-6 mx-1">{{ $dailySessions }}</span> marta davomat qabul qilinadi
        <small class="d-block text-muted mt-1">
            <i class="fa fa-info-circle me-1"></i>Sozlamalardan o'zgartirish mumkin (Admin → Sozlamalar → Tizim sozlamalari)
        </small>
    </div>
</div>
```

**Ko'rinishi:**
```
┌────────────────────────────────────────────────────────────┐
│ 📅  Davomat sozlamalari: Kuniga [3] marta davomat qabul    │
│     qilinadi                                               │
│     ℹ️ Sozlamalardan o'zgartirish mumkin                    │
│     (Admin → Sozlamalar → Tizim sozlamalari)               │
└────────────────────────────────────────────────────────────┘
```

---

## 🎨 Dizayn Xususiyatlari

### Alert Card:
- **Turi:** `alert-info` (ko'k rang)
- **Layout:** Flexbox (icon + content)
- **Icon:** `fa-calendar-check` (katta o'lchamda)
- **Badge:** Primary rang, seanslar soni ko'rsatiladi
- **Hint Text:** Kichik, kulrang matn

### Dinamik Qiymat:
```php
$dailySessions = \App\Models\Setting::get('daily_attendance_sessions', 3);
```

**Default:** 3 (agar sozlamada o'rnatilmagan bo'lsa)

---

## 📋 Misollar

### Misol 1: Default (3 seans)
```
Setting: daily_attendance_sessions = 3

Ko'rinishi:
┌────────────────────────────────────────┐
│ 📅 Davomat sozlamalari: Kuniga [3]     │
│    marta davomat qabul qilinadi        │
└────────────────────────────────────────┘
```

### Misol 2: 1 seans
```
Setting: daily_attendance_sessions = 1

Ko'rinishi:
┌────────────────────────────────────────┐
│ 📅 Davomat sozlamalari: Kuniga [1]     │
│    marta davomat qabul qilinadi        │
└────────────────────────────────────────┘
```

### Misol 3: 5 seans
```
Setting: daily_attendance_sessions = 5

Ko'rinishi:
┌────────────────────────────────────────┐
│ 📅 Davomat sozlamalari: Kuniga [5]     │
│    marta davomat qabul qilinadi        │
└────────────────────────────────────────┘
```

---

## 🔗 Bog'liq Funksiyalar

### 1. Sozlamalar Sahifasi
**Fayl:** `resources/views/admin/settings.blade.php`

Admin sozlamalardan qiymatni o'zgartirishi mumkin:
- **Manzil:** `/admin/settings`
- **Bo'lim:** Tizim sozlamalari
- **Maydon:** "Kuniga davomat necha marta qabul qilinadi"
- **Qiymat:** 1 dan 10 gacha

---

## ✅ Foyda

1. **Shaffoflik** - Foydalanuvchilar sozlamalardan xabardor
2. **Qulaylik** - Sahifadan chiqmasdan ma'lumot olish
3. **Yo'naltirish** - Sozlamalar sahifasiga havola
4. **Professional ko'rinish** - Chiroyli info card

---

## 📍 Sahifalar

### 1. Guruhlar sahifasi:
```
/admin/catalogs/groups
```

### 2. Davomat sahifasi:
```
/admin/students/attendance
```

### 3. Sozlamalar:
```
/admin/settings
```

---

## 🎯 Keyingi Qadamlar

Hozirgi holatda faqat ma'lumot ko'rsatiladi. Keyinchalik quyidagilar amalga oshirilishi mumkin:

1. **Dinamik seans ustunlari** - Jadvalda seanslar soni dinamik bo'lishi
2. **Dinamik dropdown** - Seans tanlashda faqat mavjud seanslar
3. **Seans vaqtlarini sozlash** - Har bir seans uchun vaqt belgilash
4. **Rang kodlash** - Har xil seanslar uchun turli ranglar

---

**📅 Yaratildi:** 2025-10-28  
**✅ Holat:** Tugallandi  
**🎯 Maqsad:** Foydalanuvchilarni davomat sozlamalari haqida xabardor qilish
