# 🔐 Talaba Login API - Postman Qo'llanmasi

## 📋 Talaba Login API

Talabalar uchun login, logout va profil olish funksiyalari.

---

## 🚀 1. Login (Kirish)

### Endpoint:
```
POST http://localhost/amaliyot/public/api/student/login
```

### Headers:
```
Content-Type: application/json
Accept: application/json
```

### Body (JSON):
```json
{
  "username": "aliyev123",
  "password": "student123"
}
```

### ✅ Muvaffaqiyatli javob:
```json
{
  "success": true,
  "message": "Muvaffaqiyatli kirildi",
  "student": {
    "id": 1,
    "full_name": "Aliyev Ali",
    "username": "aliyev123",
    "group_name": "Matematika 1-guruh",
    "faculty": "Matematika",
    "organization": "Toshkent Davlat Universiteti"
  },
  "token": "1|abcd1234567890..."
}
```

### ❌ Xatolik javobi:
```json
{
  "success": false,
  "message": "Login yoki parol noto'g'ri",
  "errors": {
    "username": ["Login yoki parol noto'g'ri"]
  }
}
```

---

## 🔓 2. Logout (Chiqish)

### Endpoint:
```
POST http://localhost/amaliyot/public/api/student/logout
```

### Headers:
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

### Body: (bo'sh)

### ✅ Muvaffaqiyatli javob:
```json
{
  "success": true,
  "message": "Muvaffaqiyatli chiqildi"
}
```

---

## 👤 3. Profil olish

### Endpoint:
```
GET http://localhost/amaliyot/public/api/student/profile
```

### Headers:
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

### Body: (bo'sh)

### ✅ Muvaffaqiyatli javob:
```json
{
  "success": true,
  "student": {
    "id": 1,
    "full_name": "Aliyev Ali",
    "username": "aliyev123",
    "group_name": "Matematika 1-guruh",
    "faculty": "Matematika",
    "organization": "Toshkent Davlat Universiteti",
    "internship_start_date": "2025-10-23",
    "internship_end_date": "2025-11-29"
  }
}
```

---

## 📱 Postman da test qilish

### 1. **Login qilish:**

1. **Method:** POST
2. **URL:** `http://localhost/amaliyot/public/api/student/login`
3. **Headers:**
   - `Content-Type: application/json`
   - `Accept: application/json`
4. **Body:** Raw JSON
   ```json
   {
     "username": "aliyev123",
     "password": "student123"
   }
   ```
5. **Send** bosing

### 2. **Token ni saqlash:**

- Javobdan `token` ni nusxalang
- Keyingi so'rovlarda `Authorization` headeriga qo'shing:
  ```
  Bearer YOUR_TOKEN_HERE
  ```

### 3. **Profil olish:**

1. **Method:** GET
2. **URL:** `http://localhost/amaliyot/public/api/student/profile`
3. **Headers:**
   - `Content-Type: application/json`
   - `Accept: application/json`
   - `Authorization: Bearer YOUR_TOKEN_HERE`
4. **Send** bosing

### 4. **Logout qilish:**

1. **Method:** POST
2. **URL:** `http://localhost/amaliyot/public/api/student/logout`
3. **Headers:**
   - `Content-Type: application/json`
   - `Accept: application/json`
   - `Authorization: Bearer YOUR_TOKEN_HERE`
4. **Send** bosing

---

## 🔧 API Test senariolari

### ✅ Test 1: To'g'ri login
- **Username:** `aliyev123`
- **Password:** `student123`
- **Kutilgan:** 200 OK, token qaytadi

### ❌ Test 2: Noto'g'ri parol
- **Username:** `aliyev123`
- **Password:** `wrongpassword`
- **Kutilgan:** 422 Unprocessable Entity, xatolik xabari

### ❌ Test 3: Mavjud bo'lmagan username
- **Username:** `nonexistent`
- **Password:** `student123`
- **Kutilgan:** 422 Unprocessable Entity, xatolik xabari

### ❌ Test 4: Faol bo'lmagan talaba
- **Username:** `inactive_student`
- **Password:** `password`
- **Kutilgan:** 422 Unprocessable Entity (agar faol bo'lmasa)

### ✅ Test 5: Token bilan profil olish
- **Authorization:** `Bearer VALID_TOKEN`
- **Kutilgan:** 200 OK, talaba ma'lumotlari

### ✅ Test 6: Logout
- **Authorization:** `Bearer VALID_TOKEN`
- **Kutilgan:** 200 OK, muvaffaqiyatli chiqish

---

## 🔒 Xavfsizlik

### ✅ **Qilingan:**
- ✅ Password hashed saqlanadi
- ✅ Token authentication
- ✅ Faqat faol talabalar kirishi mumkin
- ✅ Username va password validation

### ❌ **Qilinmagan (ixtiyoriy):**
- ❌ Rate limiting (login urinishlari cheklovi)
- ❌ CAPTCHA
- ❌ 2FA (ikki faktorli autentifikatsiya)
- ❌ Session timeout

---

## 📊 Database

### Talaba yaratish (admin orqali):

```sql
INSERT INTO students (full_name, username, password, group_name, faculty, is_active)
VALUES ('Aliyev Ali', 'aliyev123', '$2y$12$...', 'Matematika 1-guruh', 'Matematika', 1);
```

### Token saqlash:

Laravel Sanctum `personal_access_tokens` jadvalida saqlanadi.

---

## 🚨 Xatoliklar

### 1. "Login yoki parol noto'g'ri"
- **Sabab:** Username yoki password noto'g'ri
- **Yechim:** To'g'ri ma'lumotlarni kiriting

### 2. "422 Unprocessable Entity"
- **Sabab:** Validation xatoligi
- **Yechim:** Username va password majburiy maydonlar

### 3. "401 Unauthorized"
- **Sabab:** Token noto'g'ri yoki muddati tugagan
- **Yechim:** Qaytadan login qiling

---

## 📋 API Documentation

| Endpoint | Method | Headers | Body | Response |
|----------|--------|---------|------|----------|
| `/api/student/login` | POST | Content-Type, Accept | username, password | token, student |
| `/api/student/logout` | POST | Authorization | - | success message |
| `/api/student/profile` | GET | Authorization | - | student data |

---

## 🎯 Ishlatish misoli

### JavaScript (Frontend):

```javascript
// Login
const login = async (username, password) => {
  const response = await fetch('/api/student/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ username, password }),
  });

  const data = await response.json();

  if (data.success) {
    localStorage.setItem('token', data.token);
    return data.student;
  } else {
    throw new Error(data.message);
  }
};

// Profil olish
const getProfile = async () => {
  const token = localStorage.getItem('token');
  const response = await fetch('/api/student/profile', {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  });

  return await response.json();
};
```

---

## ✅ Tayyor!

Endi Postman da talaba loginini test qilishingiz mumkin:

1. **Login qiling** va token oling
2. **Token bilan profil oling**
3. **Logout qiling**

**🔗 Endpoint:** `http://localhost/amaliyot/public/api/student/login`
