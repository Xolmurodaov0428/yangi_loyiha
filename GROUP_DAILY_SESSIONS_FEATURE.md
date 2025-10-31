# Guruh Qo'shganda Kunlik Davomat Seanslarini Belgilash

## 🎯 Maqsad

Har bir guruh qo'shilganda kuniga necha marta davomat olinishini belgilash imkoniyati.

---

## ✨ Amalga Oshirilgan O'zgarishlar

### 1. Database Migration

**Fayl:** [`database/migrations/2025_10_28_155626_add_daily_sessions_to_groups_table.php`](c:\xampp\htdocs\amaliyot\database\migrations\2025_10_28_155626_add_daily_sessions_to_groups_table.php)

```php
Schema::table('groups', function (Blueprint $table) {
    $table->integer('daily_sessions')
          ->default(3)
          ->after('faculty')
          ->comment('Kuniga necha marta davomat olinadi');
});
```

**Xususiyatlar:**
- **Turi:** INTEGER
- **Default:** 3
- **Nullable:** NO
- **Joylashuv:** `faculty` ustunidan keyin

---

### 2. Group Model O'zgarishi

**Fayl:** [`app/Models/Group.php`](c:\xampp\htdocs\amaliyot\app\Models\Group.php)

```php
protected $fillable = [
    'name',
    'faculty',
    'supervisor_id',
    'student_count',
    'is_active',
    'daily_sessions',  // ← Yangi
];
```

---

### 3. Yangi Guruh Qo'shish Modali

**Fayl:** [`resources/views/admin/catalogs/groups.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\catalogs\groups.blade.php)

**Qo'shilgan maydon:**

```blade
<div class="mb-3">
    <label class="form-label">
        <i class="fa fa-calendar-check me-1"></i>Kuniga necha marta davomat olinadi <span class="text-danger">*</span>
    </label>
    <select name="daily_sessions" class="form-select" required>
        <option value="1">1 marta</option>
        <option value="2">2 marta</option>
        <option value="3" selected>3 marta (tavsiya etiladi)</option>
        <option value="4">4 marta</option>
        <option value="5">5 marta</option>
        <option value="6">6 marta</option>
    </select>
    <small class="text-muted">Bu guruhda kuniga necha marta davomat olinishini belgilaydi</small>
</div>
```

**Ko'rinishi:**

```
┌────────────────────────────────────────────┐
│ 📅 Kuniga necha marta davomat olinadi *    │
│ ┌────────────────────────────────────────┐ │
│ │ 3 marta (tavsiya etiladi)        ▼    │ │
│ └────────────────────────────────────────┘ │
│ Bu guruhda kuniga necha marta davomat      │
│ olinishini belgilaydi                      │
└────────────────────────────────────────────┘
```

---

### 4. Guruhni Tahrirlash Modali

Xuddi shu maydon tahrirlash modaliga ham qo'shildi:

```blade
<div class="mb-3">
    <label class="form-label">
        <i class="fa fa-calendar-check me-1"></i>Kuniga necha marta davomat olinadi <span class="text-danger">*</span>
    </label>
    <select name="daily_sessions" id="editGroupDailySessions" class="form-select" required>
        <option value="1">1 marta</option>
        <option value="2">2 marta</option>
        <option value="3">3 marta (tavsiya etiladi)</option>
        <option value="4">4 marta</option>
        <option value="5">5 marta</option>
        <option value="6">6 marta</option>
    </select>
    <small class="text-muted">Bu guruhda kuniga necha marta davomat olinishini belgilaydi</small>
</div>
```

---

### 5. JavaScript O'zgarishlari

**editGroup funksiyasi yangilandi:**

```javascript
function editGroup(id, name, faculty, isActive, dailySessions) {
    document.getElementById('editGroupName').value = name;
    document.getElementById('editGroupFaculty').value = faculty || '';
    
    // Set daily sessions
    document.getElementById('editGroupDailySessions').value = dailySessions || 3;
    
}
```

**Table button'da parameter qo'shildi:**

```blade
<button class="btn btn-sm btn-outline-primary" 
        onclick="editGroup({{ $group->id }}, '{{ $group->name }}', '{{ $group->faculty }}', {{ $group->is_active ? 'true' : 'false' }}, {{ $group->daily_sessions ?? 3 }})"
        data-bs-toggle="modal" 
        data-bs-target="#editGroupModal">
    <i class="fa fa-edit"></i>
</button>
```

---

### 6. Controller O'zgarishlari

**Fayl:** [`app/Http/Controllers/Admin/CatalogController.php`](c:\xampp\htdocs\amaliyot\app\Http\Controllers\Admin\CatalogController.php)

#### storeGroup metodida:

```php
public function storeGroup(Request $request)
{
    $validated = $request->validate([
        'name' => [...],
        'faculty' => 'nullable|string|max:255',
        'daily_sessions' => 'required|integer|min:1|max:10',  // ← Yangi
    ]);

}
```

#### updateGroup metodida:

```php
public function updateGroup(Request $request, $id)
{
    $validated = $request->validate([
        'name' => [...],
        'faculty' => 'nullable|string|max:255',
        'daily_sessions' => 'required|integer|min:1|max:10',  // ← Yangi
    ], [...]);

}
```

---

## 📊 Database Struktura

### `groups` jadvali:

| Ustun | Turi | Default | Null | Izoh |
|-------|------|---------|------|------|
| id | BIGINT | AUTO | NO | Primary key |
| name | VARCHAR(255) | - | NO | Guruh nomi |
| faculty | VARCHAR(255) | NULL | YES | Fakultet |
| daily_sessions | INT | 3 | NO | Kunlik davomat soni |
| student_count | INT | 0 | NO | Talabalar soni |
| is_active | BOOLEAN | 1 | NO | Faol holat |
| created_at | TIMESTAMP | - | YES | Yaratilgan vaqt |
| updated_at | TIMESTAMP | - | YES | Yangilangan vaqt |

---

## 🎯 Foydalanish

### Yangi Guruh Qo'shish:

1. **Guruhlar sahifasiga o'tish:**
   ```
   /admin/catalogs/groups
   ```

2. **"Yangi guruh qo'shish" tugmasini bosish**

3. **Formani to'ldirish:**
   - Guruh nomi: `221-20`
   - Fakultet: `Informatika`
   - **Kuniga necha marta davomat olinadi:** `3 marta` ← Yangi!

4. **Saqlash**

### Guruhni Tahrirlash:

1. **Tahrirlash tugmasini bosish** (✏️ icon)

2. **Davomat seanslarini o'zgartirish:**
   - Dropdown dan boshqa qiymatni tanlash
   - Masalan: `5 marta`

3. **O'zgarishlarni saqlash**

---

## 📋 Misollar

### Misol 1: Kunduzgi guruh (3 marta)

```
Guruh: 221-20
Fakultet: Informatika
Daily Sessions: 3 marta

Davomat vaqtlari:
- 1-Seans: 09:00
- 2-Seans: 13:00
- 3-Seans: 17:00
```

### Misol 2: Kechki guruh (2 marta)

```
Guruh: 221-20 (Kechki)
Fakultet: Informatika
Daily Sessions: 2 marta

Davomat vaqtlari:
- 1-Seans: 18:00
- 2-Seans: 20:00
```

### Misol 3: Qisqa kurs (1 marta)

```
Guruh: Kurs-01
Fakultet: -
Daily Sessions: 1 marta

Davomat vaqti:
- 1-Seans: 10:00
```

### Misol 4: Intensiv dastur (5 marta)

```
Guruh: Bootcamp-2025
Fakultet: IT
Daily Sessions: 5 marta

Davomat vaqtlari:
- 1-Seans: 09:00
- 2-Seans: 11:00
- 3-Seans: 14:00
- 4-Seans: 16:00
- 5-Seans: 18:00
```

---

## ✅ Afzalliklar

1. **Moslashuvchanlik** - Har bir guruh o'z davomat rejimiga ega
2. **Soddalik** - Guruh qo'shishda bir joyda sozlanadi
3. **Aniqlik** - Guruh yaratuvchisi aniq biladi necha marta davomat olinadi
4. **Tahrirlash** - Keyin ham o'zgartirish mumkin
5. **Validatsiya** - 1 dan 10 gacha cheklangan

---

## 🔮 Keyingi Qadamlar

Ushbu sozlamani davomat tizimida ishlatish:

### 1. Davomat Sahifasida:

```php
// Controller
$group = Group::find($group_id);
$dailySessions = $group->daily_sessions;

// Davomat yaratish
for ($i = 1; $i <= $dailySessions; $i++) {
    Attendance::create([
        'student_id' => $student->id,
        'session' => 'session_' . $i,
        // ...
    ]);
}
```

### 2. Jadval Ustunlarini Dinamik Qilish:

```blade
@for ($i = 1; $i <= $selectedGroup->daily_sessions; $i++)
    <th>{{ $i }}-Seans</th>
@endfor
```

### 3. Dropdown'da Dinamik Seanslar:

```blade
@for ($i = 1; $i <= $group->daily_sessions; $i++)
    <option value="session_{{ $i }}">{{ $i }}-Seans</option>
@endfor
```

---

## 📊 Database Qiymatlari

### Mavjud Guruhlar (Migratsiyadan keyin):

Barcha mavjud guruhlar avtomatik ravishda `daily_sessions = 3` qiymatiga ega bo'ladi (default).

### Yangi Qo'shilgan Guruhlar:

Admin tomonidan tanlangan qiymat saqlanadi.

---

**📅 Yaratildi:** 2025-10-28  
**✅ Holat:** Tugallandi va test qilindi  
**🎯 Natija:** Har bir guruh uchun alohida davomat rejimi!
