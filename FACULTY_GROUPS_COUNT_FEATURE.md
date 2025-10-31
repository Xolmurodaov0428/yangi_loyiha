# Fakultetlarda Guruhlar Soni (Groups Count in Faculties)

## 🎯 Maqsad (Objective)

Fakultetlar ro'yxatida har bir fakultetga tegishli **guruhlar sonini** ko'rsatish.

**Show the number of groups belonging to each faculty in the faculties list.**

---

## ✨ Yangi Ustun (New Column)

### Before:
```
| # | Fakultet nomi | Kod  | Tavsif | Talabalar | Holat | Amallar |
|---|---------------|------|--------|-----------|-------|---------|
```

### After:
```
| # | Fakultet nomi | Kod  | Tavsif | Guruhlar | Talabalar | Holat | Amallar |
|---|---------------|------|--------|----------|-----------|-------|---------|
```

**Yangi "Guruhlar" ustuni qo'shildi!** 🆕

---

## 🔧 Amalga Oshirilgan O'zgarishlar (Implementation Changes)

### 1. Controller Update

**File:** [`app/Http/Controllers/Admin/CatalogController.php`](c:\xampp\htdocs\amaliyot\app\Http\Controllers\Admin\CatalogController.php)

**Before:**
```php
public function faculties()
{
    $faculties = Faculty::withCount('students')->paginate(15);  // ❌ Only students
    return view('admin.catalogs.faculties', compact('faculties'));
}
```

**After:**
```php
public function faculties()
{
    $faculties = Faculty::withCount(['students', 'groups'])->paginate(15);  // ✅ Both students and groups
    return view('admin.catalogs.faculties', compact('faculties'));
}
```

**Explanation:**
- `withCount(['students', 'groups'])` - Laravel relationship count for both
- Creates two attributes:
  - `students_count` - Number of students
  - `groups_count` - Number of groups

---

### 2. View Update

**File:** [`resources/views/admin/catalogs/faculties.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\catalogs\faculties.blade.php)

#### a) Table Header:
```blade
<thead class="table-light">
    <tr>
        <th>#</th>
        <th>Fakultet nomi</th>
        <th>Kod</th>
        <th>Tavsif</th>
        <th>Guruhlar</th>        <!-- ✅ NEW COLUMN -->
        <th>Talabalar</th>
        <th>Holat</th>
        <th class="text-end">Amallar</th>
    </tr>
</thead>
```

#### b) Table Body:
```blade
<td>{{ Str::limit($faculty->description ?? '-', 50) }}</td>
<td><span class="badge bg-primary">{{ $faculty->groups_count ?? 0 }}</span></td>  <!-- ✅ NEW -->
<td><span class="badge bg-info">{{ $faculty->students_count ?? 0 }}</span></td>
```

#### c) Empty State colspan:
```blade
<!-- Updated colspan from 7 to 8 -->
<td colspan="8" class="text-center text-muted py-4">
    <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
    Hozircha fakultetlar yo'q
</td>
```

---

## 📊 Test Natijalari (Test Results)

**Test File:** [`test_faculty_groups_count.php`](c:\xampp\htdocs\amaliyot\test_faculty_groups_count.php)

### Output:
```
Fakultet            Kod       Guruhlar    Talabalar
----------------------------------------------------
Fizika              FIZI      1 ta        2 ta
Informatika         IT        1 ta        0 ta
Iqtisodiyot         ECON      0 ta        0 ta
Matematika          MATH      2 ta        2 ta
----------------------------------------------------
JAMI:                         4 ta        4 ta
```

### Detallı Ma'lumot:

**📚 Fizika (FIZI)**
- Guruhlar: 1 ta
- Talabalar: 2 ta
- Guruhlar ro'yxati:
  - 223-20 (2 talaba)

**📚 Informatika (IT)**
- Guruhlar: 1 ta
- Talabalar: 0 ta
- Guruhlar ro'yxati:
  - 221-20 (0 talaba)

**📚 Iqtisodiyot (ECON)**
- Guruhlar: 0 ta
- Talabalar: 0 ta
- (Guruhlar yo'q)

**📚 Matematika (MATH)**
- Guruhlar: 2 ta
- Talabalar: 2 ta
- Guruhlar ro'yxati:
  - 222-20 (2 talaba)
  - 221-20 (0 talaba)

---

## 🎨 UI Display

### Badge Colors:
- **Guruhlar** - Primary badge (blue): `bg-primary`
- **Talabalar** - Info badge (cyan): `bg-info`

### Visual Example:
```
┌──────────────┬──────┬──────────┬──────────┬───────────┬─────────┐
│ Fakultet     │ Kod  │ Tavsif   │ Guruhlar │ Talabalar │ Holat   │
├──────────────┼──────┼──────────┼──────────┼───────────┼─────────┤
│ Informatika  │ IT   │ Komp...  │    1     │     0     │  Faol   │
│ Matematika   │ MATH │ Amal...  │    2     │     2     │  Faol   │
│ Fizika       │ FIZI │ -        │    1     │     2     │  Faol   │
│ Iqtisodiyot  │ ECON │ Iqti...  │    0     │     0     │  Faol   │
└──────────────┴──────┴──────────┴──────────┴───────────┴─────────┘
```

---

## 💡 Foydali Ma'lumotlar (Useful Information)

### Fakultet-Guruh Relationship:

```php
// Faculty model
public function groups()
{
    return $this->hasMany(Group::class, 'faculty', 'name');
}
```

**Important:** 
- Relationship uses `faculty` (text field) not foreign key
- `faculty` field in groups table matches `name` field in faculties table

### Query Explanation:

```php
Faculty::withCount(['students', 'groups'])
```

**Generates SQL:**
```sql
SELECT 
    faculties.*,
    (SELECT COUNT(*) FROM students WHERE students.faculty = faculties.name) as students_count,
    (SELECT COUNT(*) FROM groups WHERE groups.faculty = faculties.name) as groups_count
FROM faculties
```

---

## 🔍 Qo'shimcha Imkoniyatlar (Additional Features)

### 1. Click to View Groups (Future Enhancement)
```blade
<td>
    <a href="{{ route('admin.catalogs.groups', ['faculty' => $faculty->name]) }}" 
       class="badge bg-primary text-decoration-none">
        {{ $faculty->groups_count ?? 0 }}
    </a>
</td>
```

### 2. Show Empty Faculties Warning
```blade
@if ($faculty->groups_count == 0)
    <span class="badge bg-warning">Guruh yo'q</span>
@else
    <span class="badge bg-primary">{{ $faculty->groups_count }}</span>
@endif
```

### 3. Tooltip with Details
```blade
<td>
    <span class="badge bg-primary" 
          data-bs-toggle="tooltip" 
          title="Bu fakultetda {{ $faculty->groups_count }} ta guruh mavjud">
        {{ $faculty->groups_count ?? 0 }}
    </span>
</td>
```

---

## 📈 Statistika (Statistics)

### Current Data:
- **Total Faculties:** 4
- **Total Groups:** 4
- **Total Students:** 4

### Distribution:
| Fakultet    | Guruhlar | Talabalar | Avg Students/Group |
|-------------|----------|-----------|-------------------|
| Matematika  | 2        | 2         | 1.0               |
| Fizika      | 1        | 2         | 2.0               |
| Informatika | 1        | 0         | 0.0               |
| Iqtisodiyot | 0        | 0         | -                 |

---

## ✅ Xulosa (Conclusion)

### What Changed:
✅ Added `withCount('groups')` to controller  
✅ Added "Guruhlar" column to table header  
✅ Display groups count with blue badge  
✅ Updated empty state colspan  
✅ Tested and verified working  

### Benefits:
- 👁️ **Better visibility** - See groups count at a glance
- 📊 **Better analytics** - Understand faculty size
- 🎯 **Better management** - Identify empty faculties
- 🚀 **Better UX** - One place for all faculty info

---

## 🔗 Tegishli Fayllar (Related Files)

1. **Controller:** [`app/Http/Controllers/Admin/CatalogController.php`](c:\xampp\htdocs\amaliyot\app\Http\Controllers\Admin\CatalogController.php)
2. **View:** [`resources/views/admin/catalogs/faculties.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\catalogs\faculties.blade.php)
3. **Model:** [`app/Models/Faculty.php`](c:\xampp\htdocs\amaliyot\app\Models\Faculty.php)
4. **Test:** [`test_faculty_groups_count.php`](c:\xampp\htdocs\amaliyot\test_faculty_groups_count.php)

---

**📅 Implemented:** 2025-10-28  
**✅ Status:** Complete & Tested  
**🎯 Result:** Faculties now show group counts!
