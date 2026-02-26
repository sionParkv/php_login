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

        // ✅ DB 조회는 모델로
        $post = $this->model->getPostWithAuthor($postId);
        if (!$post) return null;

        return [
            'post' => $post,
            'comments' => $this->model->getComments($postId),
            'likeCount' => $this->model->getLikeCount($postId),
            'liked' => $this->model->hasUserLiked($postId, $userId),
        ];
    }

    public function create(int $userId, string $title, string $content)
    {
        $title = trim($title);
        if ($title === '') return false;

        return $this->model->createPost($userId, $title, (string)$content);
    }

    public function update(int $postId, int $userId, string $title, string $content)
    {
        $title = trim($title);
        if ($title === '') return false;

        return $this->model->updatePost($postId, $userId, $title, (string)$content);
    }

    public function delete(int $postId, int $userId)
    {
        return $this->model->deletePost($postId, $userId);
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

    public function deleteComment(int $commentId, int $userId): array
    {
        if ($commentId <= 0) {
            return ['status' => 'error', 'message' => '잘못된 요청'];
        }

        $ok = $this->model->deleteComment($commentId, $userId);

        if (!$ok) {
            return ['status' => 'error', 'message' => '삭제 권한이 없거나 댓글이 없습니다'];
        }

        return ['status' => 'success', 'commentId' => $commentId];
    }
}