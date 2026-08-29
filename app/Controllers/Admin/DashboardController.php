<?php

namespace App\Controllers\Admin;

use Core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->view('home.index', [
            'title' => 'Admin Paneli - ' . config('app.name'),
            'users' => []
        ]);
    }
}
