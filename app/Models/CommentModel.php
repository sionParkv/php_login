<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'dev_comments';
    protected $allowedFields = ['post_id', 'user_id', 'content'];
}
