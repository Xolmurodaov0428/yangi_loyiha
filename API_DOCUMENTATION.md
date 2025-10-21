# API Dokumentatsiya

## Base URL
```
http://localhost/amaliyot/public/api/v1
```

## Autentifikatsiya

Barcha API so'rovlari uchun Bearer Token talab qilinadi.

**Header:**
```
Authorization: Bearer YOUR_API_TOKEN
```

Token olish uchun admin panelga kiring va "API Tokenlar" bo'limidan yangi token yarating.

---

## Endpoints

### 1. Talabalar ro'yxati

**GET** `/students`

Barcha talabalarni olish (pagination bilan)

**Query Parameters:**
- `group_id` (optional) - Guruh bo'yicha filter
- `supervisor_id` (optional) - Rahbar bo'yicha filter
- `search` (optional) - Qidiruv (ism, username, student_id)
- `per_page` (optional, default: 15) - Sahifadagi elementlar soni

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "student_id": "ST001",
      "full_name": "Aliyev Akbar",
      "username": "aliyev_akbar",
      "group_id": 1,
      "group_name": "Informatika 1",
      "supervisor_id": 2,
      "phone": "+998901234567",
      "email": "aliyev@example.com",
      "group": {
        "id": 1,
        "name": "Informatika 1",
        "course": 1,
        "faculty": "Informatika va axborot texnologiyalari"
      },
      "supervisor": {
        "id": 2,
        "name": "Aliyev Javohir",
        "email": "supervisor1@test.uz"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

**Misol:**
```bash
curl -X GET "http://localhost/amaliyot/public/api/v1/students?group_id=1&per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 2. Bitta talaba ma'lumotlari

**GET** `/students/{id}`

Talaba to'liq ma'lumotlarini olish

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "student_id": "ST001",
    "full_name": "Aliyev Akbar",
    "username": "aliyev_akbar",
    "group_id": 1,
    "group_name": "Informatika 1",
    "supervisor_id": 2,
    "phone": "+998901234567",
    "email": "aliyev@example.com",
    "organization_id": 1,
    "created_at": "2025-10-15T10:00:00.000000Z",
    "updated_at": "2025-10-15T10:00:00.000000Z",
    "group": {...},
    "supervisor": {...},
    "organization": {...}
  }
}
```

**Misol:**
```bash
curl -X GET "http://localhost/amaliyot/public/api/v1/students/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 3. Talaba davomati

**GET** `/students/{id}/attendance`

Talaba davomat ma'lumotlarini olish

**Query Parameters:**
- `from` (optional) - Boshlanish sanasi (YYYY-MM-DD)
- `to` (optional) - Tugash sanasi (YYYY-MM-DD)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "student_id": 1,
      "date": "2025-10-15",
      "status": "present",
      "notes": null,
      "marked_by": 2,
      "created_at": "2025-10-15T08:00:00.000000Z"
    }
  ],
  "statistics": {
    "total": 20,
    "present": 18,
    "absent": 1,
    "late": 1,
    "excused": 0
  }
}
```

**Status qiymatlari:**
- `present` - Keldi
- `absent` - Kelmadi
- `late` - Kech qoldi
- `excused` - Sababli

**Misol:**
```bash
curl -X GET "http://localhost/amaliyot/public/api/v1/students/1/attendance?from=2025-10-01&to=2025-10-15" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 4. Talaba xabarlari

**GET** `/students/{id}/messages`

Talaba va rahbar o'rtasidagi xabarlar

**Query Parameters:**
- `per_page` (optional, default: 20) - Sahifadagi xabarlar soni

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "conversation_id": 1,
      "sender_id": 1,
      "sender_type": "student",
      "message": "Assalomu alaykum, ustoz!",
      "is_read": true,
      "attachment_path": null,
      "attachment_name": null,
      "created_at": "2025-10-15T10:30:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45
  }
}
```

**Misol:**
```bash
curl -X GET "http://localhost/amaliyot/public/api/v1/students/1/messages?per_page=50" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Xato kodlari

- `401 Unauthorized` - Token yo'q yoki noto'g'ri
- `404 Not Found` - Resurs topilmadi
- `500 Internal Server Error` - Server xatosi

**Xato response:**
```json
{
  "success": false,
  "message": "API token talab qilinadi"
}
```

---

## Real-time yangilanishlar

API ma'lumotlari real-time yangilanadi:
- Yangi talaba qo'shilsa - darhol API'da ko'rinadi
- Davomat belgilansa - darhol API'da ko'rinadi
- Xabar yuborilsa - darhol API'da ko'rinadi

Polling interval: 30 soniya tavsiya etiladi.

---

## Rate Limiting

Hozircha rate limiting yo'q, lekin kelajakda qo'shilishi mumkin.

---

## Misol: Mobile App Integration

```javascript
// React Native / JavaScript misol
const API_BASE = 'http://localhost/amaliyot/public/api/v1';
const API_TOKEN = 'your_token_here';

async function getStudents() {
  const response = await fetch(`${API_BASE}/students`, {
    headers: {
      'Authorization': `Bearer ${API_TOKEN}`,
      'Accept': 'application/json'
    }
  });
  
  const data = await response.json();
  return data;
}

async function getStudentAttendance(studentId, from, to) {
  const response = await fetch(
    `${API_BASE}/students/${studentId}/attendance?from=${from}&to=${to}`,
    {
      headers: {
        'Authorization': `Bearer ${API_TOKEN}`,
        'Accept': 'application/json'
      }
    }
  );
  
  const data = await response.json();
  return data;
}
```

---

## Support

Muammolar yuzaga kelsa, admin bilan bog'laning.
