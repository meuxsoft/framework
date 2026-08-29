<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use Throwable;

class HomeController extends Controller
{
    /**
     * Show application home welcome screen with sample Model data.
     *
     * @return string
     */
    public function index()
    {
        $users = [];
        $dbConnected = true;

        try {
            $users = User::getActiveUsers();
        } catch (Throwable $e) {
            $dbConnected = false;
        }

        return $this->view('home.index', [
            'title'       => 'Hoş Geldiniz - ' . config('app.name'),
            'users'       => $users,
            'dbConnected' => $dbConnected
        ]);
    }

    /**
     * Return JSON health status API.
     *
     * @return void
     */
    public function status()
    {
        $userCount = 0;
        try {
            $userCount = User::count();
        } catch (Throwable $e) {
            $userCount = null;
        }

        $this->json([
            'status'       => 'ok',
            'framework'    => 'Meuxsoft Framework',
            'php_version'  => PHP_VERSION,
            'user_count'   => $userCount,
            'timestamp'    => time(),
        ]);
    }
}
