<?php
namespace App\Models;

class BoardModel extends BaseModel
{
    protected $table = 'dev_posts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'title', 'content', 'views', 'created_at'];

    /* =========================
       POST 관련
    ========================== */

    // ✅ 이 함수는 그대로 유지
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
        return $this->incrementWhere('dev_posts', ['id' => $postId], 'views', 1);
    }

    public function getPostWithAuthor(int $postId): ?array
    {
        return $this->selectOne(
            'dev_posts',
            'dev_posts.*, dev_users.name',
            ['dev_posts.id' => $postId],
            [
                ['table' => 'dev_users', 'on' => 'dev_users.id = dev_posts.user_id', 'type' => '']
            ]
        );
    }

    public function createPost(int $userId, string $title, string $content): int
    {
        $this->insert([
            'user_id' => $userId,
            'title'   => $title,
            'content' => $content
        ]);

        return (int) $this->getInsertID();
    }

    public function updatePost(int $postId, int $userId, string $title, string $content): bool
    {
        // 본인 글만 업데이트
        return $this->updateWhere(
            'dev_posts',
            ['id' => $postId, 'user_id' => $userId],
            ['title' => $title, 'content' => $content]
        );
    }

    public function deletePost(int $postId, int $userId): bool
    {
        // 본인 글만 삭제
        return $this->deleteWhere(
            'dev_posts',
            ['id' => $postId, 'user_id' => $userId]
        );
    }

    /* =========================
       COMMENT 관련
    ========================== */

    public function getComments(int $postId)
    {
        return $this->selectAll(
            'dev_comments',
            'dev_comments.*, dev_users.name',
            ['post_id' => $postId],
            ['dev_comments.id', 'ASC'],
            [
                ['table' => 'dev_users', 'on' => 'dev_users.id = dev_comments.user_id', 'type' => '']
            ]
        );
    }

    public function insertComment(int $postId, int $userId, string $content): int
    {
        $this->db->table('dev_comments')->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content
        ]);

        return (int)$this->db->insertID();
    }

    public function deleteComment(int $commentId, int $userId): bool
    {
        // 본인 댓글만 삭제
        return $this->deleteWhere(
            'dev_comments',
            ['id' => $commentId, 'user_id' => $userId]
        );
    }

    /* =========================
       LIKE 관련
    ========================== */

    public function toggleLike(int $postId, int $userId)
    {
        // 존재하면 삭제, 없으면 insert (원자성/중복 방지는 DB unique 추천)
        $existing = $this->selectOne(
            'dev_likes',
            'id',
            ['post_id' => $postId, 'user_id' => $userId]
        );

        if ($existing) {
            $this->deleteWhere('dev_likes', ['id' => $existing['id']]);
            return false; // 취소됨
        }

        $this->insertRow('dev_likes', [
            'post_id' => $postId,
            'user_id' => $userId
        ]);
        return true; // 좋아요 추가됨
    }

    public function getLikeCount(int $postId)
    {
        return $this->countWhere('dev_likes', ['post_id' => $postId]);
    }

    public function hasUserLiked(int $postId, int $userId): bool
    {
        return $this->existsWhere('dev_likes', ['post_id' => $postId, 'user_id' => $userId]);
    }
}