# PHP 7.3 Static MVC Framework — Geliştirme Promptu

PHP 7.3 ile tamamen uyumlu, profesyonel fakat sade, hızlı, modüler ve sürdürülebilir bir **kendi MVC PHP altyapısı** oluştur.

Laravel, Symfony, CodeIgniter veya başka bir PHP framework kullanma.

Amaç; tekrar tekrar farklı projelerde kullanılabilecek, **hafif bir PHP MVC çekirdeği** oluşturmaktır.

## 1. TEMEL KURALLAR

- PHP sürümü kesinlikle **7.3** olacak.
- PHP 7.4+ veya PHP 8+ özellikleri kesinlikle kullanılmayacak.
- Typed properties kullanılmayacak.
- Arrow functions kullanılmayacak.
- Constructor property promotion kullanılmayacak.
- Enum kullanılmayacak.
- PHP 7.3'te bulunmayan hiçbir syntax/API kullanılmayacak.
- Tüm class'lar namespace kullanacak.
- Class'lar `use` ile dahil edilecek.
- Core Library sınıflarının tamamı **static** olacak.
- Core Library'ler kesinlikle `new` ile instantiate edilmeyecek.
- Library kullanımı `ClassName::method()` şeklinde olacak.
- Composer yalnızca gerekli autoload/dependency işlemleri için kullanılabilir.
- PSR-4 autoload kullanılacak.
- Harici MVC framework kullanılmayacak.
- Gereksiz abstraction, container, facade veya karmaşık design pattern oluşturulmayacak.
- Sistem basit, okunabilir, hızlı ve geliştirilebilir olacak.

---

# 2. MİMARİ

En önemli kural:

**`core` framework çekirdeğidir. `app` proje/uygulama tarafıdır.**

Core hiçbir şekilde belirli bir projeye bağımlı olmamalıdır.

Proje ayarları, config dosyaları ve uygulamaya özel tüm içerikler `app` içerisinde bulunmalıdır.

Önerilen yapı:

```text
project/
│
├── app/
│   ├── Config/
│   │   ├── app.php
│   │   └── database.php
│   │
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   │   └── layouts/
│   │
│   └── Modules/
│       ├── Auth/
│       ├── Admin/
│       └── Product/
│
├── core/
│   ├── Libraries/
│   │   ├── Database/
│   │   │   ├── Database.php
│   │   │   └── QueryBuilder.php
│   │   │
│   │   ├── Upload/
│   │   │   └── Upload.php
│   │   │
│   │   ├── Session/
│   │   │   └── Session.php
│   │   │
│   │   ├── Router/
│   │   │   └── Router.php
│   │   │
│   │   ├── Layout/
│   │   │   └── Layout.php
│   │   │
│   │   └── Modules/
│   │       └── Module.php
│   │
│   ├── Helpers/
│   │   ├── helpers.php
│   │   └── Helper.php
│   │
│   ├── Bootstrap.php
│   └── Controller.php
│
├── routes/
│   └── web.php
│
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   │
│   └── uploads/
│
├── storage/
│   └── logs/
│
├── composer.json
├── .htaccess
└── README.md
```

Klasör yapısını gereksiz yere büyütme.

---

# 3. NAMESPACE

PSR-4 namespace yapısı:

```text
App\
Core\
```

Örnek:

```php
namespace Core\Libraries\Database;
```

```php
namespace Core\Libraries\Session;
```

```php
namespace Core\Libraries\Router;
```

```php
namespace Core\Libraries\Upload;
```

```php
namespace Core\Libraries\Layout;
```

```php
namespace Core\Libraries\Modules;
```

Uygulama:

```php
namespace App\Controllers;
```

```php
namespace App\Models;
```

---

# 4. STATIC LIBRARY KURALI

Bu proje için kritik kural:

**Core Libraries'ın tamamı static olacaktır.**

Örneğin:

```php
use Core\Libraries\Database\Database;
use Core\Libraries\Session\Session;
use Core\Libraries\Router\Router;
use Core\Libraries\Upload\Upload;
use Core\Libraries\Layout\Layout;
use Core\Libraries\Modules\Module;
```

Kullanım:

```php
Database::table('users')
    ->where('status', 1)
    ->get();
```

```php
Session::set('user_id', 15);
```

```php
Router::get('/', 'HomeController@index');
```

```php
Upload::setPath('uploads/products')
    ->upload($_FILES['image']);
```

```php
Layout::render('admin', 'users.index', $data);
```

```php
Module::register('Product');
```

Kesinlikle:

```php
$db = new Database();
$session = new Session();
$router = new Router();
$upload = new Upload();
```

şeklinde kullanım yapılmayacak.

**`new` ile Library oluşturmak yasaktır.**

---

# 5. DATABASE

PDO tabanlı static Database Library oluştur.

Namespace:

```php
Core\Libraries\Database
```

Ana kullanım:

```php
Database::table('users')
```

Örnek:

```php
$users = Database::table('users')
    ->where('status', 1)
    ->orderBy('id', 'DESC')
    ->limit(20)
    ->get();
```

Desteklenmesi gereken Query Builder işlemleri:

```text
select
where
orWhere
whereIn
whereNotIn
whereNull
whereNotNull
like
orderBy
groupBy
having
limit
offset
join
leftJoin
rightJoin
count
sum
avg
min
max
first
find
exists
insert
update
delete
```

Örnek:

```php
$user = Database::table('users')
    ->where('id', $id)
    ->first();
```

Insert:

```php
$id = Database::table('users')->insert([
    'name' => 'Ercan',
    'email' => 'test@example.com'
]);
```

Update:

```php
Database::table('users')
    ->where('id', $id)
    ->update([
        'name' => 'Yeni İsim'
    ]);
```

Delete:

```php
Database::table('users')
    ->where('id', $id)
    ->delete();
```

## Güvenlik

- PDO kullanılmalı.
- Prepared statements kullanılmalı.
- Kullanıcı girdileri doğrudan SQL'e eklenmemeli.
- SQL Injection'a karşı güvenli olmalı.
- Transaction desteklenmeli.

```php
Database::beginTransaction();

Database::commit();

Database::rollback();
```

Database bağlantı bilgileri:

```text
app/Config/database.php
```

içerisinden alınmalı.

---

# 6. CONFIG

Config dosyalarının tamamı `app/Config` altında olacak.

Örnek:

```php
// app/Config/app.php

return [
    'name' => 'My Application',
    'url' => 'http://localhost',
    'timezone' => 'Europe/Istanbul',
    'debug' => true
];
```

Database:

```php
// app/Config/database.php

return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'database',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

Kullanım:

```php
config('app.name');
config('app.url');
config('database.host');
```

**Core içerisinde projeye özel config tutulmayacak.**

---

# 7. SESSION

Static Session Library oluştur.

Namespace:

```php
Core\Libraries\Session
```

Kullanım:

```php
Session::start();

Session::set('user_id', 15);

Session::get('user_id');

Session::has('user_id');

Session::remove('user_id');

Session::flash('success', 'İşlem başarılı.');

Session::getFlash('success');

Session::destroy();
```

Session güvenliği dikkate alınmalı.

---

# 8. UPLOAD

Static Upload Library oluştur.

Namespace:

```php
Core\Libraries\Upload
```

Örnek:

```php
$result = Upload::setPath('uploads/products')
    ->setAllowedTypes([
        'jpg',
        'jpeg',
        'png',
        'webp'
    ])
    ->setMaxSize(5242880)
    ->upload($_FILES['image']);
```

Destek:

- MIME kontrolü
- Extension kontrolü
- Maksimum dosya boyutu
- Random filename
- Güvenli filename
- PHP upload error kontrolü
- Çoklu upload
- Upload sonucu
- Hata mesajları

Kullanıcı tarafından gönderilen dosya adına güvenme.

---

# 9. ROUTER

Static Router Library oluştur.

Namespace:

```php
Core\Libraries\Router
```

Kullanım:

```php
Router::get('/', 'HomeController@index');

Router::get('/products', 'ProductController@index');

Router::get('/products/{id}', 'ProductController@show');

Router::post('/products', 'ProductController@store');

Router::put('/products/{id}', 'ProductController@update');

Router::delete('/products/{id}', 'ProductController@delete');
```

Destek:

- GET
- POST
- PUT
- DELETE
- Route parameters
- 404
- 405
- HTTP method kontrolü

Örneğin:

```text
/products/15
```

isteği:

```php
ProductController@show
```

metoduna:

```text
15
```

parametresi ile aktarılmalı.

Controller namespace çözümlemesi düzgün yapılmalı.

---

# 10. LAYOUT / VIEW

Static Layout Library oluştur.

Namespace:

```php
Core\Libraries\Layout
```

Kullanım:

```php
Layout::render('main', 'home.index', $data);
```

Helper üzerinden:

```php
return view('home.index', $data);
```

View yapısı:

```text
app/
└── Views/
    ├── layouts/
    │   ├── main.php
    │   └── admin.php
    │
    ├── home/
    │   └── index.php
    │
    └── products/
        ├── index.php
        ├── create.php
        └── edit.php
```

Layout içinde:

```php
<?= $content ?>
```

mantığı kullanılabilir.

Herhangi bir template engine oluşturma.

Normal PHP View kullan.

---

# 11. MODULE SYSTEM

Module sistemi:

```text
app/Modules/
```

altında olacak.

Örnek:

```text
app/
└── Modules/
    ├── Auth/
    │   ├── Controllers/
    │   ├── Models/
    │   ├── Views/
    │   ├── routes.php
    │   └── Module.php
    │
    ├── Admin/
    │   ├── Controllers/
    │   ├── Models/
    │   ├── Views/
    │   ├── routes.php
    │   └── Module.php
    │
    └── Product/
        ├── Controllers/
        ├── Models/
        ├── Views/
        ├── routes.php
        └── Module.php
```

Module sistemi Core içerisinde:

```text
Core\Libraries\Modules\Module
```

tarafından yönetilecek.

Kullanım:

```php
use Core\Libraries\Modules\Module;

Module::register('Auth');
Module::register('Admin');
Module::register('Product');
```

Her Module:

- Controller
- Model
- View
- Route
- Module.php

barındırabilir.

Her Module kendi `routes.php` dosyasını sisteme dahil edebilmeli.

Module aktif/pasif sistemi desteklenmeli.

---

# 12. MVC

Controller:

```php
namespace App\Controllers;

use Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('products.index', [
            'products' => $products
        ]);
    }
}
```

Model:

```php
namespace App\Models;

use Core\Libraries\Database\Database;

class Product
{
    protected static $table = 'products';

    public static function all()
    {
        return Database::table(self::$table)->get();
    }
}
```

View:

```php
<?php foreach ($products as $product): ?>

    <h2><?= e($product['name']) ?></h2>

<?php endforeach; ?>
```

Kurallar:

- Controller içerisinde HTML yazılmayacak.
- View içerisinde database sorgusu yapılmayacak.
- Model içerisinde HTML bulunmayacak.
- MVC katmanları birbirine karıştırılmayacak.

---

# 13. BASE CONTROLLER

`core/Controller.php` oluştur.

Namespace:

```php
namespace Core;
```

Controller:

```php
namespace App\Controllers;

use Core\Controller;

class ProductController extends Controller
{
}
```

şeklinde çalışmalı.

---

# 14. HELPERS

Gerekli helper fonksiyonlarını oluştur:

```text
url()
asset()
redirect()
view()
request()
e()
old()
csrf_token()
csrf_field()
config()
```

Örnek:

```php
url('products/15');
```

```php
asset('css/app.css');
```

```php
redirect('/login');
```

```php
return view('products.index', $data);
```

```php
e($value);
```

`e()` XSS korumalı olmalı.

Helper fonksiyonları kullanılacaksa global fonksiyon çakışmalarına dikkat et.

---

# 15. REQUEST

Gerekli görülüyorsa static Request Library oluştur.

Örnek:

```php
Request::get('id');

Request::post('email');

Request::input('name');

Request::method();

Request::isPost();

Request::isGet();
```

Request değerleri güvenli şekilde alınmalı.

---

# 16. SECURITY

Temel güvenlik sistemlerini dahil et:

- CSRF Protection
- XSS Protection
- SQL Injection Protection
- Secure Session
- Password Hashing
- Input Validation
- File Upload Security

Şifre işlemleri:

```php
password_hash();
password_verify();
```

ile yapılmalı.

---

# 17. ERROR HANDLING

Development / Production ayrımı oluştur.

Development:

```text
Detaylı hata
Stack trace
Geliştirici bilgisi
```

Production:

```text
Kullanıcıya güvenli hata
Detaylar storage/logs altına
```

HTTP hata kodları:

```text
404
403
405
419
500
```

desteklenmeli.

---

# 18. BOOTSTRAP

`core/Bootstrap.php` sistemin başlangıç noktası olacak.

Sırasıyla:

1. Composer autoload
2. Helper yükleme
3. Config yükleme
4. Session başlatma
5. Database hazırlama
6. Module sistemi
7. Routes yükleme
8. Router dispatch

işlemlerini gerçekleştirmeli.

Bootstrap mümkün olduğunca sade tutulmalı.

---

# 19. PUBLIC INDEX

`public/index.php` tek giriş noktası olacak.

Örneğin:

```php
require_once '../vendor/autoload.php';
require_once '../core/Bootstrap.php';

\Core\Bootstrap::run();
```

URL üzerinden doğrudan Controller/Model dosyalarına erişim engellenmeli.

---

# 20. .HTACCESS

Apache için clean URL oluştur.

Örnek:

```text
/
 /products
 /products/15
 /login
 /admin
```

çalışmalı.

URL'de:

```text
index.php
```

görünmemeli.

Tüm istekler Router'a aktarılmalı.

---

# 21. COMPOSER

PSR-4:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Core\\": "core/"
        }
    }
}
```

kullan.

Composer'dan sonra:

```bash
composer dump-autoload
```

çalıştırılabilecek şekilde yapı oluştur.

---

# 22. ÖRNEK PRODUCT MODULE

Sistemin gerçekten çalıştığını göstermek için tam çalışan Product Module oluştur.

```text
app/Modules/Product/
├── Controllers/
│   └── ProductController.php
├── Models/
│   └── Product.php
├── Views/
│   ├── index.php
│   ├── create.php
│   └── edit.php
├── routes.php
└── Module.php
```

CRUD:

```text
GET    /products
GET    /products/create
POST   /products
GET    /products/{id}/edit
POST   /products/{id}/update
POST   /products/{id}/delete
```

işlemleri çalışmalı.

Database SQL tablosunu da oluştur.

---

# 23. KOD KALİTESİ

Kod:

- PHP 7.3 uyumlu
- Namespace kullanan
- `use` kullanan
- Static Library mimarisine uygun
- Modüler
- Güvenli
- Okunabilir
- Sade
- Yeniden kullanılabilir
- Hızlı

olmalı.

Gereksiz abstraction oluşturma.

Laravel benzeri devasa bir framework oluşturma.

Container, service provider, repository, facade gibi katmanları ihtiyaç yoksa ekleme.

Ama sistem ileride yeni Library ve Module eklenebilecek kadar esnek olmalı.

---

# 24. EN ÖNEMLİ MİMARİ KURALI

Bu kurala kesinlikle uy:

```text
APP = PROJE
CORE = SİSTEM
```

### APP

Projenin değişken tarafıdır:

```text
Config
Controllers
Models
Views
Modules
```

### CORE

Her projede tekrar kullanılabilecek sistem tarafıdır:

```text
Libraries
Helpers
Bootstrap
Base Controller
```

Core, belirli bir projenin config veya business logic'ine bağımlı olmayacak.

---

# 25. SON KONTROL

Kod üretildikten sonra sistemi baştan sona kontrol et.

Özellikle:

- PHP 7.3 syntax
- Namespace
- `use`
- Static class kullanımı
- `new` ile Library oluşturulup oluşturulmadığı
- PSR-4
- Composer
- Database
- Query Builder
- Router
- Session
- Upload
- Layout
- Module
- MVC
- Config
- Helpers
- Security
- Error Handling
- Bootstrap
- `.htaccess`
- Dosya yolları

kontrol edilmeli.

**Tanımsız class, yanlış namespace, yanlış `use`, eksik method, eksik dependency, eksik dosya veya PHP 7.3'te çalışmayacak herhangi bir kod bırakma.**

Her dosyanın tam ve çalışabilir kodunu üret.

Kodları sadece teorik olarak anlatma; gerçekten çalışabilecek bir proje oluştur.

Son olarak tüm class, method, namespace ve dependency ilişkilerini tekrar kontrol ederek sistemin **PHP 7.3 üzerinde syntax ve mimari olarak hatasız** olduğundan emin ol.

Ana hedef:

**BASİT + PROFESYONEL + STATIC + NAMESPACE + MODÜLER + MVC + PHP 7.3**
