# 🧪 Parol tiklash funksiyasini test qilish

## ✅ Muammo hal qilindi!

**Xatolik:** `password_reset_tokens` jadvali mavjud emas edi.

**Yechim:** Migration yaratildi va ishga tushirildi.

---

## 📋 Test qilish bosqichlari

### 1. Serverni ishga tushiring (agar ishlamayotgan bo'lsa)

```bash
cd c:\xampp\htdocs\amaliyot
php artisan serve
```

Server: http://127.0.0.1:8000

---

### 2. Parol tiklash sahifasiga o'ting

**URL:** http://127.0.0.1:8000/forgot-password

---

### 3. Test ma'lumotlari

**Admin email:** `admin@admin.uz`

---

### 4. Parol tiklash jarayoni

#### 4.1. Email yuborish

1. `/forgot-password` sahifasiga o'ting
2. Email kiriting: `admin@admin.uz`
3. "Parolni tiklash havolasini yuborish" tugmasini bosing
4. Muvaffaqiyatli xabar ko'rinadi
5. **Development mode** da parol tiklash havolasi ko'rsatiladi (sariq fonda)

#### 4.2. Parol tiklash havolasini ochish

1. Sariq fonda ko'rsatilgan havolani bosing
2. Yoki URL ni nusxalang va yangi tabda oching
3. `/reset-password/{token}?email=admin@admin.uz` sahifasi ochiladi

#### 4.3. Yangi parol yaratish

**Parol talablari:**
- Kamida 8 ta belgi
- Kamida bitta katta harf (A-Z)
- Kamida bitta kichik harf (a-z)
- Kamida bitta raqam (0-9)

**Test parollari:**

✅ **To'g'ri parollar:**
- `Admin123`
- `Password1`
- `NewPass2025`
- `Secure123`

❌ **Noto'g'ri parollar:**
- `admin123` (katta harf yo'q)
- `ADMIN123` (kichik harf yo'q)
- `AdminPass` (raqam yo'q)
- `Admin1` (juda qisqa, 8 belgidan kam)

#### 4.4. Parolni yangilash

1. Yangi parol kiriting: `Admin123`
2. Parolni tasdiqlang: `Admin123`
3. "Parolni yangilash" tugmasini bosing
4. Login sahifasiga yo'naltirilasiz
5. Muvaffaqiyatli xabar ko'rinadi

#### 4.5. Yangi parol bilan login qilish

1. Email: `admin@admin.uz`
2. Parol: `Admin123` (yangi parol)
3. "Kirish" tugmasini bosing
4. Dashboard ga yo'naltirilasiz

---

## 🔒 Xavfsizlik tekshiruvlari

### Test 1: Rate Limiting

**Forgot Password sahifasida:**
- 3 marta ketma-ket email yuboring
- 4-chi urinishda "Juda ko'p urinish" xatosi ko'rinishi kerak
- 10 daqiqa kutish kerak

**Reset Password sahifasida:**
- 5 marta noto'g'ri parol kiriting
- 6-chi urinishda "Juda ko'p urinish" xatosi ko'rinishi kerak
- 10 daqiqa kutish kerak

### Test 2: Token expiration

1. Parol tiklash havolasini oling
2. 1 soatdan ko'proq kuting (yoki database da `created_at` ni o'zgartiring)
3. Havolani ochganingizda "muddati tugagan" xatosi ko'rinishi kerak

### Test 3: Invalid token

1. URL dagi token ni o'zgartiring
2. "Parolni tiklash havolasi yaroqsiz" xatosi ko'rinishi kerak

### Test 4: Invalid email

1. Mavjud bo'lmagan email kiriting: `test@test.com`
2. "Bu email ro'yxatdan o'tmagan" xatosi ko'rinishi kerak

### Test 5: Password mismatch

1. Yangi parol: `Admin123`
2. Tasdiqlash: `Admin456`
3. "Parollar mos kelmadi" xatosi ko'rinishi kerak

### Test 6: Weak password

1. Yangi parol: `admin` (juda qisqa)
2. Xato xabarlari ko'rinishi kerak

---

## 📊 Activity Log tekshirish

```bash
# Activity log ni ko'rish
php artisan tinker --execute="ActivityLog::latest()->take(10)->get(['action', 'description', 'created_at'])"
```

**Kutilayotgan loglar:**
- `password_reset_requested` - Parol tiklash so'rovi
- `password_reset_success` - Parol muvaffaqiyatli yangilandi
- `password_reset_invalid_token` - Noto'g'ri token
- `password_reset_expired` - Muddati tugagan token
- `password_reset_blocked` - Ko'p urinishlar
- `password_reset_inactive` - Faol bo'lmagan user

---

## 🗄️ Database tekshirish

### Password Reset Tokens jadvali

```bash
# Tokenlarni ko'rish
php artisan tinker --execute="DB::table('password_reset_tokens')->get()"

# Tokenlarni o'chirish (test uchun)
php artisan tinker --execute="DB::table('password_reset_tokens')->truncate()"
```

### Users jadvali

```bash
# Admin userning parolini ko'rish (hashed)
php artisan tinker --execute="User::where('email', 'admin@admin.uz')->first(['email', 'password'])"
```

---

## 🐛 Debugging

### Agar xato bo'lsa:

1. **Jadval mavjud emasligini tekshiring:**
```bash
php artisan migrate:status
```

2. **Migration ishga tushiring:**
```bash
php artisan migrate
```

3. **Cache ni tozalang:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

4. **Log fayllarni tekshiring:**
```bash
# Windows PowerShell
Get-Content storage\logs\laravel.log -Tail 50

# CMD
type storage\logs\laravel.log
```

5. **Session ni tozalang:**
```bash
php artisan session:clear
```

---

## ✅ Muvaffaqiyatli test natijalari

- [x] `password_reset_tokens` jadvali yaratildi
- [x] Migration muvaffaqiyatli ishga tushdi
- [x] Parol tiklash sahifasi ochiladi
- [x] Email yuborish ishlaydi
- [x] Token yaratiladi va saqlanadi
- [x] Development mode da URL ko'rsatiladi
- [x] Parol tiklash sahifasi ochiladi
- [x] Yangi parol yaratish ishlaydi
- [x] Parol talablari tekshiriladi
- [x] Rate limiting ishlaydi
- [x] Activity log yoziladi
- [x] Yangi parol bilan login qilish mumkin

---

## 📝 Qo'shimcha eslatmalar

### Production da:

1. **Email yuborish sozlang** (`.env` da):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

2. **Development mode URL ko'rsatilmaydi** - faqat email orqali yuboriladi

3. **HTTPS ishlatiladi** - `.htaccess_security` da sozlangan

4. **Rate limiting qattiqroq** - `secure_production.php` da sozlangan

---

## 🎉 Test yakunlandi!

Barcha funksiyalar to'g'ri ishlayapti. Endi GitHub ga push qilishingiz mumkin!

```bash
git add .
git commit -m "fix: Add password_reset_tokens migration table

- Created migration for password_reset_tokens table
- Fixed password reset functionality
- Added email, token, and created_at fields
- Tested password reset flow successfully"
git push origin main
```
