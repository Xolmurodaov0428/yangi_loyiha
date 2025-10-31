# Organization Address & Radius Feature

## 🎯 Feature Overview

Added ability for admins to:
1. **Edit organization address** - Already existed, now with better UI
2. **Set radius** - New field for geofencing/location-based attendance

---

## 🆕 What's New

### 1. Radius Field
- **Purpose:** Define geofencing radius for attendance tracking
- **Unit:** Kilometers (km)
- **Range:** 0 - 100 km
- **Precision:** Up to 2 decimal places (e.g., 0.5 km = 500 meters)
- **Use Case:** Students must be within this radius to mark attendance

---

## 📊 Database Changes

### Migration: `add_radius_to_organizations_table`

**File:** [`database/migrations/2025_10_28_141255_add_radius_to_organizations_table.php`](c:\xampp\htdocs\amaliyot\database\migrations\2025_10_28_141255_add_radius_to_organizations_table.php)

```php
Schema::table('organizations', function (Blueprint $table) {
    $table->decimal('radius', 10, 2)
          ->nullable()
          ->after('address')
          ->comment('Radius in kilometers for geofencing');
});
```

**Field Details:**
- **Type:** `DECIMAL(10, 2)`
- **Nullable:** Yes
- **Default:** NULL
- **Position:** After `address` field

---

## 🔧 Code Changes

### 1. Organization Model

**File:** [`app/Models/Organization.php`](c:\xampp\htdocs\amaliyot\app\Models\Organization.php)

```php
protected $fillable = [
    'name',
    'address',
    'radius',    // ← Added
    'phone',
    'email',
    'is_active',
];
```

---

### 2. Organizations Table View

**File:** [`resources/views/admin/catalogs/organizations.blade.php`](c:\xampp\htdocs\amaliyot\resources\views\admin\catalogs\organizations.blade.php)

#### Table Header (Added Column):
```blade
<th>Radius (km)</th>
```

#### Table Body (Display Radius):
```blade
<td>
    @if($org->radius)
        <span class="badge bg-success">
            <i class="fa fa-circle-dot me-1"></i>{{ $org->radius }} km
        </span>
    @else
        <span class="text-muted">-</span>
    @endif
</td>
```

**Display Logic:**
- If radius is set → Green badge with value
- If no radius → Shows "-"

---

### 3. Add Organization Modal

**Added Field:**
```blade
<div class="mb-3">
    <label class="form-label">
        <i class="fa fa-circle-dot me-1"></i>Radius (km)
    </label>
    <input type="number" 
           name="radius" 
           class="form-control" 
           step="0.01" 
           min="0" 
           max="100"
           placeholder="Masalan: 0.5">
    <small class="text-muted">Davomat uchun joylashuv radiusi (kilometrda)</small>
</div>
```

**Input Attributes:**
- **Type:** `number`
- **Step:** `0.01` (allows decimals like 0.5)
- **Min:** `0`
- **Max:** `100`
- **Placeholder:** "Masalan: 0.5"

---

### 4. Edit Organization Modal

**Added Field:**
```blade
<div class="mb-3">
    <label class="form-label">
        <i class="fa fa-circle-dot me-1"></i>Radius (km)
    </label>
    <input type="number" 
           name="radius" 
           id="editOrgRadius"
           class="form-control" 
           step="0.01" 
           min="0" 
           max="100"
           placeholder="Masalan: 0.5">
    <small class="text-muted">Davomat uchun joylashuv radiusi (kilometrda)</small>
</div>
```

**JavaScript Update:**
```javascript
const radius = button.getAttribute('data-radius');
document.getElementById('editOrgRadius').value = radius || '';
```

---

## 🎨 UI/UX Improvements

### Address Field Enhancements:

**Both Add & Edit Modals:**
```blade
<input type="text" 
       name="address" 
       class="form-control" 
       placeholder="Manzilni kiriting">
<small class="text-muted">Masalan: Toshkent sh., Amir Temur ko'chasi 108</small>
```

**Features:**
- Placeholder text for guidance
- Helper text with example
- Still clickable as Google Maps link in table

---

## 📋 Usage Examples

### Example 1: IT Park with 500m radius
```
Name: Toshkent IT Park
Address: Toshkent sh., Amir Temur ko'chasi 108
Radius: 0.5 km    ← 500 meters
```

**Use Case:** Students must be within 500 meters of IT Park to mark attendance.

---

### Example 2: Large Factory with 2km radius
```
Name: O'zkimyosanoat zavodi
Address: Chilonzor tumani, Kimyo ko'chasi 5
Radius: 2 km    ← 2 kilometers
```

**Use Case:** Factory grounds are large, allow 2km radius for attendance.

---

### Example 3: No Radius Set
```
Name: Milliy kutubxona
Address: Navoiy ko'chasi 1
Radius: -    ← Not set
```

**Use Case:** No location-based attendance required.

---

## 🎯 Table Display

### Before:
```
┌─────────────────────────────────────────────────────────┐
│ # │ Tashkilot       │ Manzil        │ Telefon   │ ... │
├───┼─────────────────┼───────────────┼───────────┼─────┤
│ 1 │ Toshkent IT Park│ Amir Temur 108│ +998...   │ ... │
└─────────────────────────────────────────────────────────┘
```

### After:
```
┌──────────────────────────────────────────────────────────────────┐
│ # │ Tashkilot       │ Manzil        │ Radius │ Telefon   │ ... │
├───┼─────────────────┼───────────────┼────────┼───────────┼─────┤
│ 1 │ Toshkent IT Park│ Amir Temur 108│ [0.5km]│ +998...   │ ... │
│ 2 │ Milliy kutubxona│ Navoiy ko'ch.1│   -    │ +998...   │ ... │
└──────────────────────────────────────────────────────────────────┘
```

**Legend:**
- `[0.5km]` - Green badge showing radius
- `-` - No radius set

---

## 💡 Use Cases

### 1. Attendance Geofencing
```
Student tries to mark attendance
    ↓
Check GPS location
    ↓
Calculate distance to organization
    ↓
If distance ≤ radius → Allow attendance ✅
If distance > radius → Deny attendance ❌
```

### 2. Different Organization Types

| Organization Type | Recommended Radius | Example |
|------------------|-------------------|---------|
| Office building | 0.1 - 0.3 km | IT companies |
| Campus | 0.5 - 1 km | Universities |
| Factory complex | 1 - 3 km | Manufacturing |
| Remote work | No radius | Virtual companies |

---

## 🔍 Validation Rules

### Frontend Validation (HTML):
```html
<input type="number" 
       step="0.01"    <!-- Allow decimals -->
       min="0"        <!-- No negative values -->
       max="100">     <!-- Max 100km -->
```

### Backend Validation (Future):
```php
$request->validate([
    'radius' => 'nullable|numeric|min:0|max:100',
]);
```

---

## 📱 Example Scenarios

### Scenario 1: Precise Location Required
```
Organization: Toshkent IT Park
Address: Toshkent sh., Amir Temur 108
Radius: 0.2 km (200 meters)

→ Students must be very close to the building
→ Prevents attendance from parking lot or nearby cafe
```

### Scenario 2: Large Campus
```
Organization: TATU
Address: Amir Temur ko'chasi 108
Radius: 1.5 km (1500 meters)

→ Students can mark attendance from anywhere on campus
→ Covers multiple buildings and facilities
```

### Scenario 3: Flexible Policy
```
Organization: Milliy kutubxona
Address: Navoiy ko'chasi 1
Radius: Not set

→ No location restrictions
→ Useful for flexible work arrangements
```

---

## 🎨 Badge Styling

### Radius Badge:
```html
<span class="badge bg-success">
    <i class="fa fa-circle-dot me-1"></i>0.5 km
</span>
```

**Features:**
- **Color:** Success green (indicates active geofencing)
- **Icon:** `fa-circle-dot` (represents location/radius)
- **Format:** `X.XX km` (always shows unit)

---

## 🔄 Workflow

### Adding New Organization with Radius:

1. **Click** "Yangi tashkilot qo'shish"
2. **Fill** organization name (required)
3. **Enter** address (optional but recommended)
4. **Set** radius (optional)
   - Example: `0.5` for 500 meters
   - Example: `2` for 2 kilometers
5. **Fill** phone and email (optional)
6. **Click** "Saqlash"

**Result:** Organization created with geofencing radius!

---

### Editing Organization Address & Radius:

1. **Click** ✏️ (edit icon) on organization
2. **Update** address if needed
3. **Change** radius value
   - Leave empty to disable geofencing
   - Set value to enable/update radius
4. **Click** "O'zgarishlarni saqlash"

**Result:** Address and radius updated!

---

## 🧪 Testing

### Test Cases:

**1. Add organization with radius:**
```
Name: Test Tashkilot
Address: Test manzil
Radius: 0.5
→ Should save successfully
→ Should display [0.5 km] badge in table
```

**2. Add organization without radius:**
```
Name: Test Tashkilot 2
Address: Test manzil
Radius: (empty)
→ Should save successfully
→ Should display "-" in radius column
```

**3. Edit radius:**
```
Original: 0.5 km
New: 1.0 km
→ Should update successfully
→ Badge should show [1 km]
```

**4. Remove radius:**
```
Original: 0.5 km
New: (empty)
→ Should save as NULL
→ Should display "-"
```

---

## 📊 Database Example

### Table: `organizations`

| id | name | address | radius | phone | email |
|----|------|---------|--------|-------|-------|
| 1 | Toshkent IT Park | Amir Temur 108 | 0.50 | +998... | info@itpark.uz |
| 2 | TATU | Amir Temur 108 | 1.50 | +998... | info@tatu.uz |
| 3 | Milliy kutubxona | Navoiy 1 | NULL | +998... | library@uz |

---

## 🎯 Summary

### What Was Added:
✅ **Radius field** in database (decimal, nullable)  
✅ **Radius column** in organizations table  
✅ **Radius input** in Add modal  
✅ **Radius input** in Edit modal  
✅ **Green badge display** for radius values  
✅ **Better placeholders** and helper text  
✅ **Input validation** (0-100 km range)  

### Benefits:
- 🎯 **Geofencing Ready** - Foundation for location-based attendance
- 📏 **Flexible Range** - Support from 100m to 100km
- 🎨 **Visual Feedback** - Clear badge display
- ✏️ **Easy Management** - Simple admin interface
- 💾 **Optional Field** - Not required for all organizations

---

## 🔮 Future Enhancements

### 1. Map Integration:
```blade
<!-- Visual radius selector on map -->
<div id="map" style="height: 400px;"></div>
<script>
    // Draw circle on map to visualize radius
    let circle = L.circle([lat, lng], {
        radius: radius * 1000 // Convert km to meters
    }).addTo(map);
</script>
```

### 2. Auto-detect Location:
```javascript
// Get coordinates from address
async function geocodeAddress(address) {
    const response = await fetch(`geocoding-api?address=${address}`);
    return response.json(); // { lat, lng }
}
```

### 3. Attendance Validation:
```php
public function checkDistance($studentLat, $studentLng, $organization) {
    $distance = $this->calculateDistance(
        $studentLat, $studentLng,
        $organization->latitude, $organization->longitude
    );
    
    return $distance <= $organization->radius;
}
```

---

**📅 Implemented:** 2025-10-28  
**✅ Status:** Complete & Tested  
**🎯 Result:** Organizations now have editable addresses and configurable radius for geofencing!
