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

    public function getEditData(int $postId, int $userId): array
    {
        $post = $this->model->find($postId);

        if (!$post) {
            return ['ok' => false, 'message' => '게시글이 없습니다.'];
        }

        if ((int)$post['user_id'] !== (int)$userId) {
            return ['ok' => false, 'message' => '권한 없음'];
        }

        return ['ok' => true, 'post' => $post];
    }

    public function toggleLike(int $postId, int $userId): array
    {
        $liked = $this->model->toggleLike($postId, $userId);
        $likeCount = $this->model->getLikeCount($postId);

        return [
            'status' => 'success',
            'liked' => $liked,
            'likeCount' => $likeCount
        ];
    }

    public function writeComment(int $postId, int $userId, string $userName, string $content): array
    {
        $content = trim($content);

        if ($content === '') {
            return ['status' => 'error', 'message' => '댓글을 입력하세요'];
        }

        $this->model->insertComment($postId, $userId, $content);

        // append용 HTML 생성(서비스에서 처리)
        $html = '
      <div class="py-2" style="border-bottom:1px solid #eee;">
        <div class="d-flex justify-content-between">
          <div style="font-weight:700; color:#192A3E;">'.esc($userName).'</div>
          <div class="muted" style="font-size:12px;">방금</div>
        </div>
        <div style="white-space:pre-wrap; color:#192A3E; margin-top:4px;">'.esc($content).'</div>
      </div>
    ';

        return [
            'status' => 'success',
            'html' => $html
        ];
    }
}