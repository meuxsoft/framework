<?php

namespace App\Controllers;

use Core\Controller;
use Core\Libraries\Database\Database;
use Core\Libraries\Modules\Module;

class HomeController extends Controller
{
    /**
     * Show application home dashboard.
     *
     * @return string
     */
    public function index()
    {
        $stats = [
            'php_version' => PHP_VERSION,
            'modules'     => Module::all(),
            'db_driver'   => config('database.default'),
            'app_name'    => config('app.name'),
        ];

        return $this->view('home.index', [
            'title' => 'Ana Sayfa - ' . config('app.name'),
            'stats' => $stats
        ]);
    }

    /**
     * Show about page.
     *
     * @return string
     */
    public function about()
    {
        return $this->view('home.about', [
            'title' => 'Hakkında - ' . config('app.name')
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
            'status'    => 'ok',
            'framework' => 'PHP 7.3 Static MVC',
            'timestamp' => time(),
            'modules'   => array_keys(Module::all()),
        ]);
    }
}
