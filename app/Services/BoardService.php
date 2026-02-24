<?php
namespace App\Services;

use App\Models\BoardModel;

class BoardService
{
    public function __construct(private BoardModel $model)
    {
    }

    public function getList(?string $q, int $perPage = 10)
    {
        $builder = $this->model->getPostsWithLikeCount($q);

        return [
            'posts' => $builder->paginate($perPage),
            'pager' => $this->model->pager,
            'totalCount' => $this->model->countAll()
        ];
    }

    public function getDetail(int $postId, int $userId)
    {
        $this->model->increaseViews($postId);

        $post = $this->model
            ->select('dev_posts.*, dev_users.name')
            ->join('dev_users', 'dev_users.id = dev_posts.user_id')
            ->where('dev_posts.id', $postId)
            ->first();

        if (!$post) return null;

        return [
            'post' => $post,
            'comments' => $this->model->getComments($postId),
            'likeCount' => $this->model->getLikeCount($postId),
            'liked' => $this->model->hasUserLiked($postId, $userId)
        ];
    }

    public function create(int $userId, string $title, string $content)
    {
        return $this->model->insert([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content
        ]);
    }

    public function update(int $postId, int $userId, string $title, string $content)
    {
        $post = $this->model->find($postId);
        if (!$post || $post['user_id'] != $userId) return false;

        return $this->model->update($postId, [
            'title' => $title,
            'content' => $content
        ]);
    }

    public function delete(int $postId, int $userId)
    {
        $post = $this->model->find($postId);
        if (!$post || $post['user_id'] != $userId) return false;

        return $this->model->delete($postId);
    }

    public function writeComment(int $postId, int $userId, string $content)
    {
        return $this->model->insertComment($postId, $userId, $content);
    }

    public function toggleLike(int $postId, int $userId)
    {
        return $this->model->toggleLike($postId, $userId);
    }
}