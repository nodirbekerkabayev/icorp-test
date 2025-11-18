# iCorp Test API - Laravel Implementation

Test API bilan ishlash vazifasi - Laravel web controller yechimi.

## Vazifa tavsifi

Test API bilan HTTP so'rovlar orqali ishlash va ma'lumotlarni qayta ishlash ko'nikmalarini ko'rsatish.

## Algoritm

1. **POST so'rov** - `https://test.icorp.uz/interview.php` ga JSON yuborish
2. **Webhook qabul qilish** - ikkinchi kod qismini olish
3. **Kodlarni birlashtirish** - ikkala qismni qo'shish
4. **GET so'rov** - birlashgan kod bilan final xabarni olish

## O'rnatish

### Talablar
- PHP >= 8.1
- Composer
- ngrok (webhook uchun)

### Qadamlar

```bash
# Repository ni clone qiling
git clone https://github.com/USERNAME/icorp-test.git
cd icorp-test

# Dependencies o'rnatish
composer install

# .env faylini yaratish
cp .env.example .env
php artisan key:generate

# Cache tozalash
php artisan config:clear
php artisan cache:clear
```

## Ishlatish

### 1. Laravel serverini ishga tushirish

```bash
php artisan serve
```

Server `http://127.0.0.1:8000` da ishga tushadi.

### 2. ngrok orqali webhook URL olish

Yangi terminal oynasida:

```bash
ngrok http 8000
```

Ngrok sizga webhook URL beradi, masalan: `https://abcd1234.ngrok.io`

### 3. Web interfeys orqali test qilish

1. Brauzerda `http://127.0.0.1:8000` ni oching
2. **Webhook URL** maydoniga ngrok URL ni kiriting:
   ```
   https://YOUR-NGROK-ID.ngrok.io/api-test/webhook
   ```
3. **Xabar** maydoniga o'z xabaringizni kiriting (ixtiyoriy)
4. "**Jarayonni Boshlash**" tugmasini bosing
5. Bir necha soniya kuting (webhook kelguncha)
6. "**Status Tekshirish**" tugmasini bosing
7. Ikkala kod qismi ham tayyor bo'lgandan keyin "**Final Xabarni Olish**" tugmasini bosing

## Fayl tuzilishi

```
icorp-test/
├── app/
│   └── Http/
│       ├── Controllers/
│       │   └── ApiTestController.php    # Asosiy controller
│       └── Middleware/
│           └── VerifyCsrfToken.php      # CSRF konfiguratsiyasi
├── routes/
│   └── web.php                          # Route'lar
├── resources/
│   └── views/
│       └── api-test.blade.php           # Web interface
└── README.md
```

## API Endpoints

| Method | URL | Tavsif |
|--------|-----|--------|
| GET | `/` | Asosiy web sahifa |
| POST | `/api-test/start` | POST so'rov yuborish va jarayonni boshlash |
| POST | `/api-test/webhook` | Webhook endpoint (API bu yerga kod yuboradi) |
| GET | `/api-test/status` | Kod qismlari statusini tekshirish |
| GET | `/api-test/final` | Final xabarni olish (GET so'rov) |

## Texnologiyalar

- **Laravel 11** - PHP framework
- **Tailwind CSS** - UI styling
- **ngrok** - Webhook tunnel
- **Laravel HTTP Client** - API so'rovlar uchun
- **Laravel Cache** - Ma'lumotlarni vaqtinchalik saqlash

## Muammolarni hal qilish

### Webhook kelgani yo'q
- ngrok ishlab turganligini tekshiring
- URL to'g'ri kiritilganligini tekshiring (https bilan)
- ngrok terminaldagi loglarni ko'ring

### 500 xatoligi
- `storage/logs/laravel.log` faylini tekshiring
- Cache tozalang: `php artisan cache:clear`
- Permission tekshiring: `chmod -R 775 storage bootstrap/cache`

## Muallif

Sizning ismingiz
GitHub: https://github.com/nodirbekerkabayev

