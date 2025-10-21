# 🚀 Tezkor Boshlash - Rahbar Paneli Yangi Funksiyalar

## 📋 Qisqacha Ko'rsatma

### 1️⃣ Bildirishnomalar

#### Bildirishnomalarni Ko'rish
1. **Navbar'dagi qo'ng'iroq belgisini bosing** 🔔
   - Dropdown menyuda oxirgi 5 ta bildirishnoma ko'rinadi
   - Qizil badge o'qilmagan bildirishnomalar sonini ko'rsatadi

2. **"Barchasini ko'rish" tugmasini bosing**
   - To'liq bildirishnomalar sahifasiga o'tasiz
   - `/supervisor/notifications` URL

#### Bildirishnomalarni Boshqarish
- **O'qilgan deb belgilash**: Har bir bildirishnomadagi ✓ tugmasini bosing
- **Barchasini o'qilgan**: "Barchasini o'qilgan deb belgilash" tugmasini bosing
- **O'chirish**: Har bir bildirishnomadagi 🗑️ tugmasini bosing
- **Filtrlash**: "Barchasi", "O'qilmaganlar", "O'qilganlar" tablarini tanlang

---

### 2️⃣ Profil

#### Profilga Kirish
1. **Navbar'dagi profil tugmasini bosing** 👤
2. Dropdown menyudan **"Profil"** ni tanlang
3. Yoki sidebar'dan **"Profil"** havolasini bosing

#### Profil Ma'lumotlarini Tahrirlash
1. Profil sahifasida **"Profil ma'lumotlarini tahrirlash"** formasini toping
2. Quyidagi maydonlarni to'ldiring:
   - **Ism Familiya** (majburiy)
   - **Username** (ixtiyoriy)
   - **Email** (majburiy, noyob)
3. **"Saqlash"** tugmasini bosing

#### Parolni O'zgartirish
1. Profil sahifasida **"Parolni o'zgartirish"** formasini toping
2. Quyidagi maydonlarni to'ldiring:
   - **Joriy parol** (majburiy)
   - **Yangi parol** (kamida 8 ta belgi)
   - **Parolni tasdiqlash** (yangi parolni takrorlang)
3. **"Parolni o'zgartirish"** tugmasini bosing

#### Faoliyat Tarixini Ko'rish
1. Profil sahifasida **"Faoliyat tarixi"** kartasini toping
2. **"Ko'rish"** tugmasini bosing
3. Tizimda amalga oshirilgan barcha harakatlarni ko'rasiz

---

## 🎯 Asosiy URL'lar

```
Bildirishnomalar:
/supervisor/notifications

Profil:
/supervisor/profile

Faoliyat tarixi:
/supervisor/profile/activity-logs
```

---

## 💡 Maslahatlar

### Bildirishnomalar uchun:
- ✅ Bildirishnomalar har 30 soniyada avtomatik yangilanadi
- ✅ Davomat kiritganingizda avtomatik bildirishnoma yaratiladi
- ✅ Badge raqami 99 dan oshsa "99+" ko'rsatiladi
- ✅ Dropdown menyuda faqat oxirgi 5 ta bildirishnoma ko'rinadi

### Profil uchun:
- ✅ Email noyob bo'lishi kerak
- ✅ Parol kamida 8 ta belgidan iborat bo'lishi kerak
- ✅ Joriy parolni to'g'ri kiritmasangiz, yangi parol saqlanmaydi
- ✅ Barcha o'zgarishlar activity log'da saqlanadi

---

## 🎨 Interfeys Elementlari

### Navbar (Yuqori panel)
```
[☰ Menyu] [Rahbar Paneli]  [🔔 Bildirishnomalar] [👤 Profil] [Chiqish]
```

### Sidebar (Chap panel)
```
ASOSIY
  📊 Dashboard

MODULLAR
  👥 Talabalar
  📖 Kundaliklar
  ✅ Davomat
  ⭐ Baholash
  📄 Hujjatlar

SOZLAMALAR
  🔔 Bildirishnomalar
  👤 Profil
```

---

## ⚡ Tezkor Klaviatura Tugmalari

Hozircha klaviatura tugmalari qo'llab-quvvatlanmaydi, lekin kelajakda qo'shilishi mumkin:
- `Ctrl + N` - Bildirishnomalar
- `Ctrl + P` - Profil
- `Ctrl + Shift + N` - Yangi bildirishnoma

---

## 🔍 Qidiruv

Bildirishnomalar sahifasida qidiruv funksiyasi hozircha mavjud emas, lekin filtrlash orqali kerakli bildirishnomalarni topishingiz mumkin:
1. "O'qilmaganlar" - faqat yangi bildirishnomalar
2. "O'qilganlar" - faqat o'qilgan bildirishnomalar
3. "Barchasi" - barcha bildirishnomalar

---

## 📱 Mobil Qurilmalarda

### Bildirishnomalar:
- Navbar'dagi qo'ng'iroq belgisi har doim ko'rinadi
- Dropdown menyu to'liq ekranga moslashadi
- Swipe harakatlari qo'llab-quvvatlanmaydi (hozircha)

### Profil:
- Barcha formalar mobil qurilmalarga moslashtirilgan
- Katta tugmalar - oson bosish uchun
- Responsive layout

---

## ❓ Tez-tez So'raladigan Savollar

### Q: Bildirishnomalar necha vaqt saqlanadi?
**A:** Bildirishnomalar o'chirilmaguncha saqlanadi. Siz istalgan vaqtda o'chirishingiz mumkin.

### Q: Parolni unutsam nima qilaman?
**A:** "Parolni unutdim" funksiyasidan foydalaning (login sahifasida).

### Q: Email o'zgartirsam, qayta login qilishim kerakmi?
**A:** Yo'q, email o'zgartirish avtomatik logout qilmaydi.

### Q: Bildirishnomalarni email orqali olsam bo'ladimi?
**A:** Hozircha yo'q, lekin kelajakda qo'shilishi rejalashtirilgan.

### Q: Profil rasmini qo'shsam bo'ladimi?
**A:** Hozircha yo'q, lekin kelajakda qo'shilishi rejalashtirilgan.

---

## 🆘 Yordam Kerakmi?

Agar muammo yuzaga kelsa:
1. Sahifani yangilang (F5)
2. Cache'ni tozalang (Ctrl + Shift + R)
3. Boshqa brauzerda sinab ko'ring
4. Administratorga murojaat qiling

---

## ✅ Tekshirish Ro'yxati

Yangi funksiyalarni sinab ko'rish uchun:

- [ ] Bildirishnomalar dropdown'ini ochish
- [ ] Bildirishnomalar sahifasiga o'tish
- [ ] Bildirishnomani o'qilgan deb belgilash
- [ ] Bildirishnomani o'chirish
- [ ] Profil sahifasiga o'tish
- [ ] Profil ma'lumotlarini tahrirlash
- [ ] Parolni o'zgartirish
- [ ] Faoliyat tarixini ko'rish
- [ ] Davomat kiritib, bildirishnoma yaratilishini tekshirish

---

**Omad tilaymiz! 🎉**

Agar qo'shimcha yordam kerak bo'lsa, `SUPERVISOR_FEATURES.md` faylini o'qing.
