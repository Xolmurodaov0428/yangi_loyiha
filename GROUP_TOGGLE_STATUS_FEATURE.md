# Guruhni Faol/Nofaol Qilish (Toggle Group Active/Inactive Status)

## 🎯 Maqsad (Objective)

Guruhlar ro'yxatida har bir guruhni **faol** yoki **nofaol** holatga o'tkazish imkoniyatini qo'shish.

**Add the ability to toggle groups between active and inactive status in the groups list.**

---

## ✨ Yangi Funksiya (New Feature)

### Toggle Button:
- **Faol guruh** uchun: 🟠 Orange button with "toggle-on" icon
- **Nofaol guruh** uchun: 🟢 Green button with "toggle-off" icon

---

## 🔧 Amalga Oshirilgan O'zgarishlar (Implementation Changes)

### 1. Route Added

**File:** [`routes/web.php`](c:\xampp\htdocs\amaliyot\routes\web.php)

```php
Route::post('/groups/{id}/toggle', [CatalogController::class, 'toggleGroup'])
    ->name('groups.toggle');
```

**Route Details:**
- **Method:** POST
- **URL:** `/admin/catalogs/groups/{id}/toggle`
- **Name:** `admin.catalogs.groups.toggle`

---

### 2. Controller Method

**File:** [`app/Http/Controllers/Admin/CatalogController.php`](c:\xampp\htdocs\amaliyot\app\Http\Controllers\Admin\CatalogController.php)

```php
public function toggleGroup($id)
{
    $group = Group::findOrFail($id);
    $group->is_active = !$group->is_active;
    $group->save();

    $status = $group->is_active ? 'faollashtirildi' : 'nofaol qilindi';

    return redirect()->route('admin.catalogs.groups')
        ->with('success', "Guruh '{$group->name}' {$status}!");
}
```

**Logic:**
1. Find group by ID
2. Toggle `is_active` status (true ↔ false)
3. Save changes
4. Redirect with success message

---

### 3. View Update - Toggle Button

**File:** [`resources/views/admin/catalogs/groups.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\catalogs\groups.blade.php)

#### Button in Table:
```blade
<button class="btn btn-sm btn-outline-{{ $group->is_active ? 'warning' : 'success' }}" 
        onclick="toggleGroup({{ $group->id }}, '{{ $group->name }}', {{ $group->is_active ? 'true' : 'false' }})"
        title="{{ $group->is_active ? 'Nofaol qilish' : 'Faollashtirish' }}">
    <i class="fa fa-{{ $group->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
</button>
```

**Dynamic Styling:**
- **Active group:** Orange outline (`btn-outline-warning`) + toggle-on icon
- **Inactive group:** Green outline (`btn-outline-success`) + toggle-off icon

---

### 4. JavaScript Function

```javascript
function toggleGroup(id, name, isActive) {
    const action = isActive ? 'nofaol qilish' : 'faollashtirish';
    if (confirm(`"${name}" guruhini ${action}ga ishonchingiz komilmi?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/catalogs/groups/${id}/toggle`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
}
```

**Flow:**
1. Show confirmation dialog
2. Create hidden form with CSRF token
3. Submit POST request to toggle endpoint
4. Redirect back to groups list

---

## 📊 Button States

### Active Group (is_active = true):

```
┌─────────────────────────────────────────┐
│ Holat: ✅ Faol                          │
│                                         │
│ Tugma: [🟠 ⏸] Nofaol qilish             │
│        (Orange, toggle-on icon)         │
└─────────────────────────────────────────┘
```

### Inactive Group (is_active = false):

```
┌─────────────────────────────────────────┐
│ Holat: ❌ Nofaol                        │
│                                         │
│ Tugma: [🟢 ▶] Faollashtirish            │
│        (Green, toggle-off icon)         │
└─────────────────────────────────────────┘
```

---

## 🎨 UI Example

### Groups Table with Toggle Buttons:

| # | Guruh nomi | Fakultet    | Talabalar | Holat     | Amallar                |
|---|------------|-------------|-----------|-----------|------------------------|
| 1 | 221-20     | Informatika | 0         | ✅ Faol   | 🟠 ✏️ 🗑️               |
| 2 | 222-20     | Matematika  | 2         | ✅ Faol   | 🟠 ✏️ 🗑️               |
| 3 | 223-20     | Fizika      | 2         | ❌ Nofaol | 🟢 ✏️ 🗑️               |

**Legend:**
- 🟠 = Toggle off (make inactive)
- 🟢 = Toggle on (make active)
- ✏️ = Edit
- 🗑️ = Delete

---

## 🔄 User Flow

### 1. Toggle Active → Inactive:

```
User clicks orange toggle button
    ↓
Confirmation: "221-20 guruhini nofaol qilishga ishonchingiz komilmi?"
    ↓
User confirms
    ↓
POST /admin/catalogs/groups/1/toggle
    ↓
is_active: true → false
    ↓
Redirect with success: "Guruh '221-20' nofaol qilindi!"
    ↓
Badge changes: ✅ Faol → ❌ Nofaol
Button changes: 🟠 → 🟢
```

### 2. Toggle Inactive → Active:

```
User clicks green toggle button
    ↓
Confirmation: "221-20 guruhini faollashtirishga ishonchingiz komilmi?"
    ↓
User confirms
    ↓
POST /admin/catalogs/groups/1/toggle
    ↓
is_active: false → true
    ↓
Redirect with success: "Guruh '221-20' faollashtirildi!"
    ↓
Badge changes: ❌ Nofaol → ✅ Faol
Button changes: 🟢 → 🟠
```

---

## 🧪 Test Results

**Test File:** [`test_group_toggle.php`](c:\xampp\htdocs\amaliyot\test_group_toggle.php)

### Current Groups Status:

```
Guruh nomi               Fakultet            Holat
------------------------------------------------------------
221-20                   Informatika         ✅ Faol
221-20                   Matematika          ✅ Faol
222-20                   Matematika          ✅ Faol
223-20                   Fizika              ✅ Faol
Dasturlash 1-guruh       init                ✅ Faol
Dasturlash 2-guruh       init                ✅ Faol
```

### Toggle Test:
```
Test: Birinchi guruhni toggle qilish...
Guruh: 221-20
Avvalgi holat: Faol
Yangi holat: Nofaol

✅ Toggle muvaffaqiyatli bajarildi!
✅ Asl holatga qaytarildi
```

---

## 💡 Foydasi (Benefits)

### 1. **Guruhlarni vaqtincha o'chirish**
Instead of deleting, you can deactivate groups temporarily:
- Keep historical data
- Reactivate when needed
- No data loss

### 2. **Arxivlash (Archiving)**
Deactivate old groups:
- Keep for records
- Hide from active lists
- Easy to restore

### 3. **Boshqaruv (Management)**
Better control:
- Active/inactive filtering
- Cleaner interface
- Better organization

---

## 🎯 Qo'shimcha Imkoniyatlar (Future Enhancements)

### 1. Filter by Status:
```blade
<select name="status" class="form-select">
    <option value="all">Hammasi</option>
    <option value="active">Faqat faol</option>
    <option value="inactive">Faqat nofaol</option>
</select>
```

### 2. Bulk Toggle:
```php
// Toggle multiple groups at once
public function bulkToggle(Request $request)
{
    $groupIds = $request->group_ids;
    Group::whereIn('id', $groupIds)->update([
        'is_active' => DB::raw('NOT is_active')
    ]);
}
```

### 3. Auto-deactivate Empty Groups:
```php
// Deactivate groups with no students
Group::has('students', '=', 0)
    ->update(['is_active' => false]);
```

### 4. Activity Log:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'group_toggled',
    'description' => "Guruh '{$group->name}' {$status}",
]);
```

---

## 📋 Success Messages

### Active → Inactive:
```
✅ Guruh '221-20' nofaol qilindi!
```

### Inactive → Active:
```
✅ Guruh '221-20' faollashtirildi!
```

---

## 🔗 Tegishli Fayllar (Related Files)

1. **Route:** [`routes/web.php`](c:\xampp\htdocs\amaliyot\routes\web.php) - Line ~119
2. **Controller:** [`app/Http/Controllers/Admin/CatalogController.php`](c:\xampp\htdocs\amaliyot\app\Http\Controllers\Admin\CatalogController.php) - toggleGroup()
3. **View:** [`resources/views/admin/catalogs/groups.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\catalogs\groups.blade.php)
4. **Model:** [`app/Models/Group.php`](c:\xampp\htdocs\amaliyot\app\Models\Group.php) - is_active field
5. **Test:** [`test_group_toggle.php`](c:\xampp\htdocs\amaliyot\test_group_toggle.php)

---

## ✅ Xulosa (Conclusion)

### What Was Added:
✅ **Route** - POST endpoint for toggling  
✅ **Controller** - toggleGroup() method  
✅ **View** - Toggle button with dynamic styling  
✅ **JavaScript** - Confirmation and form submission  
✅ **Testing** - Verified functionality  

### Benefits:
- 🎯 **Easy toggling** - One click to activate/deactivate
- 🔒 **Safe operation** - Confirmation dialog
- 🎨 **Visual feedback** - Color-coded buttons
- 📊 **Status tracking** - Clear active/inactive badges
- 💾 **Data preservation** - No need to delete groups

---

**📅 Implemented:** 2025-10-28  
**✅ Status:** Complete & Tested  
**🎯 Result:** Groups can now be toggled active/inactive!
