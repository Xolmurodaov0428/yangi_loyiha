# 🐛 Bug Fix: Cache table not found

## ❌ Xatolik

```
Illuminate\Database\QueryException
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'laravel_db.cache' doesn't exist
```

**Xatolik joyi:** `app/Http/Controllers/AuthController.php:38` (RateLimiter)

---

## 🔍 Sabab

`AuthController` da `RateLimiter` ishlatilmoqda, lekin `cache` jadvali database da mavjud emas.

### RateLimiter kodda:

```php
// AuthController.php - login metodi
$key = 'login.' . $request->ip();
$maxAttempts = 5;
$decayMinutes = 1;

if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
    // ... xatolik yuzaga keladi
}
```

### Muammo:

Laravel `RateLimiter` default holatda database cache driver ishlatadi va `cache` jadvaliga murojaat qiladi. Lekin migration fayli yo'q edi.

---

## ✅ Yechim

### 1. Cache migration yaratish

```bash
php artisan make:migration create_cache_table
```

### 2. Migration strukturasi

**Fayl:** `database/migrations/2025_10_22_135542_create_cache_table.php`

```php
public function up(): void
{
    // Cache jadvali - RateLimiter va boshqa cache uchun
    Schema::create('cache', function (Blueprint $table) {
        $table->string('key')->primary();
        $table->mediumText('value');
        $table->integer('expiration');
    });

    // Cache locks jadvali - atomic operations uchun
    Schema::create('cache_locks', function (Blueprint $table) {
        $table->string('key')->primary();
        $table->string('owner');
        $table->integer('expiration');
    });
}

public function down(): void
{
    Schema::dropIfExists('cache');
    Schema::dropIfExists('cache_locks');
}
```

### 3. Migration ishga tushirish

```bash
php artisan migrate
```

**Natija:**
```
INFO  Running migrations.
2025_10_22_135542_create_cache_table .......... DONE
```

---

## 📊 Cache jadval strukturasi

### `cache` jadvali

| Ustun | Turi | Tavsif |
|-------|------|--------|
| `key` | string (primary) | Cache key (masalan: `login.127.0.0.1`) |
| `value` | mediumText | Serialize qilingan ma'lumot |
| `expiration` | integer | Unix timestamp (muddati tugash vaqti) |

### `cache_locks` jadvali

| Ustun | Turi | Tavsif |
|-------|------|--------|
| `key` | string (primary) | Lock key |
| `owner` | string | Lock owner identifier |
| `expiration` | integer | Unix timestamp (lock muddati) |

---

## 🔧 Cache driver sozlamalari

### `.env` faylida:

```env
CACHE_STORE=database
# yoki
CACHE_STORE=file
# yoki
CACHE_STORE=redis
```

### `config/cache.php` da:

```php
'default' => env('CACHE_STORE', 'database'),

'stores' => [
    'database' => [
        'driver' => 'database',
        'table' => 'cache',
        'connection' => null,
        'lock_connection' => null,
    ],
    // ...
],
```

---

## 🧪 Test qilish

### 1. Login sahifasiga o'ting

```
http://127.0.0.1:8000/login
```

### 2. Login qiling

**Email:** `admin@admin.uz`  
**Parol:** `admin0428` (yoki yangi parol)

**Kutilayotgan natija:**
- ✅ Xatolik yo'q
- ✅ RateLimiter ishlaydi
- ✅ Login muvaffaqiyatli

### 3. Rate limiting ni test qiling

1. 5 marta noto'g'ri parol kiriting
2. 6-chi urinishda "Juda ko'p urinish" xatosi ko'rinishi kerak
3. Cache jadvalida ma'lumot saqlanadi:

```sql
SELECT * FROM cache WHERE `key` LIKE 'login.%';
```

**Natija:**
```
key: login.127.0.0.1
value: i:6; (serialize qilingan)
expiration: 1729598400
```

---

## 📝 Cache bilan bog'liq boshqa funksiyalar

### 1. RateLimiter (AuthController da)

```php
// Login rate limiting
RateLimiter::tooManyAttempts('login.' . $request->ip(), 5)

// Password reset rate limiting
RateLimiter::tooManyAttempts('password-reset.' . $request->ip(), 3)

// Register rate limiting
RateLimiter::tooManyAttempts('register.' . $request->ip(), 3)
```

### 2. Cache helper funksiyalari

```php
// Ma'lumot saqlash
Cache::put('key', 'value', $seconds);

// Ma'lumot olish
$value = Cache::get('key');

// Ma'lumot o'chirish
Cache::forget('key');

// Barcha cache ni tozalash
Cache::flush();
```

### 3. Artisan buyruqlari

```bash
# Cache ni tozalash
php artisan cache:clear

# Cache jadvalini tozalash
php artisan cache:table  # migration yaratadi
php artisan migrate      # migration ishga tushiradi

# Cache ni ko'rish (tinker orqali)
php artisan tinker
>>> DB::table('cache')->get()
```

---

## 🔍 Debugging

### Cache jadvalini tekshirish

```sql
-- Cache jadvalini ko'rish
SELECT * FROM cache;

-- Rate limiter ma'lumotlarini ko'rish
SELECT * FROM cache WHERE `key` LIKE '%login%';

-- Muddati tugagan cache ni o'chirish
DELETE FROM cache WHERE expiration < UNIX_TIMESTAMP();
```

### Cache ni qo'lda tozalash

```bash
# Artisan orqali
php artisan cache:clear

# SQL orqali
mysql -u root -p
USE laravel_db;
TRUNCATE TABLE cache;
TRUNCATE TABLE cache_locks;
```

---

## ⚙️ Alternative: File cache driver

Agar database cache kerak bo'lmasa, file cache ishlatishingiz mumkin:

### `.env` da:

```env
CACHE_STORE=file
```

**Afzalliklari:**
- ✅ Database jadval kerak emas
- ✅ Tezroq (kichik loyihalar uchun)
- ✅ Sozlash oson

**Kamchiliklari:**
- ❌ Distributed systems da ishlamaydi
- ❌ Sekinroq (katta loyihalar uchun)
- ❌ File system ga bog'liq

---

## 🚀 Production sozlamalari

### Redis cache (tavsiya etiladi)

```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Afzalliklari:**
- ✅ Juda tez
- ✅ Distributed cache
- ✅ TTL (Time To Live) support
- ✅ Atomic operations

### Memcached

```env
CACHE_STORE=memcached
MEMCACHED_HOST=127.0.0.1
MEMCACHED_PORT=11211
```

---

## 📦 Migration ro'yxati

Barcha migration'lar:

```bash
php artisan migrate:status
```

**Yangi qo'shilgan:**
- ✅ `2025_10_22_135042_create_password_reset_tokens_table`
- ✅ `2025_10_22_135542_create_cache_table`

---

## 🎯 Xulosa

1. **Muammo:** `cache` jadvali mavjud emas edi
2. **Sabab:** RateLimiter database cache driver ishlatadi
3. **Yechim:** Cache migration yaratildi va ishga tushirildi
4. **Natija:** Login va rate limiting ishlaydi

---

## 📦 GitHub ga push qilish

```bash
git add .
git commit -m "fix: Add cache table migration for RateLimiter

- Created cache and cache_locks tables
- Fixed QueryException for missing cache table
- RateLimiter now works properly for login, password reset
- Cache table structure matches Laravel defaults
- Added cache_locks for atomic operations
- All rate limiting features now functional"
git push origin main
```

---

**✅ Bug tuzatildi! Login va rate limiting endi to'g'ri ishlaydi.**
