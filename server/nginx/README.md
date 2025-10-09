# Nginx Configuration

Bu folder loyihaning barcha domenlari uchun Nginx konfiguratsiya fayllarini o'z ichiga oladi.

## Domenlar

1. **api.domain.lc** - Laravel API backend
2. **admin.domain.lc** - React Admin panel
3. **domain.lc** - React Frontend (asosiy sayt)
4. **file.domain.lc** - File viewer (tokenli fayl ko'rish)

## O'rnatish

### Ubuntu/Debian

```bash
# 1. Nginx o'rnatish
sudo apt-get update
sudo apt-get install nginx

# 2. Konfiguratsiya fayllarini nusxalash
sudo cp server/nginx/*.conf /etc/nginx/sites-available/

# 3. Symlink yaratish (sites-enabled)
sudo ln -s /etc/nginx/sites-available/api.domain.lc.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/admin.domain.lc.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/domain.lc.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/file.domain.lc.conf /etc/nginx/sites-enabled/

# 4. Default konfigni o'chirish (agar kerak bo'lsa)
sudo rm /etc/nginx/sites-enabled/default

# 5. Konfiguratsiyani tekshirish
sudo nginx -t

# 6. Nginx qayta yuklash
sudo systemctl reload nginx
```

### CentOS/RHEL/AlmaLinux

```bash
# 1. Nginx o'rnatish
sudo dnf install nginx -y
# yoki CentOS 7/8 uchun:
# sudo yum install nginx -y

# 2. Nginx xizmatini yoqish
sudo systemctl enable nginx
sudo systemctl start nginx

# 3. Konfiguratsiya fayllarini nusxalash
sudo cp server/nginx/*.conf /etc/nginx/conf.d/

# 4. Konfiguratsiyani tekshirish
sudo nginx -t

# 5. Nginx qayta yuklash
sudo systemctl reload nginx

# 6. Firewall sozlamalari
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload

# 7. SELinux sozlamalari
sudo setsebool -P httpd_can_network_connect on
sudo setsebool -P httpd_unified on
```

## SSL Sertifikat (Let's Encrypt)

### Certbot o'rnatish

**Ubuntu/Debian:**
```bash
sudo apt-get install certbot python3-certbot-nginx
```

**CentOS/RHEL:**
```bash
sudo dnf install certbot python3-certbot-nginx -y
```

### SSL sertifikatlarni olish

```bash
# Har bir domen uchun alohida
sudo certbot --nginx -d api.domain.lc
sudo certbot --nginx -d admin.domain.lc
sudo certbot --nginx -d domain.lc -d www.domain.lc
sudo certbot --nginx -d file.domain.lc

# Yoki barcha domenlar uchun bitta buyruqda
sudo certbot --nginx -d api.domain.lc -d admin.domain.lc -d domain.lc -d www.domain.lc -d file.domain.lc
```

### SSL auto-renewal

```bash
# Certbot avtomatik yangilanishini tekshirish
sudo certbot renew --dry-run

# Cron job (avtomatik o'rnatilgan)
sudo systemctl status certbot.timer
```

## Directory Structure

Loyiha kataloglari quyidagicha bo'lishi kerak:

```
/var/www/html/
├── crm-backend/          # Laravel API
│   ├── public/           # Nginx root
│   ├── static/           # File storage
│   └── ...
├── crm-admin/            # React Admin
│   └── build/            # Production build
└── crm-frontend/         # React Frontend
    └── build/            # Production build
```

## Konfiguratsiyalarni sozlash

### 1. Path o'zgartirish

Agar loyihalaringiz boshqa joyda bo'lsa, har bir konfiguratsiya faylida `root` direktivasini o'zgartiring:

```nginx
# Masalan:
root /home/user/projects/crm-backend/public;
```

### 2. Domain o'zgartirish

Har bir faylda `server_name` direktivasini o'z domeningizga o'zgartiring:

```nginx
server_name api.yourdomain.com;
```

### 3. PHP-FPM versiyasi

PHP versiyangizga qarab socket path ni o'zgartiring:

```nginx
# PHP 8.4
fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;

# PHP 8.3
fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

# CentOS/RHEL uchun:
fastcgi_pass unix:/var/run/php-fpm/www.sock;
```

### 4. Storage path (file.domain.lc)

File viewer uchun storage yo'lini to'g'rilang:

```nginx
location /storage/ {
    alias /var/www/html/crm-backend/static/;
    # ...
}
```

## Konfiguratsiya tafsilotlari

### api.domain.lc - Laravel API

- **Maqsad:** Backend API
- **Root:** `/var/www/html/crm-backend/public`
- **Features:**
  - CORS headers (API uchun)
  - Large file upload support (100MB)
  - PHP-FPM processing
  - Static file caching

### admin.domain.lc - React Admin

- **Maqsad:** Admin panel
- **Root:** `/var/www/html/crm-admin/build`
- **Features:**
  - React Router support (try_files fallback)
  - Static asset caching (1 year)
  - Gzip compression
  - Source map hiding

### domain.lc - React Frontend

- **Maqsad:** Asosiy sayt
- **Root:** `/var/www/html/crm-frontend/build`
- **Features:**
  - React Router support
  - Static asset caching (1 year)
  - Gzip compression
  - www redirect support

### file.domain.lc - File Viewer

- **Maqsad:** Tokenli fayl ko'rish
- **Root:** `/var/www/html/crm-backend/public`
- **Features:**
  - Direct static file serving (`/storage/`)
  - Laravel file viewer routing (`/api/v1/files/view/{slug}`)
  - Large file transfer support
  - Restricted access (faqat file routes)

**URL Mapping:**
```
https://file.domain.lc/storage/2025/01/file.pdf
  → /var/www/html/crm-backend/static/2025/01/file.pdf

https://file.domain.lc/api/v1/files/view/abc123?token=...
  → Laravel controller (tokenni tekshiradi)
```

## PHP-FPM Sozlamalari

### Ubuntu/Debian

```bash
# PHP-FPM konfiguratsiya
sudo nano /etc/php/8.4/fpm/pool.d/www.conf

# Quyidagilarni tekshiring:
listen = /var/run/php/php8.4-fpm.sock
listen.owner = www-data
listen.group = www-data
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20

# PHP-FPM restart
sudo systemctl restart php8.4-fpm
```

### CentOS/RHEL

```bash
# PHP-FPM konfiguratsiya
sudo nano /etc/php-fpm.d/www.conf

# Quyidagilarni tekshiring:
listen = /var/run/php-fpm/www.sock
listen.owner = nginx
listen.group = nginx
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20

# PHP-FPM restart
sudo systemctl restart php-fpm
```

## Muammolarni hal qilish

### 502 Bad Gateway

```bash
# PHP-FPM ishlayotganini tekshirish
sudo systemctl status php8.4-fpm

# Socket mavjudligini tekshirish
ls -la /var/run/php/php8.4-fpm.sock

# Permission tekshirish
sudo chown www-data:www-data /var/run/php/php8.4-fpm.sock
```

### 413 Request Entity Too Large

```nginx
# Nginx konfigda:
client_max_body_size 100M;
```

```ini
# PHP konfigda: /etc/php/8.4/fpm/php.ini
upload_max_filesize = 100M
post_max_size = 100M
```

### React Router 404 errors

React apps uchun `try_files $uri $uri/ /index.html;` borligini tekshiring.

### Static files not serving

```bash
# Permission tekshirish
sudo chown -R www-data:www-data /var/www/html/crm-backend/static
sudo chmod -R 755 /var/www/html/crm-backend/static

# CentOS/RHEL
sudo chown -R nginx:nginx /var/www/html/crm-backend/static
```

### SELinux issues (CentOS/RHEL)

```bash
# SELinux context o'rnatish
sudo chcon -R -t httpd_sys_content_t /var/www/html/
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/crm-backend/storage

# Yoki SELinux ni vaqtincha o'chirish (testing uchun)
sudo setenforce 0
```

## Foydali komandalar

```bash
# Nginx sintaksisni tekshirish
sudo nginx -t

# Nginx qayta yuklash (yangi config bilan)
sudo systemctl reload nginx

# Nginx restart
sudo systemctl restart nginx

# Nginx statusni ko'rish
sudo systemctl status nginx

# Error loglarni ko'rish
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/api.domain.lc-error.log

# Access loglarni ko'rish
sudo tail -f /var/log/nginx/api.domain.lc-access.log
```

## Performance Tuning

### Nginx konfiguratsiya (/etc/nginx/nginx.conf)

```nginx
worker_processes auto;
worker_connections 1024;

http {
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript
               application/x-javascript application/xml+rss
               application/javascript application/json;

    # Client limits
    client_max_body_size 100M;
    client_body_buffer_size 128k;

    # Proxy buffers
    proxy_buffer_size 4k;
    proxy_buffers 8 4k;
    proxy_busy_buffers_size 8k;
}
```

## Security Best Practices

1. **SSL faqat** - HTTP ni HTTPS ga redirect qiling
2. **Security headers** - XSS, clickjacking dan himoya
3. **Hide version** - `server_tokens off;` (nginx.conf da)
4. **Rate limiting** - DDoS dan himoya
5. **IP whitelisting** - Admin panel uchun
6. **Regular updates** - Nginx va SSL sertifikatlar

## Production Deployment Checklist

- [ ] Barcha domenlar uchun SSL sertifikat o'rnatilgan
- [ ] HTTP HTTPS ga redirect qilingan
- [ ] PHP-FPM to'g'ri ishlayapti
- [ ] File permissions to'g'ri o'rnatilgan
- [ ] Error loglar monitoring qilingan
- [ ] Gzip compression yoqilgan
- [ ] Static file caching sozlangan
- [ ] Security headers qo'shilgan
- [ ] Firewall sozlangan (ports 80, 443)
- [ ] SELinux to'g'ri configured (CentOS/RHEL)
