# 🚀 Serverga yuklash va xavfsizlik qo'llanmasi

Bu qo'llanma Laravel Amaliyot Boshqaruv Tizimini production serverga xavfsiz yuklash va sozlash bo'yicha to'liq ko'rsatmalar beradi.

## 📋 Talablar

### Server talablari:
- PHP 8.1 yoki yuqori
- MySQL 5.7+ yoki MariaDB 10.3+
- Composer
- Apache 2.4+ yoki Nginx
- SSL sertifikati (Let's Encrypt tavsiya etiladi)
- SSH kirish huquqi

### Tavsiya etiladigan server sozlamalari:
- RAM: Minimum 1GB (2GB+ tavsiya etiladi)
- Disk: Minimum 5GB bo'sh joy
- PHP Extensions: mbstring, xml, pdo, pdo_mysql, openssl, json, tokenizer, fileinfo, bcmath

---

## 1. Loyihani serverga yuklash

### Usul 1: Git orqali (tavsiya etiladi)
```bash
# SSH orqali serverga kirish
ssh username@your-server-ip

# Loyiha papkasini yaratish
mkdir -p /home/username/laravel
cd /home/username/laravel

# Git repository dan yuklash
git clone https://github.com/your-username/your-repo.git .

# Yoki SSH orqali
git clone git@github.com:your-username/your-repo.git .

# Dependencies o'rnatish
composer install --no-dev --optimize-autoloader

# Node packages (agar kerak bo'lsa)
npm install --production
npm run build
```

### Usul 2: FTP/SFTP orqali
```bash
# Server strukturasi:
# /home/username/
#   ├── laravel/          (asosiy loyiha - app, config, database, routes, etc.)
#   └── public_html/      (public papka - index.php, assets)

# FileZilla yoki WinSCP ishlatib barcha fayllarni yuklang
# DIQQAT: .env faylini yuklamang! Serverda yaratiladi
```

### Usul 3: ZIP arxiv orqali
```bash
# Mahalliy kompyuterda
zip -r project.zip . -x "*.git*" "node_modules/*" "vendor/*" ".env"

# Serverga yuklash va ochish
cd /home/username/laravel
unzip project.zip
composer install --no-dev --optimize-autoloader
```

## 2. Xavfsizlik sozlamalari

### A. .env faylini sozlash
```bash
# .env faylini nusxalash
cp .env.production.example .env

# .env faylini tahrirlash
nano .env

# MUHIM: Quyidagi sozlamalarni o'zgartiring:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_strong_password

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Timezone
APP_TIMEZONE=Asia/Tashkent
SYSTEM_TIMEZONE=Asia/Tashkent
```

### B. APP_KEY generatsiya qilish
```bash
# Encryption key yaratish
php artisan key:generate

# Natijani tekshirish
php artisan tinker
>>> env('APP_KEY')
# "base64:..." ko'rinishida bo'lishi kerak
>>> exit
```

### C. Fayl va papka ruxsatlarini sozlash
```bash
# Owner o'rnatish (web server user)
sudo chown -R www-data:www-data /home/username/laravel

# Yoki cPanel/shared hosting uchun
chown -R username:username /home/username/laravel

# Storage va cache papkalariga yozish ruxsati
chmod -R 775 storage bootstrap/cache

# Public papka
chmod -R 755 public

# Boshqa papkalar
chmod -R 755 app config database routes resources

# .env faylini himoyalash
chmod 600 .env

# Ruxsatlarni tekshirish
ls -la storage/
ls -la bootstrap/cache/
```

### D. .htaccess xavfsizligini qo'shish
```bash
# .htaccess_security faylini public/.htaccess ga qo'shish
cd /home/username/laravel

# Backup olish
cp public/.htaccess public/.htaccess.backup

# Xavfsizlik qoidalarini qo'shish
cat .htaccess_security >> public/.htaccess

# Yoki qo'lda qo'shish
nano public/.htaccess
# .htaccess_security faylining mazmunini ko'chirib qo'ying

# HTTPS redirect ni yoqish (SSL o'rnatilgandan keyin)
# public/.htaccess da quyidagi qatorlarni izohdan chiqaring:
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### E. Xavfsizlik scriptini ishga tushirish
```bash
# Parollarni yangilash va xavfsizlikni sozlash
php secure_production.php

# Script sizdan so'raydi:
# 1. Yangi admin paroli (minimum 12 belgi, katta/kichik harf va raqam)
# 2. Boshqa foydalanuvchi parollarini yangilashni xohlaysizmi

# Script avtomatik bajaradi:
# - Admin parolini yangilaydi
# - Muhit sozlamalarini tekshiradi
# - Fayl ruxsatlarini tekshiradi
# - Keshlarni tozalaydi va optimizatsiya qiladi
```

## 3. SSL (HTTPS) o'rnatish

### Usul 1: cPanel orqali (Shared Hosting)
```bash
# 1. cPanel ga kirish
# 2. "SSL/TLS Status" ni toping
# 3. "Run AutoSSL" tugmasini bosing
# 4. Let's Encrypt avtomatik o'rnatiladi (2-3 daqiqa)
# 5. Domen yonida "AutoSSL certificate installed" yozuvi paydo bo'ladi

# Tekshirish:
# https://yourdomain.com ga kiring
# Brauzerda qulf belgisi ko'rinishi kerak
```

### Usul 2: Certbot orqali (VPS/Dedicated Server)
```bash
# Certbot o'rnatish (Ubuntu/Debian)
sudo apt update
sudo apt install certbot python3-certbot-apache

# Apache uchun SSL sertifikat olish
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Nginx uchun
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Interaktiv jarayonda:
# - Email kiriting (eslatmalar uchun)
# - Shartlarni qabul qiling
# - HTTP dan HTTPS ga redirect qilishni tanlang (2)

# Avtomatik yangilanishni tekshirish
sudo certbot renew --dry-run

# Cron job qo'shish (avtomatik yangilash)
sudo crontab -e
# Quyidagi qatorni qo'shing:
0 3 * * * certbot renew --quiet
```

### Usul 3: Cloudflare orqali (Bepul)
```bash
# 1. Cloudflare.com da ro'yxatdan o'ting
# 2. Domeningizni qo'shing
# 3. Nameserver larni o'zgartiring (domen provayderida)
# 4. SSL/TLS > Overview > Full (strict) tanlang
# 5. 24 soat ichida SSL faollashadi
```

### SSL o'rnatilgandan keyin:
```bash
# 1. .env faylida APP_URL ni yangilang
nano .env
APP_URL=https://yourdomain.com

# 2. public/.htaccess da HTTPS redirect ni yoqing
nano public/.htaccess
# Quyidagi qatorlarni izohdan chiqaring:
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# 3. Keshni yangilang
php artisan config:clear
php artisan config:cache

# 4. Tekshirish
curl -I https://yourdomain.com
# HTTP/2 200 va SSL ma'lumotlari ko'rinishi kerak
```

## 4. Database sozlash va migratsiya

### A. Database yaratish
```bash
# MySQL ga kirish
mysql -u root -p

# Database yaratish
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Foydalanuvchi yaratish va ruxsat berish
CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# cPanel da (phpMyAdmin orqali):
# 1. MySQL Databases > Create New Database
# 2. MySQL Users > Create New User
# 3. Add User to Database > All Privileges
```

### B. Migratsiyalarni ishga tushirish
```bash
cd /home/username/laravel

# Database ulanishini tekshirish
php artisan db:show

# Migratsiyalarni ishga tushirish
php artisan migrate --force

# Agar xato bo'lsa:
php artisan migrate:fresh --force
# DIQQAT: Bu barcha ma'lumotlarni o'chiradi!

# Migratsiya holatini tekshirish
php artisan migrate:status
```

### C. Admin va dastlabki ma'lumotlar
```bash
# Admin yaratish
php create_admin.php
# Yoki
php artisan db:seed --class=AdminSeeder

# Admin ma'lumotlari:
# Username: admin
# Password: admin0428 (yoki sizning parolingiz)
# Email: admin@admin.uz

# Test ma'lumotlar (faqat development/staging uchun)
# php seed_full_database.php
```

## 5. Optimizatsiya va kesh

### A. Laravel optimizatsiyasi
```bash
# Barcha keshlarni tozalash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Production uchun keshlash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Composer autoload optimizatsiyasi
composer dump-autoload --optimize --classmap-authoritative

# Optimizatsiyani tekshirish
php artisan optimize
```

### B. OPcache sozlash (PHP)
```bash
# php.ini ni tahrirlash
sudo nano /etc/php/8.1/apache2/php.ini

# Quyidagi sozlamalarni qo'shing/o'zgartiring:
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1

# Apache ni qayta ishga tushirish
sudo systemctl restart apache2
```

### C. Database optimizatsiyasi
```bash
# MySQL ga kirish
mysql -u root -p

# Database optimizatsiyasi
USE your_database_name;
OPTIMIZE TABLE users, students, groups, messages, notifications;

# Index qo'shish (agar kerak bo'lsa)
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE users ADD INDEX idx_username (username);
```

## 6. Web Server sozlamalari (Apache/Nginx)

### Apache Virtual Host:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /home/username/laravel/public
    
    <Directory /home/username/laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /home/username/laravel/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
    
    <Directory /home/username/laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx:
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /home/username/laravel/public;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 7. Xavfsizlik tekshiruvi va monitoring

### A. Xavfsizlik tekshiruvi ro'yxati
```bash
# ✅ Quyidagi barcha punktlarni tekshiring:

# 1. Environment sozlamalari
grep "APP_DEBUG" .env          # false bo'lishi kerak
grep "APP_ENV" .env            # production bo'lishi kerak
grep "APP_URL" .env            # https:// bilan boshlanishi kerak

# 2. Fayl ruxsatlari
ls -la .env                    # 600 yoki 400 bo'lishi kerak
ls -la storage/                # 775 bo'lishi kerak
ls -la bootstrap/cache/        # 775 bo'lishi kerak

# 3. .htaccess xavfsizligi
grep "RewriteRule.*\.env" public/.htaccess  # .env bloklangan bo'lishi kerak

# 4. SSL sertifikat
curl -I https://yourdomain.com | grep "HTTP"  # 200 OK bo'lishi kerak

# 5. Rate limiting
# Login sahifasiga 6 marta noto'g'ri parol bilan kirish urinib ko'ring
# "Juda ko'p urinish" xabari chiqishi kerak
```

### B. Xavfsizlik skanerlari
```bash
# Composer audit (zaifliklarni tekshirish)
composer audit

# Agar zaifliklar topilsa:
composer update --with-dependencies

# Laravel security checker
composer require --dev enlightn/security-checker
php artisan security:check

# SSL sertifikat tekshiruvi
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Yoki online:
# https://www.ssllabs.com/ssltest/
```

### C. Xavfsizlik headerlarini tekshirish
```bash
# Security headerlarni tekshirish
curl -I https://yourdomain.com

# Quyidagilar bo'lishi kerak:
# X-Frame-Options: SAMEORIGIN
# X-XSS-Protection: 1; mode=block
# X-Content-Type-Options: nosniff
# Strict-Transport-Security: max-age=31536000

# Yoki online:
# https://securityheaders.com
```

## 8. Backup va monitoring

### A. Backup tizimi
```bash
# Laravel backup package o'rnatish
composer require spatie/laravel-backup

# Config faylini publish qilish
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"

# config/backup.php ni sozlash
nano config/backup.php

# Manual backup
php artisan backup:run

# Cron job qo'shish (har kuni soat 2:00 da)
crontab -e
0 2 * * * cd /home/username/laravel && php artisan backup:run >> /dev/null 2>&1

# Backup fayllarini tekshirish
ls -lh storage/app/backups/
```

### B. Database backup
```bash
# Manual database backup
mysqldump -u your_db_user -p your_database_name > backup_$(date +%Y%m%d).sql

# Cron job (har kuni soat 3:00 da)
0 3 * * * mysqldump -u your_db_user -pyour_password your_database_name | gzip > /home/username/backups/db_$(date +\%Y\%m\%d).sql.gz

# Backup restore
mysql -u your_db_user -p your_database_name < backup_20241022.sql
```

### C. Log monitoring
```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Oxirgi 100 qator
tail -n 100 storage/logs/laravel.log

# Xatolarni qidirish
grep "ERROR" storage/logs/laravel.log

# Failed login attempts
grep "login_failed" storage/logs/laravel.log

# Log rotation sozlash (logrotate)
sudo nano /etc/logrotate.d/laravel

# Quyidagini qo'shing:
/home/username/laravel/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### D. Uptime monitoring
```bash
# Simple uptime check script
nano /home/username/scripts/check_uptime.sh

#!/bin/bash
URL="https://yourdomain.com"
if curl -s --head $URL | grep "200 OK" > /dev/null; then
    echo "Site is UP"
else
    echo "Site is DOWN" | mail -s "Website Down Alert" admin@yourdomain.com
fi

# Cron job (har 5 daqiqada)
*/5 * * * * /home/username/scripts/check_uptime.sh

# Yoki online monitoring:
# - UptimeRobot.com (bepul)
# - Pingdom.com
# - StatusCake.com
```

## 9. Performance monitoring

### A. Laravel Telescope (development/staging)
```bash
# FAQAT development/staging uchun!
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Production da o'chirish
# .env da:
TELESCOPE_ENABLED=false
```

### B. Server monitoring
```bash
# Disk space
df -h

# Memory usage
free -m

# CPU usage
top

# Apache/Nginx status
sudo systemctl status apache2
sudo systemctl status nginx

# MySQL status
sudo systemctl status mysql

# PHP-FPM status (Nginx uchun)
sudo systemctl status php8.1-fpm
```

## 10. Muammolarni hal qilish

### A. 500 Internal Server Error
```bash
# 1. Loglarni tekshirish
tail -n 50 storage/logs/laravel.log
tail -n 50 /var/log/apache2/error.log

# 2. Ruxsatlarni tekshirish
ls -la storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 3. Keshni tozalash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. .env faylini tekshirish
cat .env | grep "APP_KEY"  # Bo'sh bo'lmasligi kerak

# 5. Composer autoload
composer dump-autoload
```

### B. Database connection error
```bash
# 1. .env da database sozlamalarini tekshirish
cat .env | grep "DB_"

# 2. MySQL ishlab turganini tekshirish
sudo systemctl status mysql

# 3. Database ulanishini test qilish
php artisan db:show

# 4. MySQL user ruxsatlarini tekshirish
mysql -u root -p
SHOW GRANTS FOR 'your_db_user'@'localhost';
```

### C. HTTPS ishlamayotgan bo'lsa
```bash
# 1. SSL sertifikat amal qilish muddatini tekshirish
openssl x509 -in /path/to/cert.pem -noout -dates

# 2. .env da APP_URL ni tekshirish
grep "APP_URL" .env  # https:// bo'lishi kerak

# 3. .htaccess da HTTPS redirect
grep "RewriteCond.*HTTPS" public/.htaccess

# 4. Apache SSL module yoqilganini tekshirish
sudo a2enmod ssl
sudo systemctl restart apache2

# 5. Certbot yangilash
sudo certbot renew --dry-run
```

### D. Permission denied errors
```bash
# Storage va cache papkalariga ruxsat berish
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# SELinux muammosi (CentOS/RHEL)
sudo setenforce 0
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_unified 1
```

### E. Composer memory limit error
```bash
# Composer memory limit oshirish
php -d memory_limit=-1 /usr/local/bin/composer install

# Yoki
COMPOSER_MEMORY_LIMIT=-1 composer install
```

## 11. Production checklist

### Yuklashdan oldin:
- [ ] Barcha testlar o'tdi
- [ ] .env.production.example to'ldirildi
- [ ] Database backup olindi
- [ ] Git repository yangilandi
- [ ] Dependencies yangilandi (`composer update`)

### Yuklashdan keyin:
- [ ] .env fayli to'g'ri sozlandi
- [ ] APP_KEY generatsiya qilindi
- [ ] Database migratsiyalari bajarildi
- [ ] Admin yaratildi va parol o'zgartirildi
- [ ] SSL sertifikati o'rnatildi
- [ ] HTTPS redirect yoqildi
- [ ] Fayl ruxsatlari to'g'ri o'rnatildi
- [ ] .htaccess xavfsizligi qo'shildi
- [ ] Keshlar optimizatsiya qilindi
- [ ] Backup tizimi sozlandi
- [ ] Monitoring o'rnatildi
- [ ] Barcha sahifalar ishlayotganini tekshirdik
- [ ] Login/logout ishlayotganini tekshirdik
- [ ] Rate limiting ishlayotganini tekshirdik

## 12. Qo'shimcha resurslar

### Dokumentatsiya:
- Laravel Deployment: https://laravel.com/docs/deployment
- Laravel Security: https://laravel.com/docs/security
- Let's Encrypt: https://letsencrypt.org/
- Certbot: https://certbot.eff.org/

### Xavfsizlik tekshiruv vositalari:
- SSL Labs: https://www.ssllabs.com/ssltest/
- Security Headers: https://securityheaders.com
- Mozilla Observatory: https://observatory.mozilla.org/

### Monitoring vositalari:
- UptimeRobot: https://uptimerobot.com (bepul)
- Pingdom: https://www.pingdom.com
- New Relic: https://newrelic.com
- Sentry: https://sentry.io (error tracking)

### Backup vositalari:
- Spatie Laravel Backup: https://github.com/spatie/laravel-backup
- Laravel Backup Panel: https://github.com/backup-manager/laravel

---

## 📞 Yordam va qo'llab-quvvatlash

Muammolar yuzaga kelsa:
1. `storage/logs/laravel.log` ni tekshiring
2. Server error loglarini ko'ring (`/var/log/apache2/error.log`)
3. Ushbu qo'llanmadagi "Muammolarni hal qilish" bo'limiga qarang
4. Laravel dokumentatsiyasiga murojaat qiling

---

**⚠️ MUHIM ESLATMA:** 
- Serverga yuklashdan oldin mahalliy muhitda to'liq test qiling!
- Production ga yuklashdan oldin backup oling!
- Xavfsizlik sozlamalarini e'tiborsiz qoldirmang!
- Parollarni hech qachon hardcode qilmang!
- .env faylini git ga yuklamang!

**✅ Muvaffaqiyatli deployment!**
