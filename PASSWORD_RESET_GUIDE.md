# 🔑 Parolni tiklash tizimi - Xavfsizlik qo'llanmasi

## Umumiy ma'lumot

Parolni tiklash funksiyasi to'liq xavfsiz qilib yaratilgan va quyidagi xavfsizlik choralarini o'z ichiga oladi.

---

## 🔒 Xavfsizlik choralari

### 1. **Rate Limiting**
- **Parol tiklash so'rovi:** 3 urinish / 10 daqiqa (IP bo'yicha)
- **Parol yangilash:** 5 urinish / 10 daqiqa (IP bo'yicha)
- Ortiqcha urinishlar bloklangan va logga yoziladi

### 2. **Token xavfsizligi**
- **Cryptographically secure token:** `bin2hex(random_bytes(32))` - 64 belgilik random token
- **Token hashing:** Database da hash qilingan holda saqlanadi
- **Token expiration:** 60 daqiqadan keyin yaroqsiz bo'ladi
- **One-time use:** Bir marta ishlatilgandan keyin o'chiriladi

### 3. **Parol talablari**
- Minimum 8 belgi
- Kamida bitta katta harf (A-Z)
- Kamida bitta kichik harf (a-z)
- Kamida bitta raqam (0-9)
- Parol confirmation (ikki marta kiritish)

### 4. **User enumeration himoyasi**
- Email mavjud yoki yo'qligini oshkor qilmaydi
- Har doim bir xil xabar qaytaradi
- Timing attack lardan himoyalangan

### 5. **Activity Logging**
- Barcha parol tiklash so'rovlari loglanadi
- Failed attempts loglanadi
- IP address tracking
- Suspicious activity detection

### 6. **Additional Security**
- Faol bo'lmagan userlar uchun parol tiklash rad etiladi
- Email validation
- CSRF protection
- Input sanitization

---

## 📋 Qanday ishlaydi?

### 1. Foydalanuvchi parolni unutdi

```
User → "Parolni unutdingizmi?" → Email kiriting → So'rov yuborish
```

### 2. Tizim jarayoni

```
1. Rate limiting tekshiruvi
2. Email validation
3. User mavjudligini tekshirish
4. Secure token generatsiya
5. Token ni database ga saqlash (hashed)
6. Email yuborish (yoki development da URL ko'rsatish)
7. Activity log
```

### 3. Parolni yangilash

```
User → Reset URL → Yangi parol kiriting → Tasdiqlash → Parol yangilandi
```

### 4. Xavfsizlik tekshiruvlari

```
1. Rate limiting
2. Token mavjudligi
3. Token muddati (60 daqiqa)
4. Token verification (hash check)
5. Parol kuchi validation
6. User mavjudligi
7. Activity log
```

---

## 🚀 Ishlatish

### Development muhitida:

1. **Login sahifasiga o'ting:**
   ```
   http://localhost/amaliyot/public/login
   ```

2. **"Parolni unutdingizmi?" ni bosing**

3. **Email kiriting:**
   ```
   admin@admin.uz
   ```

4. **Reset URL ni ko'ring:**
   - Development muhitida URL ekranda ko'rsatiladi
   - Production da email orqali yuboriladi

5. **Reset URL ga o'ting va yangi parol kiriting:**
   - Minimum 8 belgi
   - Katta harf, kichik harf, raqam

6. **Yangi parol bilan login qiling**

### Production muhitida:

1. Email yuborish tizimini sozlang (`.env` da):
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@gmail.com
   MAIL_PASSWORD=your_app_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

2. Email template yarating (ixtiyoriy):
   ```php
   // resources/views/emails/password-reset.blade.php
   ```

3. Mail class yarating:
   ```bash
   php artisan make:mail PasswordResetMail
   ```

4. AuthController da email yuborish kodini yoqing:
   ```php
   Mail::to($user->email)->send(new PasswordResetMail($resetUrl));
   ```

---

## 🛡️ Xavfsizlik best practices

### Admin uchun:

1. **Loglarni monitoring qiling:**
   ```bash
   # Failed password reset attempts
   grep "password_reset_invalid_token" storage/logs/laravel.log
   grep "password_reset_expired" storage/logs/laravel.log
   
   # Suspicious activity
   grep "password_reset_blocked" storage/logs/laravel.log
   ```

2. **Rate limiting sozlamalarini tekshiring:**
   ```php
   // AuthController.php
   $maxAttempts = 3;  // Kamaytirishingiz mumkin
   $decayMinutes = 10; // Oshirishingiz mumkin
   ```

3. **Token expiration vaqtini sozlang:**
   ```php
   // AuthController.php - resetPassword method
   if (now()->diffInMinutes($resetRecord->created_at) > 60) {
       // 60 ni o'zgartirishingiz mumkin (masalan, 30)
   ```

4. **Email template ni customize qiling:**
   - Branding qo'shing
   - Xavfsizlik maslahatlarini qo'shing
   - Support contact ma'lumotlarini qo'shing

### Foydalanuvchilar uchun:

1. **Kuchli parol yarating:**
   - Kamida 12 belgi (tavsiya etiladi)
   - Maxsus belgilar qo'shing (!@#$%^&*)
   - Boshqa saytlarda ishlatilmagan parol

2. **Phishing dan ehtiyot bo'ling:**
   - Faqat rasmiy email dan kelgan havolalarni bosing
   - URL ni tekshiring (https://yourdomain.com)
   - Shubhali emaillarni report qiling

3. **Parolni tez-tez o'zgartiring:**
   - Har 3-6 oyda bir marta
   - Agar suspicious activity bo'lsa, darhol

---

## 🔧 Muammolarni hal qilish

### 1. "Parolni tiklash havolasi topilmadi"
**Sabab:** Token yaroqsiz yoki o'chirilgan
**Yechim:** Qaytadan parol tiklash so'rovini yuboring

### 2. "Muddati tugagan"
**Sabab:** 60 daqiqadan ko'p vaqt o'tgan
**Yechim:** Yangi so'rov yuboring

### 3. "Juda ko'p urinish"
**Sabab:** Rate limiting
**Yechim:** 10 daqiqa kuting

### 4. "Email yuborilmadi" (Production)
**Sabab:** Mail sozlamalari noto'g'ri
**Yechim:** 
```bash
# .env ni tekshiring
php artisan config:clear
php artisan config:cache

# Mail test qiling
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### 5. "Parol kuchsiz"
**Sabab:** Validation requirements
**Yechim:** Parol talablariga rioya qiling (8+ belgi, katta/kichik harf, raqam)

---

## 📊 Database struktura

### password_reset_tokens table:

```sql
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);
```

**Eslatma:** Token hashed holda saqlanadi, plain text emas!

---

## 🔍 Testing

### Manual testing:

1. **Valid email bilan test:**
   ```
   Email: admin@admin.uz
   Natija: Success message, reset URL
   ```

2. **Invalid email bilan test:**
   ```
   Email: notexist@test.com
   Natija: Bir xil success message (security)
   ```

3. **Rate limiting test:**
   ```
   4 marta ketma-ket so'rov yuboring
   Natija: "Juda ko'p urinish" xabari
   ```

4. **Expired token test:**
   ```
   Token olish → 61 daqiqa kutish → Reset qilish
   Natija: "Muddati tugagan" xabari
   ```

5. **Invalid token test:**
   ```
   URL dagi token ni o'zgartiring
   Natija: "Yaroqsiz havola" xabari
   ```

### Automated testing (ixtiyoriy):

```php
// tests/Feature/PasswordResetTest.php
public function test_password_reset_request()
{
    $user = User::factory()->create();
    
    $response = $this->post('/forgot-password', [
        'email' => $user->email,
    ]);
    
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => $user->email,
    ]);
}
```

---

## 📈 Monitoring

### Key metrics to track:

1. **Password reset requests per day**
   ```sql
   SELECT DATE(created_at), COUNT(*) 
   FROM password_reset_tokens 
   GROUP BY DATE(created_at);
   ```

2. **Failed attempts**
   ```bash
   grep "password_reset_invalid_token" storage/logs/laravel.log | wc -l
   ```

3. **Successful resets**
   ```bash
   grep "password_reset_success" storage/logs/laravel.log | wc -l
   ```

4. **Blocked IPs**
   ```bash
   grep "password_reset_blocked" storage/logs/laravel.log | cut -d' ' -f5 | sort | uniq -c
   ```

---

## 🚨 Security Alerts

### Qachon admin ga xabar berish kerak:

1. **Ko'p muvaffaqiyatsiz urinishlar** (10+ / soat)
2. **Bir IP dan ko'p so'rovlar** (5+ / soat)
3. **Faol bo'lmagan userlar uchun so'rovlar**
4. **Tunda suspicious activity** (02:00-05:00)

### Alert tizimini sozlash:

```php
// app/Console/Commands/SecurityMonitor.php
if ($failedAttempts > 10) {
    Mail::to('admin@yourdomain.com')
        ->send(new SecurityAlert($details));
}
```

---

## 📝 Changelog

### v1.0 (2024-10-22)
- ✅ Secure token generation
- ✅ Rate limiting
- ✅ Activity logging
- ✅ Strong password validation
- ✅ User enumeration protection
- ✅ Token expiration
- ✅ Development mode URL display

---

## 🔗 Qo'shimcha resurslar

- **OWASP Password Reset:** https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html
- **Laravel Mail:** https://laravel.com/docs/mail
- **Laravel Notifications:** https://laravel.com/docs/notifications

---

**✅ Parolni tiklash tizimi to'liq xavfsiz va production uchun tayyor!**

**⚠️ Eslatma:** Production da email yuborish tizimini sozlashni unutmang!
