<?php
namespace App\Models;

use CodeIgniter\Model;

class BoardModel extends Model
{
    protected $table = 'dev_posts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'title', 'content', 'views', 'created_at'];

    /* =========================
       POST 관련
    ========================== */

    public function getPostsWithLikeCount(?string $q = null)
    {
        $likeSub = "(SELECT post_id, COUNT(*) AS like_count FROM dev_likes GROUP BY post_id) likes";

        $builder = $this
            ->select("dev_posts.*, dev_users.name, IFNULL(likes.like_count,0) AS like_count")
            ->join('dev_users', 'dev_users.id = dev_posts.user_id')
            ->join($likeSub, 'likes.post_id = dev_posts.id', 'left')
            ->orderBy('dev_posts.id', 'DESC');

        if ($q) {
            $builder->groupStart()
                ->like('dev_posts.title', $q)
                ->orLike('dev_posts.content', $q)
                ->orLike('dev_users.name', $q)
                ->groupEnd();
        }

        return $builder;
    }

    public function increaseViews(int $postId)
    {
        return $this->set('views', 'views+1', false)
            ->where('id', $postId)
            ->update();
    }

    /* =========================
       COMMENT 관련
    ========================== */

    public function getComments(int $postId)
    {
        return $this->db->table('dev_comments')
            ->select('dev_comments.*, dev_users.name')
            ->join('dev_users', 'dev_users.id = dev_comments.user_id')
            ->where('post_id', $postId)
            ->orderBy('dev_comments.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function insertComment(int $postId, int $userId, string $content)
    {
        return $this->db->table('dev_comments')->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content
        ]);
    }

    /* =========================
       LIKE 관련
    ========================== */

    public function toggleLike(int $postId, int $userId)
    {
        $table = $this->db->table('dev_likes');

        $exists = $table
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($exists) {
            $table->where('id', $exists['id'])->delete();
            return false; // 취소됨
        } else {
            $table->insert([
                'post_id' => $postId,
                'user_id' => $userId
            ]);
            return true; // 좋아요 추가됨
        }
    }

    public function getLikeCount(int $postId)
    {
        return $this->db->table('dev_likes')
            ->where('post_id', $postId)
            ->countAllResults();
    }

    public function hasUserLiked(int $postId, int $userId): bool
    {
        return (bool)$this->db->table('dev_likes')
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->get()
            ->getRow();
    }
}