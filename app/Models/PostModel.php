<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'dev_posts';
    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'title', 'content', 'views', 'created_at'];
}