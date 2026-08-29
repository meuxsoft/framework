<?php

namespace App\Controllers;

use Core\Controller;
use Core\Libraries\Upload\Upload;
use Core\Libraries\Request\Request;
use Core\Libraries\Session\Session;
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
        return $this->layout('auth')->view('auth.login', [
            'title' => 'Giriş Yap - ' . config('app.name')
        ]);
    }

    /**
     * Dosya & Resim Yükleme Örnek Formu (GET)
     *
     * @return string
     */
    public function uploadDemo()
    {
        return $this->view('upload.index', [
            'title' => 'Dosya Yükleme & Boyutlandırma - ' . config('app.name')
        ]);
    }

    /**
     * Dosya & Resim Yükleme İşleyicisi (POST)
     *
     * @return void
     */
    public function handleUploadDemo()
    {
        $file = Request::files('image');
        $randomize = (bool)Request::post('randomize', false);
        $makeThumb = (bool)Request::post('thumb', false);
        $mode = Request::post('mode', 'fit');

        // Gelişmiş Upload Builder Kullanımı:
        $uploader = Upload::setPath('uploads/demo')
            ->randomize($randomize)
            ->setAllowedTypes(['jpg', 'jpeg', 'png', 'webp', 'gif'])
            ->setMaxSize(5 * 1024 * 1024); // 5 MB

        if ($mode !== 'none') {
            $uploader->resize(800, 600, $mode);
        }

        if ($makeThumb) {
            $uploader->thumb(true, 200, 200);
        }

        $result = $uploader->upload($file);

        if (!$result['success']) {
            $this->back($result['error']);
            return;
        }

        Session::flash('upload_result', $result);
        Session::flash('success', 'Dosya başarıyla yüklendi ve işlendi.');
        $this->redirect('/upload-demo');
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
