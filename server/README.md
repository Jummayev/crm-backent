# Server Configuration

Bu folder serverda ishlatadigan konfiguratsiya fayllarini o'z ichiga oladi.

## Supervisor Queue Workers

Supervisor - bu Linux process monitorlash va nazorat qilish vositasi. Laravel queue workerlarini doimiy ishlab turishi uchun ishlatiladi.

### O'rnatish (Ubuntu/Debian)

1. **Supervisor o'rnatish:**
```bash
sudo apt-get update
sudo apt-get install supervisor
```

2. **Konfiguratsiya fayllarini nusxalash:**
```bash
sudo cp server/supervisor/*.conf /etc/supervisor/conf.d/
```

3. **Supervisor yangilash:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
```

4. **Workerlarni ishga tushirish:**
```bash
sudo supervisorctl start laravel-file-access-logs-worker:*
sudo supervisorctl start laravel-default-worker:*
```

### O'rnatish (CentOS/RHEL/AlmaLinux)

1. **EPEL repository o'rnatish (agar yo'q bo'lsa):**
```bash
sudo dnf install epel-release -y
# yoki CentOS 7/8 uchun:
# sudo yum install epel-release -y
```

2. **Supervisor o'rnatish:**
```bash
sudo dnf install supervisor -y
# yoki CentOS 7/8 uchun:
# sudo yum install supervisor -y
```

3. **Supervisor xizmatini yoqish va ishga tushirish:**
```bash
sudo systemctl enable supervisord
sudo systemctl start supervisord
```

4. **Konfiguratsiya fayllarini nusxalash:**
```bash
sudo cp server/supervisor/*.conf /etc/supervisord.d/
```

5. **Supervisor yangilash:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
```

6. **Workerlarni ishga tushirish:**
```bash
sudo supervisorctl start laravel-file-access-logs-worker:*
sudo supervisorctl start laravel-default-worker:*
```

7. **Firewall sozlamalari (agar kerak bo'lsa):**
```bash
# CentOS/RHEL da SELinux va firewall sozlash
sudo setsebool -P httpd_can_network_connect on
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

### Queue Workerlar

#### 1. File Access Logs Worker (`laravel-file-access-logs-worker.conf`)
- **Maqsad:** Private faylarga kirish loglarini yozish
- **Queue:** `file-access-logs`
- **Processlar soni:** 2
- **Log fayl:** `storage/logs/file-access-logs-worker.log`

#### 2. Default Worker (`laravel-default-worker.conf`)
- **Maqsad:** Barcha boshqa joblarni bajarish
- **Queue:** `default`
- **Processlar soni:** 4
- **Log fayl:** `storage/logs/default-worker.log`

### Supervisor Komandalar

```bash
# Barcha workerlar holatini ko'rish
sudo supervisorctl status

# Muayyan workerni qayta ishga tushirish
sudo supervisorctl restart laravel-file-access-logs-worker:*
sudo supervisorctl restart laravel-default-worker:*

# Workerni to'xtatish
sudo supervisorctl stop laravel-file-access-logs-worker:*

# Barcha workerlarni qayta ishga tushirish
sudo supervisorctl restart all

# Loglarni ko'rish
sudo supervisorctl tail -f laravel-file-access-logs-worker:laravel-file-access-logs-worker_00 stdout
```

### Konfiguratsiya Parametrlari

- `process_name`: Process nomi
- `command`: Ishga tushadigan buyruq
- `autostart`: Supervisor ishga tushganda avtomatik ishga tushirish
- `autorestart`: Xatolik bo'lganda avtomatik qayta ishga tushirish
- `user`: Qaysi user ostida ishlaydi (odatda `www-data`)
- `numprocs`: Nechta process ishga tushirish
- `redirect_stderr`: Xatolarni stdout ga yo'naltirish
- `stdout_logfile`: Log fayl joylashuvi
- `stopwaitsecs`: Process to'xtatishda kutish vaqti (soniyalarda)

### Important Notes

1. **User o'zgartirish:**
   - Ubuntu/Debian: `user=www-data`
   - CentOS/RHEL: `user=nginx` yoki `user=apache`
   - Agar serverda boshqa user ishlatilsa (masalan, `ubuntu`, `forge`), konfiguratsiya fayllarini mos ravishda o'zgartiring.

2. **Path o'zgartirish:**
   - Agar loyiha boshqa joyda bo'lsa, konfiguratsiya faylidagi `/var/www/html/crm-backend` ni to'g'ri path ga o'zgartiring.

3. **CentOS/RHEL uchun konfiguratsiya joylashuvi:**
   - Ubuntu/Debian: `/etc/supervisor/conf.d/`
   - CentOS/RHEL: `/etc/supervisord.d/`

4. **Log fayllar:**
   - Log fayllar `storage/logs/` papkasida saqlanadi
   - Log fayllarni vaqti-vaqti bilan tozalash kerak

5. **Queue Driver:**
   - `.env` faylda `QUEUE_CONNECTION` ni `database` yoki `redis` ga o'rnatishni unutmang
   - Development uchun: `QUEUE_CONNECTION=sync` (joblar darhol bajariladi)
   - Production uchun: `QUEUE_CONNECTION=database` yoki `redis`

### Muammolarni hal qilish

#### Ubuntu/Debian
```bash
# Supervisor statusni tekshirish
sudo systemctl status supervisor

# Supervisor loglarini ko'rish
sudo tail -f /var/log/supervisor/supervisord.log

# Worker loglarini ko'rish
tail -f storage/logs/file-access-logs-worker.log
tail -f storage/logs/default-worker.log

# Queue jadvalidagi joblarni ko'rish
php artisan queue:monitor

# Failed joblarni ko'rish
php artisan queue:failed

# Failed jobni qayta bajarish
php artisan queue:retry {id}
php artisan queue:retry all
```

#### CentOS/RHEL
```bash
# Supervisor statusni tekshirish
sudo systemctl status supervisord

# Supervisor loglarini ko'rish
sudo tail -f /var/log/supervisor/supervisord.log

# Worker loglarini ko'rish
tail -f storage/logs/file-access-logs-worker.log
tail -f storage/logs/default-worker.log

# SELinux muammolari uchun
sudo ausearch -m avc -ts recent
sudo setenforce 0  # Vaqtincha o'chirish (testing uchun)
# Doimiy o'chirish uchun (tavsiya etilmaydi):
# sudo nano /etc/selinux/config
# SELINUX=disabled

# Permission muammolari
sudo chown -R nginx:nginx /var/www/html/crm-backend/storage
sudo chmod -R 775 /var/www/html/crm-backend/storage
```

### Production Deployment

Kod yangilanganda workerlarni qayta ishga tushirish kerak:

```bash
# Deploy script ichida:
php artisan config:clear
php artisan cache:clear
sudo supervisorctl restart all
```
