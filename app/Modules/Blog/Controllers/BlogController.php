<?php

namespace App\Modules\Blog\Controllers;

use Core\Controller;
use App\Modules\Blog\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        return $this->view('Blog::index', [
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

        return $this->view('Blog::show', [
            'title' => $post['title'] . ' - ' . config('app.name'),
            'post'  => $post
        ]);
    }
}
