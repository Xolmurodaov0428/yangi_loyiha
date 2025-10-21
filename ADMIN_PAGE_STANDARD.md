# Admin Sahifalar Standarti

## Umumiy Struktura

Barcha admin sahifalari quyidagi strukturaga ega bo'lishi kerak:

```blade
@extends('layouts.admin')

@section('title', 'Sahifa nomi')

@section('content')
<div class="container-fluid">
  <!-- 1. HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold">Sahifa sarlavhasi</h1>
      <p class="text-muted mb-0">Qisqa tavsif</p>
    </div>
    <div>
      <!-- Action buttons -->
      <a href="#" class="btn btn-primary">
        <i class="fa fa-plus me-2"></i>Yangi qo'shish
      </a>
    </div>
  </div>

  <!-- 2. ALERTS -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle me-2"></i>
      <strong>Xatoliklar:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- 3. FILTERS (agar kerak bo'lsa) -->
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <!-- Filter fields -->
      </form>
    </div>
  </div>

  <!-- 4. MAIN CONTENT -->
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <!-- Content here -->
    </div>
  </div>

  <!-- 5. STATISTICS (agar kerak bo'lsa) -->
  <div class="row g-3 mt-3">
    <!-- Stats cards -->
  </div>
</div>
@endsection
```

## Komponentlar

### 1. Header
- **Sarlavha:** `h3 mb-1 fw-bold`
- **Tavsif:** `text-muted mb-0`
- **Tugmalar:** `btn btn-primary`

### 2. Alerts
- **Success:** `alert-success` + `fa-check-circle`
- **Error:** `alert-danger` + `fa-exclamation-circle`
- **Dismissible:** `alert-dismissible fade show`

### 3. Cards
- **Karta:** `card border-0 shadow-sm`
- **Body:** `card-body`
- **Margin:** `mb-3` yoki `mb-4`

### 4. Buttons
- **Primary:** `btn btn-primary`
- **Secondary:** `btn btn-secondary`
- **Outline:** `btn btn-outline-primary`
- **Size:** `btn-sm` yoki default

### 5. Tables
- **Table:** `table table-hover align-middle`
- **Thead:** `table-light`
- **Responsive:** `table-responsive`

### 6. Forms
- **Label:** `form-label fw-semibold`
- **Input:** `form-control`
- **Select:** `form-select`
- **Checkbox:** `form-check-input`

### 7. Badges
- **Success:** `badge bg-success`
- **Danger:** `badge bg-danger`
- **Warning:** `badge bg-warning`
- **Info:** `badge bg-info`

### 8. Icons
- **Font Awesome 6**
- **Margin:** `me-1`, `me-2`, `ms-2`

## Ranglar

### Bootstrap Colors:
- **Primary:** `#0d6efd` (Asosiy)
- **Success:** `#198754` (Muvaffaqiyat)
- **Danger:** `#dc3545` (Xavfli)
- **Warning:** `#ffc107` (Ogohlantirish)
- **Info:** `#0dcaf0` (Ma'lumot)
- **Secondary:** `#6c757d` (Ikkinchi darajali)

### Gradient Colors:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Purple */
background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); /* Pink */
background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); /* Blue */
background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); /* Orange */
```

## Spacing

### Margin/Padding:
- `mb-1` = 0.25rem
- `mb-2` = 0.5rem
- `mb-3` = 1rem
- `mb-4` = 1.5rem
- `mb-5` = 3rem

### Gap:
- `g-2` = 0.5rem
- `g-3` = 1rem
- `g-4` = 1.5rem

## Responsive

### Grid:
- `col-12` - Mobile (100%)
- `col-md-6` - Tablet (50%)
- `col-lg-4` - Desktop (33.33%)
- `col-xl-3` - Large (25%)

### Breakpoints:
- `sm` - ≥576px
- `md` - ≥768px
- `lg` - ≥992px
- `xl` - ≥1200px

## Misol Sahifalar

### Index (Ro'yxat)
```blade
- Header (sarlavha + "Yangi qo'shish" tugmasi)
- Alerts
- Filters (qidiruv, filtrlar)
- Table (ma'lumotlar jadvali)
- Pagination
- Statistics (ixtiyoriy)
```

### Create/Edit (Forma)
```blade
- Header (sarlavha + "Bekor qilish" tugmasi)
- Alerts
- Form (input maydonlar)
- Buttons ("Saqlash" + "Bekor qilish")
```

### Show (Ko'rish)
```blade
- Header (sarlavha + "Tahrirlash" tugmasi)
- Alerts
- Info Cards (ma'lumotlar kartalar)
- Related Data (bog'liq ma'lumotlar)
```

## Qo'shimcha

### Loading State:
```html
<div class="spinner-border spinner-border-sm me-2"></div>
```

### Empty State:
```html
<div class="text-center py-5 text-muted">
  <i class="fa fa-inbox fs-1 mb-3 opacity-25"></i>
  <p class="mb-0">Ma'lumot topilmadi</p>
</div>
```

### Confirm Delete:
```html
<form onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
  <button type="submit" class="btn btn-danger">O'chirish</button>
</form>
```

---

✅ Barcha sahifalar ushbu standartga mos bo'lishi kerak!
