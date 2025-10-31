# Davomat Seanslarini Sozlash Funksiyasi

## 🎯 Maqsad

Kuniga necha marta davomat qabul qilinishini admin orqali sozlash imkoniyati.

---

## ✨ Qo'shilgan Funksiyalar

### 1. Sozlamalar sahifasida yangi maydon
- **Joylashuv:** Tizim sozlamalari bo'limida
- **Nomi:** "Kuniga davomat necha marta qabul qilinadi"
- **Turi:** Number input
- **Diapazoni:** 1 dan 10 gacha
- **Default qiymat:** 3

---

## 🔧 O'zgarishlar

### 1. View O'zgarishi - Settings Page

**Fayl:** [`resources/views/admin/settings.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\settings.blade.php)

```blade
<div class="mb-3">
  <label class="form-label fw-semibold">
    <i class="fa fa-calendar-check me-1"></i>Kuniga davomat necha marta qabul qilinadi
  </label>
  <input type="number" 
         name="daily_attendance_sessions" 
         class="form-control" 
         value="{{ \App\Models\Setting::get('daily_attendance_sessions', '3') }}" 
         min="1" 
         max="10" 
         required>
  <small class="text-muted">Kuniga necha marta davomat olish mumkin (1 dan 10 gacha)</small>
</div>
```

**Xususiyatlar:**
- Icon bilan label
- Default qiymat: 3
- Min: 1, Max: 10
- Required validation
- Yordam matni

---

### 2. Controller O'zgarishi

**Fayl:** [`app/Http/Controllers/Admin/SettingsController.php`](c:\xampp\htdocs\amaliyot\app\Http\Controllers\Admin\SettingsController.php)

```php
public function updateSystem(Request $request)
{
    $request->validate([
        'app_name' => 'required|string|max:255',
        'app_locale' => 'required|in:uz,ru,en',
        'daily_attendance_sessions' => 'required|integer|min:1|max:10',  // ← Yangi
        'dark_mode' => 'nullable|boolean',
    ]);

    Setting::set('app_name', $request->app_name);
    Setting::set('app_locale', $request->app_locale);
    Setting::set('daily_attendance_sessions', $request->daily_attendance_sessions);  // ← Yangi
    Setting::set('dark_mode', $request->has('dark_mode') ? '1' : '0');

    return back()->with('success', 'Tizim sozlamalari yangilandi!');
}
```

**Validatsiya qoidalari:**
- Required (majburiy)
- Integer (butun son)
- Min: 1
- Max: 10

---

## 📊 Foydalanish

### Admin tarafidan sozlash:

1. **Sozlamalar sahifasiga kirish:**
   ```
   /admin/settings
   ```

2. **Tizim sozlamalari kartasini topish**

3. **"Kuniga davomat necha marta qabul qilinadi" maydonini to'ldirish:**
   - 1 marta kuniga → `1`
   - 2 marta kuniga → `2`
   - 3 marta kuniga → `3` (default)
   - 4 marta kuniga → `4`
   - ...
   - 10 marta kuniga → `10`

4. **Saqlash tugmasini bosish**

---

## 🔮 Keyingi Qadamlar

Ushbu sozlamani davomat tizimida ishlatish uchun quyidagilar amalga oshirilishi kerak:

### 1. Davomat sahifasida dinamik seanslar:

**Hozirgi holat:**
```php
// Hardcoded 3 ta seans
for($session = 1; $session <= 3; $session++) {
    // ...
}
```

**Yangi yechim:**
```php
// Dinamik seanslar soni
$dailySessions = \App\Models\Setting::get('daily_attendance_sessions', 3);

for($session = 1; $session <= $dailySessions; $session++) {
    // ...
}
```

### 2. Dropdown seanslarini dinamik qilish:

**Hozirgi holat:**
```blade
<select name="session" class="form-select" required>
    <option value="session_1">1-Seans (09:00)</option>
    <option value="session_2">2-Seans (13:00)</option>
    <option value="session_3">3-Seans (17:00)</option>
</select>
```

**Yangi yechim:**
```blade
<select name="session" class="form-select" required>
    @php
        $dailySessions = \App\Models\Setting::get('daily_attendance_sessions', 3);
        $sessionTimes = [
            1 => '09:00',
            2 => '13:00',
            3 => '17:00',
            4 => '19:00',
            5 => '21:00',
            // ... boshqa vaqtlar
        ];
    @endphp
    
    @for($i = 1; $i <= $dailySessions; $i++)
        <option value="session_{{ $i }}">
            {{ $i }}-Seans ({{ $sessionTimes[$i] ?? '00:00' }})
        </option>
    @endfor
</select>
```

### 3. Jadval ustunlarini dinamik qilish:

```blade
@php
    $dailySessions = \App\Models\Setting::get('daily_attendance_sessions', 3);
@endphp

<!-- Ustunlar -->
@for($i = 1; $i <= $dailySessions; $i++)
    <th>{{ $i }}-Seans</th>
@endfor
```

---

## 📋 Misollar

### Misol 1: Faqat 1 marta davomat
```
Setting: daily_attendance_sessions = 1

Natija:
- Faqat 1-Seans (09:00) ko'rsatiladi
- Talabalar kuniga 1 marta davomat beradi
```

### Misol 2: 5 marta davomat
```
Setting: daily_attendance_sessions = 5

Natija:
- 1-Seans (09:00)
- 2-Seans (13:00)
- 3-Seans (17:00)
- 4-Seans (19:00)
- 5-Seans (21:00)
```

### Misol 3: Default (3 marta)
```
Setting: daily_attendance_sessions = 3 (yoki o'rnatilmagan)

Natija:
- 1-Seans (09:00)
- 2-Seans (13:00)
- 3-Seans (17:00)
```

---

## ✅ Amalga Oshirildi

- ✅ Sozlamalar sahifasiga maydon qo'shildi
- ✅ Controller validatsiyasi qo'shildi
- ✅ Setting model orqali saqlash
- ✅ Default qiymat: 3

## 🔄 Hali Amalga Oshirilmagan

- ⏳ Davomat sahifasida dinamik seanslar
- ⏳ Dropdown'da dinamik seanslar
- ⏳ Jadval ustunlarini dinamik qilish
- ⏳ Seans vaqtlarini sozlash (ixtiyoriy)

---

## 🎯 Foyda

1. **Moslashuvchanlik** - Har xil tashkilotlar uchun
2. **Soddalik** - Admin bir joydan boshqaradi
3. **Kengaytiriluvchi** - 1 dan 10 gacha istalgan son
4. **Samaradorlik** - Ortiqcha seanslar ko'rsatilmaydi

---

**📅 Yaratildi:** 2025-10-28  
**✅ Holat:** Sozlama qo'shildi, davomat tizimiga integratsiya kerak  
**🎯 Keyingi qadam:** Davomat sahifalarida dinamik seanslarni amalga oshirish
