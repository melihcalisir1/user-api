# User API

Laravel tabanlı kullanıcı yönetimi API'si. Bu proje, kullanıcı oluşturma, listeleme, güncelleme ve silme işlemlerini gerçekleştiren RESTful API endpoint'leri sunar.

## Kurulum

1. Bağımlılıkları yükleyin:
```bash
composer install
```

2. Ortam değişkenlerini ayarlayın:
```bash
cp .env.example .env
php artisan key:generate
```

3. Veritabanını oluşturun ve migrate edin:
```bash
php artisan migrate
```

4. Sunucuyu başlatın:
```bash
php artisan serve
```

API endpoint'leri `http://localhost:8000/api` adresinde erişilebilir olacaktır.

---

## Teknolojiler

- Laravel 11
- PHP 8.2+
- MySQL (geliştirme ortamı)

## Proje Yapısı

Proje, Repository Pattern ve Service Layer kullanılarak geliştirilmiştir:

- **Controllers**: `app/Http/Controllers/UserController.php`
- **Services**: `app/Services/UserService.php`
- **Repositories**: `app/Repositories/UserRepository.php`
- **Models**: `app/Models/User.php`, `app/Models/Company.php`
- **Requests**: `app/Http/Requests/UserRequest.php`

---

## API Dokümantasyonu

Tüm API endpoint'leri `/api` prefix'i ile başlar.

### 1. Kullanıcı Oluşturma

Yeni bir kullanıcı oluşturur. Eğer şirket mevcut değilse otomatik olarak oluşturulur.

**Endpoint:** `POST /api/users/create`

**Request Body:**
```json
{
    "company_name": "Smartup Network",
    "name": "Ahmet",
    "surname": "Yılmaz",
    "email": "ahmet.yilmaz@example.com",
    "phone": "5551234567"
}
```

**Validation Kuralları:**
- `company_name`: Zorunlu, string, maksimum 100 karakter
- `name`: Zorunlu, string, maksimum 50 karakter, sadece harf ve boşluk (Türkçe karakterler dahil)
- `surname`: Zorunlu, string, maksimum 50 karakter, sadece harf ve boşluk (Türkçe karakterler dahil)
- `email`: Zorunlu, geçerli email formatı, benzersiz olmalı
- `phone`: Zorunlu, tam olarak 10 haneli rakam

**Başarılı Yanıt (201):**
```json
{
    "message": "Kullanıcı başarıyla eklendi.",
    "data": {
        "id": 1,
        "company_id": 1,
        "name": "Ahmet",
        "surname": "Yılmaz",
        "email": "ahmet.yilmaz@example.com",
        "phone": "5551234567",
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T12:00:00.000000Z"
    }
}
```

**Hata Yanıtları:**
- `422`: Validation hatası
- `500`: Sunucu hatası

---

### 2. Kullanıcı Listeleme

Tüm kullanıcıları listeler. Opsiyonel olarak şirket ID'sine göre filtreleme yapılabilir.

**Endpoint:** `GET /api/users/list`

**Query Parameters:**
- `company_id` (opsiyonel): Belirli bir şirkete ait kullanıcıları filtrelemek için

**Örnek İstekler:**
```
GET /api/users/list
GET /api/users/list?company_id=1
```

**Başarılı Yanıt (200):**
```json
[
    {
        "id": 1,
        "company_id": 1,
        "name": "Ahmet",
        "surname": "Yılmaz",
        "email": "ahmet.yilmaz@example.com",
        "phone": "5551234567",
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T12:00:00.000000Z",
        "company": {
            "id": 1,
            "name": "Acme Corporation",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
]
```

---

### 3. Kullanıcı Güncelleme

Mevcut bir kullanıcının bilgilerini günceller. Silinmiş kullanıcılar güncellenemez.

**Endpoint:** `PUT /api/users/update/{id}`

**Path Parameters:**
- `id`: Güncellenecek kullanıcının ID'si

**Request Body:**
```json
{
    "company_name": "Yeni Şirket Adı",
    "name": "Mehmet",
    "surname": "Demir",
    "email": "mehmet.demir@example.com",
    "phone": "5559876543"
}
```

**Validation Kuralları:**
- `company_name`: Zorunlu, string, maksimum 100 karakter
- `name`: Zorunlu, string, maksimum 50 karakter, sadece harf ve boşluk (Türkçe karakterler dahil)
- `surname`: Zorunlu, string, maksimum 50 karakter, sadece harf ve boşluk (Türkçe karakterler dahil)
- `email`: Zorunlu, geçerli email formatı, güncellenen kullanıcı hariç benzersiz olmalı
- `phone`: Zorunlu, tam olarak 10 haneli rakam

**Başarılı Yanıt (200):**
```json
{
    "message": "Kullanıcı başarıyla güncellendi.",
    "data": {
        "id": 1,
        "company_id": 2,
        "name": "Mehmet",
        "surname": "Demir",
        "email": "mehmet.demir@example.com",
        "phone": "5559876543",
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T13:00:00.000000Z"
    }
}
```

**Hata Yanıtları:**
- `404`: Kullanıcı bulunamadı
- `422`: Validation hatası veya silinmiş kullanıcı güncellenemez
- `500`: Sunucu hatası

---

### 4. Kullanıcı Silme

Bir kullanıcıyı soft delete ile siler. Silinmiş kullanıcılar tekrar silinemez.

**Endpoint:** `DELETE /api/users/delete/{id}`

**Path Parameters:**
- `id`: Silinecek kullanıcının ID'si

**Başarılı Yanıt (200):**
```json
{
    "message": "Kullanıcı başarıyla silindi.",
    "data": {
        "id": 1,
        "company_id": 1,
        "name": "Ahmet",
        "surname": "Yılmaz",
        "email": "ahmet.yilmaz@example.com",
        "phone": "5551234567",
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T12:00:00.000000Z",
        "deleted_at": "2024-01-01T14:00:00.000000Z"
    }
}
```

**Hata Yanıtları:**
- `404`: Kullanıcı bulunamadı
- `500`: Sunucu hatası veya kullanıcı zaten silinmiş


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
