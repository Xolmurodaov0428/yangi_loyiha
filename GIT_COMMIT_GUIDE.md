# 📦 GitHub ga yangilash qo'llanmasi

## Qisqa yo'l (PowerShell/CMD)

```bash
# 1. Loyiha papkasiga o'ting
cd c:\xampp\htdocs\amaliyot

# 2. Git holatini tekshiring
git status

# 3. Barcha o'zgarishlarni qo'shing
git add .

# 4. Commit yarating
git commit -m "feat: Add comprehensive security features and password reset system

- Added rate limiting for login, registration, and password reset
- Implemented secure password reset with cryptographic tokens
- Enhanced AuthController with activity logging and IP tracking
- Created security configuration files (.htaccess_security, secure_production.php)
- Added production deployment guide and security documentation
- Disabled public registration (admin-only user management)
- Improved password strength requirements (8+ chars, uppercase, lowercase, numbers)
- Added development mode password reset URL display
- Created comprehensive security and deployment documentation"

# 5. GitHub ga push qiling
git push origin main
# Yoki agar 'master' branch bo'lsa:
# git push origin master
```

---

## Batafsil qo'llanma

### 1. Git ni tekshirish

```bash
# Git o'rnatilganini tekshirish
git --version

# Agar o'rnatilmagan bo'lsa:
# https://git-scm.com/download/win dan yuklab oling
```

### 2. Git repository ni sozlash (agar kerak bo'lsa)

```bash
# Loyiha papkasiga o'ting
cd c:\xampp\htdocs\amaliyot

# Git repository yaratish (agar mavjud bo'lmasa)
git init

# Remote repository qo'shish
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git

# Yoki SSH orqali:
# git remote add origin git@github.com:YOUR_USERNAME/YOUR_REPO.git

# Remote ni tekshirish
git remote -v
```

### 3. O'zgarishlarni ko'rish

```bash
# Barcha o'zgarishlarni ko'rish
git status

# Qaysi fayllar o'zgarganini batafsil ko'rish
git diff

# Yangi fayllar ro'yxati
git ls-files --others --exclude-standard
```

### 4. Fayllarni staging ga qo'shish

```bash
# Barcha o'zgarishlarni qo'shish
git add .

# Yoki alohida fayllarni qo'shish
git add app/Http/Controllers/AuthController.php
git add resources/views/auth/login.blade.php
git add secure_production.php
git add .htaccess_security
git add .env.production.example
git add DEPLOYMENT_GUIDE.md
git add SECURITY_SUMMARY.md
git add PASSWORD_RESET_GUIDE.md
git add REGISTER_DISABLED.md

# Staging holatini tekshirish
git status
```

### 5. Commit yaratish

```bash
# Qisqa commit message
git commit -m "Add security features and password reset"

# Yoki batafsil commit message (tavsiya etiladi)
git commit -m "feat: Add comprehensive security features

Security improvements:
- Rate limiting for authentication endpoints
- Secure password reset with token expiration
- Activity logging for security events
- IP tracking for suspicious activities
- Strong password requirements

New features:
- Disabled public registration
- Admin-only user management
- Production deployment scripts
- Comprehensive security documentation

Files added:
- secure_production.php
- .htaccess_security
- DEPLOYMENT_GUIDE.md
- SECURITY_SUMMARY.md
- PASSWORD_RESET_GUIDE.md
- REGISTER_DISABLED.md

Files modified:
- AuthController.php (rate limiting, logging)
- login.blade.php (removed registration link)
- forgot-password.blade.php (dev mode URL display)
- reset-password.blade.php (password requirements)
- routes/web.php (disabled registration routes)
- .env.production.example (comprehensive config)"
```

### 6. GitHub ga push qilish

```bash
# Main branch ga push
git push origin main

# Yoki master branch ga
git push origin master

# Agar birinchi marta push qilayotgan bo'lsangiz:
git push -u origin main

# Force push (ehtiyotkorlik bilan!)
# git push -f origin main
```

---

## Commit message best practices

### Conventional Commits format:

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Type lar:

- `feat`: Yangi feature
- `fix`: Bug fix
- `docs`: Dokumentatsiya
- `style`: Code formatting
- `refactor`: Code refactoring
- `test`: Test qo'shish
- `chore`: Maintenance

### Misollar:

```bash
# Feature
git commit -m "feat(auth): add rate limiting to login endpoint"

# Bug fix
git commit -m "fix(password-reset): correct token expiration time"

# Documentation
git commit -m "docs: add comprehensive deployment guide"

# Security
git commit -m "security(auth): implement brute force protection"

# Multiple changes
git commit -m "feat: add security features and documentation

- Implement rate limiting
- Add password reset system
- Create deployment guides
- Enhance security headers"
```

---

## Branch strategy (ixtiyoriy)

### Feature branch yaratish:

```bash
# Yangi branch yaratish
git checkout -b feature/security-improvements

# O'zgarishlarni commit qilish
git add .
git commit -m "feat: add security features"

# GitHub ga push
git push origin feature/security-improvements

# Main branch ga merge qilish
git checkout main
git merge feature/security-improvements
git push origin main
```

---

## Muammolarni hal qilish

### 1. "Permission denied" xatosi

```bash
# SSH key yaratish
ssh-keygen -t ed25519 -C "your_email@example.com"

# SSH key ni GitHub ga qo'shish
# 1. cat ~/.ssh/id_ed25519.pub
# 2. GitHub > Settings > SSH Keys > New SSH key
# 3. Key ni paste qiling
```

### 2. "Authentication failed" xatosi

```bash
# Personal Access Token yaratish
# GitHub > Settings > Developer settings > Personal access tokens > Generate new token

# Token bilan push qilish
git remote set-url origin https://YOUR_TOKEN@github.com/YOUR_USERNAME/YOUR_REPO.git
```

### 3. "Merge conflict" xatosi

```bash
# Remote dan yangilanishlarni olish
git pull origin main

# Conflict larni qo'lda hal qiling
# Keyin:
git add .
git commit -m "fix: resolve merge conflicts"
git push origin main
```

### 4. ".env fayli push qilinmoqda" xatosi

```bash
# .env ni git dan olib tashlash
git rm --cached .env

# .gitignore ga qo'shish (allaqachon bor)
echo ".env" >> .gitignore

# Commit va push
git add .gitignore
git commit -m "chore: remove .env from git"
git push origin main
```

### 5. "Large file" xatosi

```bash
# Git LFS o'rnatish (agar kerak bo'lsa)
git lfs install

# Katta fayllarni track qilish
git lfs track "*.zip"
git lfs track "*.sql"

# .gitattributes ni commit qilish
git add .gitattributes
git commit -m "chore: add git lfs tracking"
```

---

## Pre-commit checklist

Commit qilishdan oldin tekshiring:

- [ ] `.env` fayli `.gitignore` da
- [ ] Sensitive ma'lumotlar yo'q (parollar, API keys)
- [ ] Code test qilindi
- [ ] Dokumentatsiya yangilandi
- [ ] Commit message aniq va tushunarli
- [ ] Barcha kerakli fayllar qo'shildi

---

## GitHub Actions (CI/CD) - Ixtiyoriy

### `.github/workflows/laravel.yml`:

```yaml
name: Laravel CI

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  laravel-tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install Dependencies
      run: composer install --no-interaction --prefer-dist
      
    - name: Run Tests
      run: php artisan test
      
    - name: Security Check
      run: composer audit
```

---

## Foydali Git buyruqlari

```bash
# Oxirgi commit ni o'zgartirish
git commit --amend -m "New message"

# Oxirgi commit ni bekor qilish
git reset --soft HEAD~1

# Barcha o'zgarishlarni bekor qilish
git reset --hard HEAD

# Muayyan faylni qaytarish
git checkout -- filename.php

# Commit tarixini ko'rish
git log --oneline --graph --all

# Muayyan faylning tarixini ko'rish
git log --follow filename.php

# Branch larni ko'rish
git branch -a

# Remote ni yangilash
git fetch origin

# Stash (vaqtinchalik saqlash)
git stash
git stash pop
```

---

## Xavfsizlik eslatmalari

⚠️ **Hech qachon commit qilmang:**
- `.env` fayli
- Database backup fayllari
- API keys va secrets
- Private keys (SSH, SSL)
- Parollar
- Sensitive user data

✅ **Har doim commit qiling:**
- `.env.example` fayli
- Dokumentatsiya
- Source code
- Public assets
- Configuration templates

---

## Qo'shimcha resurslar

- **Git Documentation:** https://git-scm.com/doc
- **GitHub Guides:** https://guides.github.com/
- **Conventional Commits:** https://www.conventionalcommits.org/
- **Git Cheat Sheet:** https://education.github.com/git-cheat-sheet-education.pdf

---

**✅ Tayyor! Endi GitHub ga push qilishingiz mumkin!**

```bash
cd c:\xampp\htdocs\amaliyot
git add .
git commit -m "feat: Add comprehensive security features and documentation"
git push origin main
```
