<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'dev_users';
    protected $primaryKey = 'id';

    protected $allowedFields = ['email', 'password', 'name'];
}
