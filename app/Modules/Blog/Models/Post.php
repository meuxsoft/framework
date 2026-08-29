<?php

namespace App\Modules\Blog\Models;

use Core\Model;

class Post extends Model
{
    protected static $table = 'posts';
    protected static $primaryKey = 'id';

    public static function getSamplePosts()
    {
        return [
            [
                'id'      => 1,
                'title'   => 'Meuxsoft Framework Modüler Mimari Rehberi',
                'summary' => 'Modüller sayesinde projelerinizi bağımsız paketler (Blog, E-Ticaret, Admin, API) halinde geliştirebilir ve yönetebilirsiniz.',
                'content' => 'Modüler mimari, büyük projelerin yönetilebilir parçalara bölünmesini sağlar. Her modül kendi Controller, Model, View ve routes.php dosyalarını barındırır. app/Config/app.php içindeki modules dizisine modül adını eklemeniz yeterlidir.',
                'author'  => 'Ercan Ulucan',
                'date'    => date('Y-m-d'),
                'tag'     => 'Mimari'
            ],
            [
                'id'      => 2,
                'title'   => 'Hafif, Güvenli ve Statik Çekirdekli PHP MVC',
                'summary' => 'Sıfır bağımlılık, dahili autoloader ve %100 saf dinamik PHP ile milisaniyeler seviyesinde çalışma hızı.',
                'content' => 'Meuxsoft Framework, geliştiriciye gereksiz karmaşıklık çıkarmayan, statik çekirdek kütüphanelerle donatılmış modern bir MVC çatısıdır. Controller ve Model yapıları en sade haliyle kullanıma hazırdır.',
                'author'  => 'Meux Soft',
                'date'    => date('Y-m-d'),
                'tag'     => 'Performans'
            ]
        ];
    }
}
