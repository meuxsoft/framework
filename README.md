# PHP 7.3 Static MVC Framework

Hafif, modüler, profesyonel, sürdürülebilir ve **tamamen PHP 7.3 uyumlu** özel MVC çekirdeği.

---

## 🌟 Temel Özellikler ve Kurallar

- **PHP 7.3 %100 Uyumlu:** PHP 7.4+ veya PHP 8+ özellikleri (typed properties, arrow functions, constructor promotion, match, enum) kullanılmamıştır.
- **Static Core Kütüphaneleri:** Tüm Core kütüphaneleri (`Database`, `Session`, `Router`, `Upload`, `Layout`, `Module`, `Request`, `Security`) `new` kullanılmadan, doğrudan static olarak çalışır (`Database::table()`, `Session::set()` vb.).
- **APP vs CORE Ayrımı:** `core/` taşınabilir evrensel çekirdektir; `app/` ise projeye özgü kodları (Config, Controllers, Models, Views, Modules) barındırır.
- **Modüler Yapı (`app/Modules/`):** Her modül kendi Controller, Model, View ve `routes.php` dosyalarını izole şekilde yönetir.
- **Dahili Güvenlik:** CSRF koruması (`csrf_field()`), XSS filtreleme (`e()`), PDO prepared statements, güvenli session parametreleri, dosya MIME ve uzantı doğrulaması.
- **Sıfır Bağımlılıkla Çalışabilme:** Hem Composer PSR-4 desteğine sahiptir hem de `vendor/` olmadan doğrudan çalışabilen yerleşik PSR-4 fallback autoloader içerir.

---

## 📁 Proje Dizin Ağacı

```text
project/
├── app/
│   ├── Config/
│   │   ├── app.php
│   │   └── database.php
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   └── ProductController.php
│   ├── Models/
│   │   └── Product.php
│   ├── Views/
│   │   ├── errors/
│   │   │   └── 404.php
│   │   ├── home/
│   │   │   ├── about.php
│   │   │   └── index.php
│   │   └── layouts/
│   │       └── main.php
│   └── Modules/
│       └── Product/
│           ├── Controllers/
│           │   └── ProductController.php
│           ├── Models/
│           │   └── Product.php
│           ├── Views/
│           │   ├── create.php
│           │   ├── edit.php
│           │   └── index.php
│           ├── Module.php
│           └── routes.php
├── core/
│   ├── Helpers/
│   │   ├── Helper.php
│   │   └── helpers.php
│   ├── Libraries/
│   │   ├── Database/
│   │   │   ├── Database.php
│   │   │   └── QueryBuilder.php
│   │   ├── Layout/
│   │   │   └── Layout.php
│   │   ├── Modules/
│   │   │   └── Module.php
│   │   ├── Request/
│   │   │   └── Request.php
│   │   ├── Router/
│   │   │   └── Router.php
│   │   ├── Security/
│   │   │   └── Security.php
│   │   ├── Session/
│   │   │   └── Session.php
│   │   └── Upload/
│   │       └── Upload.php
│   ├── Bootstrap.php
│   └── Controller.php
├── database/
│   └── schema.sql
├── public/
│   ├── assets/
│   │   ├── css/style.css
│   │   └── js/app.js
│   ├── uploads/
│   │   └── products/
│   ├── .htaccess
│   └── index.php
├── routes/
│   └── web.php
├── storage/
│   ├── logs/
│   └── database.sqlite
├── .htaccess
├── composer.json
└── README.md
```

---

## 🚀 Hızlı Başlangıç

### 1. Web Sunucusu Yapılandırması
Apache üzerinde VirtualHost DocumentRoot olarak `public/` dizinini belirleyin veya doğrudan root dizini sunun (kök `.htaccess` istekleri otomatik olarak `public/` dizinine yönlendirir).

### 2. Veritabanı Ayarları
`app/Config/database.php` dosyasını açın:
- **SQLite (Varsayılan):** Hiçbir ayar yapmadan anında çalışır (`storage/database.sqlite` otomatik oluşturulur).
- **MySQL:** `'default' => 'mysql'` yapıp bağlantı bilgilerinizi girin.

### 3. Composer (İsteğe Bağlı)
```bash
composer dump-autoload
```

---

## 🛠️ Core Kütüphane Kullanımları

### 1. Database & QueryBuilder
```php
use Core\Libraries\Database\Database;

// Kayıtları listeleme
$users = Database::table('users')
    ->where('status', 1)
    ->whereIn('role_id', [2, 3])
    ->orderBy('id', 'DESC')
    ->limit(20)
    ->get();

// Tekil kayıt bulma
$user = Database::table('users')->find(5);

// Insert
$id = Database::table('users')->insert([
    'name'  => 'Ahmet',
    'email' => 'ahmet@example.com'
]);

// Update
Database::table('users')->where('id', 5)->update([
    'name' => 'Ahmet Yılmaz'
]);

// Delete
Database::table('users')->where('id', 5)->delete();

// Transaction
Database::beginTransaction();
try {
    // işlemler...
    Database::commit();
} catch (\Exception $e) {
    Database::rollback();
}
```

### 2. Session & Flash Messages
```php
use Core\Libraries\Session\Session;

Session::set('user_id', 10);
$userId = Session::get('user_id');
Session::flash('success', 'İşlem başarıyla tamamlandı!');
```

### 3. Router
```php
use Core\Libraries\Router\Router;

Router::get('/', 'HomeController@index');
Router::get('/products/{id}', 'ProductController@show');
Router::post('/products', 'ProductController@store');
Router::put('/products/{id}', 'ProductController@update');
Router::delete('/products/{id}', 'ProductController@delete');
```

### 4. Upload
```php
use Core\Libraries\Upload\Upload;

$result = Upload::setPath('uploads/products')
    ->setAllowedTypes(['jpg', 'jpeg', 'png', 'webp'])
    ->setMaxSize(5242880) // 5 MB
    ->upload($_FILES['image']);

if ($result['success']) {
    $imagePath = $result['path'];
}
```

### 5. Layout & View
```php
use Core\Libraries\Layout\Layout;

// Layout ile render etme
Layout::render('main', 'home.index', ['title' => 'Ana Sayfa']);

// Controller / Helper üzerinden
return view('products.index', $data);
```

### 6. Global Helpers
- `view($view, $data)`: View render eder.
- `url($path)`: Uygulama URL'si üretir.
- `asset($path)`: Statik asset URL'si üretir.
- `e($str)`: XSS korumalı HTML çıktısı üretir.
- `old($key)`: Form hata sonrası eski girdiyi getirir.
- `csrf_field()`: Gizli CSRF inputu oluşturur.
- `csrf_token()`: Aktif CSRF token değerini döner.
- `config('app.name')`: Config değerini okur.
- `redirect('/products')`: Yönlendirme yapar.
- `back()`: Önceki sayfaya döner.
