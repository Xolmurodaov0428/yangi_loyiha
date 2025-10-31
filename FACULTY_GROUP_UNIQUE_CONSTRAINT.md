# Fakultet va Guruh Unique Constraint (Faculty-Group Uniqueness)

## 📋 O'zgarishlar Tavsifi (Changes Summary)

Ushbu yangilanish fakultet ichida guruh nomlarining takrorlanmasligini ta'minlaydi, lekin har xil fakultetlarda bir xil nomli guruhlar bo'lishiga ruxsat beradi.

**This update ensures group names are unique within each faculty, but allows the same group name across different faculties.**

---

## 🎯 Maqsad (Objective)

### Oldingi holat (Before):
- Guruh nomlari **butun tizimda** unikal bo'lishi kerak edi
- "221-20" guruhi faqat **bitta fakultetda** mavjud bo'lishi mumkin edi
- Group names were **globally unique** across all faculties
- "221-20" could only exist in **one faculty**

### Hozirgi holat (After):
- Guruh nomlari **fakultet ichida** unikal
- "221-20" guruhi **har bir fakultetda** mavjud bo'lishi mumkin
- Group names are **unique per faculty**
- "221-20" can exist in **every faculty**

---

## 🔧 Amalga oshirilgan o'zgarishlar (Implemented Changes)

### 1. **Database Migration**
**File:** `database/migrations/2025_10_28_120050_add_unique_index_to_groups_table.php`

```php
// Composite unique index qo'shildi
$table->unique(['name', 'faculty'], 'groups_name_faculty_unique');
```

**Natija:**
- `name` va `faculty` kombinatsiyasi unikal bo'lishi kerak
- Same `name` + different `faculty` = ✅ Allowed
- Same `name` + same `faculty` = ❌ Not allowed

---

### 2. **Controller Validation Updates**
**File:** `app/Http/Controllers/Admin/CatalogController.php`

#### a) `storeGroup()` metodi:
```php
'name' => [
    'required',
    'string',
    'max:255',
    Rule::unique('groups')->where(function ($query) use ($request) {
        return $query->where('faculty', $request->faculty);
    }),
],
```

#### b) `updateGroup()` metodi:
```php
'name' => [
    'required',
    'string',
    'max:255',
    Rule::unique('groups')->where(function ($query) use ($request) {
        return $query->where('faculty', $request->faculty);
    })->ignore($id),
],
```

#### c) `importGroups()` metodi:
```php
// Manual check before creating
$exists = Group::where('name', $name)
    ->where('faculty', $faculty)
    ->exists();

if ($exists) {
    $errors[] = "Guruh '{$name}' '{$faculty}' fakultetida allaqachon mavjud";
    continue;
}
```

---

## 📊 Misollar (Examples)

### ✅ Ruxsat etilgan (Allowed):

| Guruh nomi | Fakultet     | Status |
|------------|--------------|--------|
| 221-20     | Informatika  | ✅ OK  |
| 221-20     | Matematika   | ✅ OK  |
| 221-20     | Fizika       | ✅ OK  |
| 222-20     | Informatika  | ✅ OK  |

### ❌ Ruxsat etilmagan (Not Allowed):

| Guruh nomi | Fakultet     | Status                    |
|------------|--------------|---------------------------|
| 221-20     | Informatika  | ✅ Already exists         |
| 221-20     | Informatika  | ❌ DUPLICATE - Not allowed|

---

## 🧪 Test Natijalari (Test Results)

```
✓ Har xil fakultetlarda bir xil nomli guruhlar bo'lishi mumkin
✓ Bir fakultetda guruh nomlari takrorlanmaydi
✓ Database composite unique index ishlayapti
```

**Test file:** `test_faculty_groups.php`

Run test:
```bash
php test_faculty_groups.php
```

---

## 📝 Excel Import Formati (Excel Import Format)

| A: Guruh nomi | B: Fakultet  |
|---------------|--------------|
| 221-20        | Informatika  |
| 221-20        | Matematika   |
| 222-20        | Informatika  |
| 223-20        | Fizika       |

**Note:** Bir xil guruh nomi har xil fakultetlarda import qilish mumkin.

---

## 🚀 Migration Ishga Tushirish (Running Migration)

```bash
php artisan migrate
```

Output:
```
INFO  Running migrations.
  2025_10_28_120050_add_unique_index_to_groups_table .... 15.77ms DONE
```

---

## 💡 Qo'shimcha Ma'lumot (Additional Information)

### Database Index Details:
- **Index Name:** `groups_name_faculty_unique`
- **Type:** Composite Unique Index
- **Columns:** `name`, `faculty`
- **Purpose:** Ensures uniqueness of group names within each faculty

### Validation Logic:
- When creating/updating groups via UI: Laravel Rule validation
- When importing via Excel: Manual database check before insertion
- Error messages: Uzbek language with clear faculty context

---

## 📌 Keyingi Qadamlar (Next Steps - Optional)

Agar kerak bo'lsa, quyidagi funksiyalarni qo'shish mumkin:

1. **Guruhlarni fakultet bo'yicha filterlash**
   - Admin panelida fakultet tanlab, shu fakultetdagi guruhlarni ko'rish

2. **Guruh statistikasi**
   - Har bir fakultetdagi guruhlar soni
   - Har bir fakultetdagi talabalar soni

3. **Fakultet-Guruh hisobotlari**
   - Excel export with faculty grouping
   - PDF reports per faculty

---

## 🔍 Tekshirish (Verification)

Database'da tekshirish:
```sql
-- Show all groups with their faculties
SELECT name, faculty, COUNT(*) as count
FROM groups
GROUP BY name, faculty;

-- Show duplicate group names across faculties (should be allowed)
SELECT name, COUNT(DISTINCT faculty) as faculty_count
FROM groups
GROUP BY name
HAVING faculty_count > 1;
```

---

## ✅ Xulosa (Conclusion)

- ✅ Migration executed successfully
- ✅ Validation rules updated in controller
- ✅ Excel import handles per-faculty uniqueness
- ✅ Database constraint enforces uniqueness
- ✅ Tests passing

**Tizim to'g'ri ishlayapti! (System is working correctly!)**
