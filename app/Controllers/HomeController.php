<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    /**
     * Show application home welcome screen.
     *
     * @return string
     */
    public function index()
    {
        return $this->view('home.index', [
            'title' => 'Hoş Geldiniz - ' . config('app.name')
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
            'timestamp'   => time(),
        ]);
    }
}
