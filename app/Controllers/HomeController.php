<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Varsayılan Layout (main.php) kullanan ana sayfa.
     *
     * @return string
     */
    public function index()
    {
        // 1. Varsayılan Layout kullanımı (Otomatik app/Views/layouts/main.php giydirilir):
        return $this->view('home.index', [
            'title' => 'Hoş Geldiniz - ' . config('app.name'),
            'users' => User::getActiveUsers()
        ]);
    }

    /**
     * Farklı bir Layout (auth.php) kullanım örneği.
     *
     * @return string
     */
    public function login()
    {
        // 2. Özel Layout kullanımı ($this->setLayout('auth') veya $this->view('auth.login', $data, 'auth')):
        return $this->setLayout('auth')->view('auth.login', [
            'title' => 'Giriş Yap - ' . config('app.name')
        ]);
    }

    /**
     * JSON API yanıtı (Layout kullanılmaz).
     *
     * @return void
     */
    public function status()
    {
        $this->json([
            'status'      => 'ok',
            'framework'   => 'Meuxsoft Framework',
            'php_version' => PHP_VERSION,
            'user_count'  => User::count(),
            'timestamp'   => time(),
        ]);
    }
}
