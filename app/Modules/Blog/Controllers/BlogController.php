<?php

namespace App\Modules\Blog\Controllers;

use Core\Controller;
use App\Modules\Blog\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        // Doğrudan 'index' yazmanız yeterlidir (Blog/Views/index.php otomatik bulunur):
        return $this->view('index', [
            'title' => 'Blog Modülü - ' . config('app.name'),
            'posts' => Post::getSamplePosts()
        ]);
    }

    public function show($id)
    {
        $posts = Post::getSamplePosts();
        $post = null;

        foreach ($posts as $p) {
            if ($p['id'] == $id) {
                $post = $p;
                break;
            }
        }

        if (!$post) {
            return redirect('/blog');
        }

        // Doğrudan 'show' yazmanız yeterlidir:
        return $this->view('show', [
            'title' => $post['title'] . ' - ' . config('app.name'),
            'post'  => $post
        ]);
    }
}
