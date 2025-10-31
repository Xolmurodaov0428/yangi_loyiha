# Dinamik Davomat Seanslar - Guruhga Asoslangan

## 🎯 Maqsad

Davomat sahifasida har bir guruh uchun o'ziga xos davomat seanslar sonini ko'rsatish. Agar guruh `daily_sessions = 2` bo'lsa, faqat 2 ta ustun ko'rsatiladi.

---

## ✨ Amalga Oshirilgan O'zgarishlar

### 1. Dinamik Jadval Ustunlari

**Fayl:** [`resources/views/admin/students/attendance.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\students\attendance.blade.php)

**Oldingi Kod (Statik):**
```blade
<thead class="table-light">
    <tr>
        <th>Talaba</th>
        <th>Guruh</th>
        <th>1-Seans<br><small class="text-muted">09:00</small></th>
        <th>2-Seans<br><small class="text-muted">13:00</small></th>
        <th>3-Seans<br><small class="text-muted">16:00</small></th>
        <th>Harakatlar</th>
    </tr>
</thead>
```

**Yangi Kod (Dinamik):**
```blade
@php
    $dailySessions = $selectedGroup->daily_sessions ?? 3;
    $sessionTimes = [
        1 => '09:00',
        2 => '13:00',
        3 => '16:00',
        4 => '18:00',
        5 => '20:00',
        6 => '22:00',
    ];
@endphp

<thead class="table-light">
    <tr>
        <th>Talaba</th>
        <th>Guruh</th>
        @for($i = 1; $i <= $dailySessions; $i++)
            <th>{{ $i }}-Seans<br><small class="text-muted">{{ $sessionTimes[$i] ?? '00:00' }}</small></th>
        @endfor
        <th>Harakatlar</th>
    </tr>
</thead>
```

**Natija:**
- Guruhda 2 seans bo'lsa → 2 ta ustun
- Guruhda 3 seans bo'lsa → 3 ta ustun  
- Guruhda 5 seans bo'lsa → 5 ta ustun

---

### 2. Dinamik Ma'lumot Qatorlari

**Oldingi Kod:**
```blade
@for($i = 1; $i <= 3; $i++)
    <td>...</td>
@endfor
```

**Yangi Kod:**
```blade
@for($i = 1; $i <= $dailySessions; $i++)
    @php
        $sessionKey = 'session_' . $i;
        $session = $student->sessions[$sessionKey] ?? null;
        $status = $session['status'] ?? 'absent';
    @endphp
    <td>...</td>
@endfor
```

---

### 3. Yangilangan Info Card

**Oldingi Kod (Global):**
```blade
<div class="alert alert-info">
    Davomat rejimi: Kuniga 3 marta davomat olinadi
</div>
```

**Yangi Kod (Guruhga Asoslangan):**
```blade
@if($selectedGroup)
    @php
        $dailySessions = $selectedGroup->daily_sessions ?? 3;
    @endphp
    <div class="alert alert-info">
        <strong>{{ $selectedGroup->name }} guruhi:</strong>
        Kuniga <span class="badge bg-primary">{{ $dailySessions }}</span> marta davomat olinadi
    </div>
@endif
```

**Xususiyatlar:**
- Faqat guruh tanlanganda ko'rsatiladi
- Guruh nomini ko'rsatadi
- O'sha guruh uchun sozlangan seans sonini ko'rsatadi

---

### 4. Dinamik Session Dropdown

**JavaScript funksiyasi qo'shildi:**

```javascript
function populateSessionDropdown() {
    @if($selectedGroup)
        const dailySessions = {{ $selectedGroup->daily_sessions ?? 3 }};
        const sessionTimes = {
            1: '09:00',
            2: '13:00',
            3: '16:00',
            4: '18:00',
            5: '20:00',
            6: '22:00'
        };
        
        const sessionSelect = document.getElementById('session_select');
        sessionSelect.innerHTML = ''; // Clear existing options
        
        for (let i = 1; i <= dailySessions; $i++) {
            const option = document.createElement('option');
            option.value = 'session_' + i;
            option.textContent = i + '-Seans (' + (sessionTimes[i] || '00:00') + ')';
            sessionSelect.appendChild(option);
        }
    @endif
}

// Sahifa yuklanganida chaqiriladi
document.addEventListener('DOMContentLoaded', function() {
    populateSessionDropdown();
});
```

**Natija:**
- Modal ochilganda, faqat guruhga tegishli seanslar ko'rsatiladi
- 2 seans bo'lsa → faqat 1-Seans va 2-Seans tanlash mumkin

---

## 📊 Misol Ko'rinishlar

### Misol 1: 2 Seansli Guruh

**Guruh:** 221-20 (Kechki)  
**Daily Sessions:** 2

**Jadval:**
```
┌──────────┬────────┬─────────┬─────────┬───────────┐
│ Talaba   │ Guruh  │ 1-Seans │ 2-Seans │ Harakatlar│
│          │        │ 09:00   │ 13:00   │           │
├──────────┼────────┼─────────┼─────────┼───────────┤
│ Alisher  │ 221-20 │ ✓ Keldi │ ✗ Kelmadi│ 👁️ ✓    │
└──────────┴────────┴─────────┴─────────┴───────────┘
```

**Info Card:**
```
ℹ️ 221-20 (Kechki) guruhi: Kuniga [2] marta davomat olinadi
```

---

### Misol 2: 3 Seansli Guruh (Default)

**Guruh:** Dasturlash 1-guruh  
**Daily Sessions:** 3

**Jadval:**
```
┌──────────┬──────────────┬─────────┬─────────┬─────────┬───────────┐
│ Talaba   │ Guruh        │ 1-Seans │ 2-Seans │ 3-Seans │ Harakatlar│
│          │              │ 09:00   │ 13:00   │ 16:00   │           │
├──────────┼──────────────┼─────────┼─────────┼─────────┼───────────┤
│ Nematova │ Dasturlash   │ ✓ Keldi │ ✓ Keldi │ ✓ Keldi │ 👁️ ✓    │
└──────────┴──────────────┴─────────┴─────────┴─────────┴───────────┘
```

**Info Card:**
```
ℹ️ Dasturlash 1-guruh guruhi: Kuniga [3] marta davomat olinadi
```

---

### Misol 3: 5 Seansli Intensiv Guruh

**Guruh:** Bootcamp-2025  
**Daily Sessions:** 5

**Jadval:**
```
┌────────┬───────────┬───────┬───────┬───────┬───────┬───────┬──────────┐
│ Talaba │ Guruh     │ 1-S   │ 2-S   │ 3-S   │ 4-S   │ 5-S   │ Harakatlar│
│        │           │ 09:00 │ 13:00 │ 16:00 │ 18:00 │ 20:00 │          │
├────────┼───────────┼───────┼───────┼───────┼───────┼───────┼──────────┤
│ Jasur  │ Bootcamp  │ ✓     │ ✓     │ ✓     │ ✓     │ ✓     │ 👁️ ✓    │
└────────┴───────────┴───────┴───────┴───────┴───────┴───────┴──────────┘
```

---

## 🎯 Afzalliklar

### 1. **Moslashuvchanlik**
- Har bir guruh o'z jadvaliga ega
- Kechki/kunduzgi guruhlar farq qiladi
- Intensiv dasturlar ko'proq seans olishi mumkin

### 2. **Soddalik**
- Ortiqcha ustunlar ko'rsatilmaydi
- Faqat kerakli ma'lumotlar
- Toza va tushunarli interfeys

### 3. **Aniqlik**
- Guruh nomini ko'rsatadi
- O'sha guruh uchun seanslar sonini aniq bildiradi
- Aralashmaslik xavfi yo'q

### 4. **Avtomatik Yangilanish**
- Guruh almashtirilsa, jadval avtomatik yangilanadi
- Modal dropdown ham avtomatik to'ldiriladi
- Qo'lda hech narsa o'zgartirish shart emas

---

## 🔧 Texnik Detallar

### PHP O'zgaruvchilari:
```php
$dailySessions = $selectedGroup->daily_sessions ?? 3;  // Guruhdan olinadi
$sessionTimes = [1 => '09:00', 2 => '13:00', ...];      // Vaqtlar
```

### JavaScript O'zgaruvchilari:
```javascript
const dailySessions = {{ $selectedGroup->daily_sessions ?? 3 }};
const sessionTimes = { 1: '09:00', 2: '13:00', ... };
```

### Loop Logikasi:
```php
@for($i = 1; $i <= $dailySessions; $i++)
    // Har bir seans uchun ustun/qator
@endfor
```

---

## ✅ Natija

| Guruh Daily Sessions | Jadvalda Ko'rsatiladigan Ustunlar | Modal Dropdownda |
|----------------------|-----------------------------------|------------------|
| 1 marta | 1-Seans | 1 ta option |
| 2 marta | 1-Seans, 2-Seans | 2 ta option |
| 3 marta | 1-Seans, 2-Seans, 3-Seans | 3 ta option |
| 4 marta | 1-4 Seanslar | 4 ta option |
| 5 marta | 1-5 Seanslar | 5 ta option |
| 6 marta | 1-6 Seanslar | 6 ta option |

---

## 🔄 Ishlash Jarayoni

1. **Admin guruh qo'shadi** → `daily_sessions = 2` belgilaydi
2. **Davomat sahifasiga kiradi** → Guruhni tanlaydi
3. **Info card ko'rsatiladi** → "221-20 guruhi: Kuniga **2** marta davomat olinadi"
4. **Jadval ko'rsatiladi** → Faqat 2 ta ustun (1-Seans, 2-Seans)
5. **Davomat belgilaydi** → Modal'da faqat 2 ta seans tanlash mumkin

---

**📅 Yaratildi:** 2025-10-28  
**✅ Holat:** To'liq amalga oshirildi  
**🎯 Natija:** Har bir guruh o'z davomat rejimiga ega!
