<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Show application home welcome screen with sample Model data.
     *
     * @return string
     */
    public function index()
    {
        return $this->view('home.index', [
            'title' => 'Hoş Geldiniz - ' . config('app.name'),
            'users' => User::getActiveUsers()
        ]);
    }

    /**
     * Return JSON health status API.
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
