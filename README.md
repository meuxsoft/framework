# Meuxsoft Framework

PHP 7.3+ uyumlu, profesyonel, hafif, modüler ve **%100 static core** mimarisine sahip MVC Starter Kit.

---

## 📁 Proje Dizin Yapısı

```text
├── app/
│   ├── Config/
│   │   ├── app.php                # Uygulama ve modül ayarları
│   │   └── database.php           # Veritabanı bağlantı ayarları (MySQL / SQLite)
│   ├── Controllers/
│   │   └── HomeController.php     # Örnek Başlangıç Kontrolcüsü
│   ├── Models/                    # Modeller
│   ├── Views/
│   │   ├── errors/404.php         # 404 Hata Sayfası
│   │   ├── home/index.php         # Ana Sayfa Görünümü
│   │   └── layouts/main.php       # Ana Düzen (Layout)
│   └── Modules/                   # Modüller
├── core/
│   ├── Helpers/
│   │   ├── Helper.php             # Yardımcı Sınıf
│   │   └── helpers.php            # Global Fonksiyonlar (view, url, e, csrf_field vb.)
│   ├── Libraries/
│   │   ├── Database/              # PDO & QueryBuilder
│   │   ├── Layout/                # View / Layout Motoru
│   │   ├── Modules/               # Modül Yöneticisi
│   │   ├── Request/               # HTTP İstek & Girdi Yöneticisi
│   │   ├── Router/                # RESTful Rota Motoru
│   │   ├── Security/              # CSRF, XSS, Şifreleme
│   │   ├── Session/               # Oturum & Flash Yönetimi
│   │   └── Upload/                # Güvenli Dosya Yükleme
│   ├── Bootstrap.php              # Yaşam Döngüsü & Hata Yakalayıcı
│   └── Controller.php             # Çekirdek Temel Kontrolcü
├── database/
│   └── schema.sql                 # SQL Şeması
├── public/
│   ├── assets/                    # CSS, JS, Görseller
│   ├── uploads/                   # Yüklenen Dosyalar
│   ├── .htaccess                  # Clean URL
│   └── index.php                  # Giriş Noktası
├── routes/
│   └── web.php                    # Web Rotaları
├── storage/
│   └── logs/                      # Hata Günlükleri
├── .gitignore
├── .htaccess
├── composer.json
└── README.md
```

---

## 🚀 Hızlı Başlangıç

### 1. Klonlama
```bash
git clone https://github.com/meuxsoft/framework.git my-project
cd my-project
```

### 2. Web Sunucusu
Apache veya yerel geliştirme sunucunuzda DocumentRoot olarak `public/` dizinini gösterin veya projeyi doğrudan kök dizinden çalıştırın (kök `.htaccess` otomatik olarak `public/` dizinine yönlendirir).

---

## 🛠️ Temel Kütüphane Kullanımları

### 1. Database & QueryBuilder
```php
use Core\Libraries\Database\Database;

// Kayıtları listeleme
$users = Database::table('users')
    ->where('status', 1)
    ->orderBy('id', 'DESC')
    ->get();

// Ekleme (Insert)
$id = Database::table('users')->insert([
    'name'  => 'Ahmet',
    'email' => 'ahmet@example.com'
]);
```

### 2. Routing (Rotalar)
```php
use Core\Libraries\Router\Router;

Router::get('/', 'HomeController@index');
Router::get('/users/{id}', 'UserController@show');
Router::post('/users', 'UserController@store');
```

### 3. Controller & View
```php
namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home.index', [
            'title' => 'Ana Sayfa'
        ]);
    }
}
```

### 4. Global Helpers
- `view('view.name', $data)`: Görünümü render eder.
- `url('/path')`: Uygulama URL'si üretir.
- `asset('css/style.css')`: Statik asset URL'si üretir.
- `e($value)`: XSS korumalı HTML çıktısı üretir.
- `csrf_field()`: Güvenli CSRF gizli input alanını üretir.
- `config('app.name')`: Konfigürasyon değerini okur.
- `redirect('/login')`: Yönlendirme yapar.
- `back()`: Bir önceki sayfaya döner.

---

## 📄 Lisans
MIT License. &copy; 2026 Meux Soft
