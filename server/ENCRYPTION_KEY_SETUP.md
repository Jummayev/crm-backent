# File Token Encryption Key Setup

## Nega kerak?

File access tokenlarini `APP_KEY` dan alohida kalit bilan shifrlash:

1. **Security**: Agar `APP_KEY` o'zgarsa, faqat session va cookie buziladi, file tokenlar ishlashda davom etadi
2. **Tracking**: Public va private faylarga access tracking uchun token bilan URL yaratamiz
3. **Isolation**: File tokenlarni alohida kalitda saqlash xavfsizroq

## O'rnatish

### 1. Yangi kalit generatsiya qilish

```bash
# Yangi random key generate qilish
php artisan tinker --execute="echo 'base64:'.base64_encode(random_bytes(32));"
```

### 2. .env fayliga qo'shish

```env
# File Access Token Encryption Key (separate from APP_KEY)
FILE_TOKEN_KEY=base64:71MRBue6qhnWgqZg4E04rPYP7HZCZPeczQbt7x8ocko=
```

### 3. Config cache yangilash

```bash
php artisan config:cache
```

## Qanday ishlaydi?

### TokenService

```php
// Custom encrypter ishlatadi
private static function getEncrypter(): Encrypter
{
    $key = config('app.file_token_key') ?: config('app.key');
    return new Encrypter($key, config('app.cipher'));
}

// Tokenni shifrlash
$token = TokenService::generateToken($file, $userId, 7);

// Tokenni ochish
$payload = TokenService::decryptToken($token);
```

### Fallback

Agar `FILE_TOKEN_KEY` o'rnatilmagan bo'lsa, `APP_KEY` ishlatiladi (backward compatibility).

## Production deployment

```bash
# 1. Serverda yangi key generate qiling
php artisan tinker --execute="echo 'base64:'.base64_encode(random_bytes(32));"

# 2. .env ga qo'shing
echo "FILE_TOKEN_KEY=base64:..." >> .env

# 3. Cache yangilang
php artisan config:cache

# 4. Queue workerlarni restart qiling
sudo supervisorctl restart all
```

## Security Best Practices

1. **Never commit** `.env` to git
2. **Rotate keys** regularly (har yili yoki xavf bo'lsa)
3. **Backup keys** before rotation
4. **Use different keys** for different environments (dev, staging, prod)

## Muammolarni hal qilish

### "Unable to decrypt" xatosi

```bash
# Config cache tozalash
php artisan config:clear

# Key to'g'riligini tekshirish
php artisan tinker
>>> config('app.file_token_key')
```

### Key rotation

Agar kalitni o'zgartirsangiz, eski tokenlar ishlamay qoladi. Yangi tokenlar yaratiladi:

```bash
# 1. Yangi key o'rnatish
FILE_TOKEN_KEY=base64:new_key_here

# 2. Cache yangilash
php artisan config:cache

# 3. Eski tokenlar 7 kun ichida expire bo'ladi
```

## Nginx access log tracking

Tokenlar bilan qaysi user qaysi faylni ko'rganini tracking qilish mumkin:

```bash
# Nginx access log
tail -f /var/log/nginx/file.domain.lc-access.log

# Token ichidan user_id olish
php artisan tinker
>>> $token = "eyJpdiI6...";
>>> \Modules\FileManager\Services\TokenService::decryptToken($token);
```
