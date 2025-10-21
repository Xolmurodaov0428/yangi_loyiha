# Apache Virtual Host Sozlash

## Muammo
- Hozir: `http://localhost/amaliyot/public/supervisor/...`
- Kerak: `http://amaliyot.local/supervisor/...`

## Yechim

### 1. Apache Virtual Host qo'shish

**Fayl**: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Quyidagini qo'shing:

```apache
<VirtualHost *:80>
    ServerName amaliyot.local
    DocumentRoot "C:/xampp/htdocs/amaliyot/public"
    
    <Directory "C:/xampp/htdocs/amaliyot/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/amaliyot-error.log"
    CustomLog "logs/amaliyot-access.log" common
</VirtualHost>
```

### 2. Hosts faylini tahrirlash

**Fayl**: `C:\Windows\System32\drivers\etc\hosts`

Administrator sifatida ochib, quyidagini qo'shing:

```
127.0.0.1    amaliyot.local
```

### 3. Apache'ni qayta ishga tushirish

XAMPP Control Panel'da:
1. Apache'ni to'xtating (Stop)
2. Qayta ishga tushiring (Start)

### 4. Brauzerda ochish

```
http://amaliyot.local/supervisor/dashboard
```

## Foydalari

- ✅ Qisqa URL: `/supervisor/...` o'rniga
- ✅ Route'lar to'g'ri ishlaydi
- ✅ Asset'lar to'g'ri yuklanadi
- ✅ Professional ko'rinish

## Alternativ (Tezkor test)

Agar virtual host sozlashni xohlamasangiz, har doim to'liq URL ishlatishingiz kerak:

```
http://localhost/amaliyot/public/supervisor/notifications
```

---

**Eslatma**: Virtual host sozlagandan keyin `.env` faylida `APP_URL` ni yangilang:

```env
APP_URL=http://amaliyot.local
```

So'ng:
```bash
php artisan config:clear
```
