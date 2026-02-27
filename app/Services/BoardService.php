<?php
namespace App\Services;

use App\Models\BoardModel;

class BoardService
{
    public function __construct(private BoardModel $boardModel)
    {
    }

    public function getList(?string $q, int $perPage = 10)
    {
        $builder = $this->boardModel->getPostsWithLikeCount($q);

        return [
            'posts' => $builder->paginate($perPage),
            'pager' => $this->boardModel->pager,
            'totalCount' => $this->boardModel->countAll()
        ];
    }

    public function getDetail(int $postId, int $userId)
    {
        $this->boardModel->increaseViews($postId);

        // ✅ DB 조회는 모델로
        $post = $this->boardModel->getPostWithAuthor($postId);
        if (!$post) return null;

        return [
            'post' => $post,
            'comments' => $this->boardModel->getComments($postId),
            'likeCount' => $this->boardModel->getLikeCount($postId),
            'liked' => $this->boardModel->hasUserLiked($postId, $userId),
        ];
    }

    public function create(int $userId, string $title, string $content)
    {
        $title = trim($title);
        if ($title === '') return false;

        return $this->boardModel->createPost($userId, $title, (string)$content);
    }

    public function update(int $postId, int $userId, string $title, string $content)
    {
        $title = trim($title);
        if ($title === '') return false;

        return $this->boardModel->updatePost($postId, $userId, $title, (string)$content);
    }

    public function delete(int $postId, int $userId)
    {
        return $this->boardModel->deletePost($postId, $userId);
    }

    public function getEditData(int $postId, int $userId): array
    {
        $post = $this->boardModel->find($postId);

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
        $liked = $this->boardModel->toggleLike($postId, $userId);
        $likeCount = $this->boardModel->getLikeCount($postId);

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

        $ok = $this->boardModel->insertComment($postId, $userId, $content);
        if (!$ok) {
            return ['status' => 'error', 'message' => '댓글 등록 실패'];
        }

        // insert id 얻기 (insertComment에서 insert 후 insertID를 리턴하게 바꾸는게 베스트)
        $commentId = (int)$this->boardModel->db->insertID();

        return [
            'status' => 'success',
            'comment' => [
                'id'        => $commentId,
                'user_id'   => $userId,
                'name'      => $userName,
                'content'   => $content,
                'created_at'=> '방금', // created_at 컬럼 있으면 실제값 내려주는게 더 좋음
                'canDelete' => true,   // 이 요청은 본인이 쓴 거라 true
            ],
        ];
    }

    public function deleteComment(int $commentId, int $userId): array
    {
        if ($commentId <= 0) {
            return ['status' => 'error', 'message' => '잘못된 요청'];
        }

        $ok = $this->boardModel->deleteComment($commentId, $userId);

        if (!$ok) {
            return ['status' => 'error', 'message' => '삭제 권한이 없거나 댓글이 없습니다'];
        }

        return ['status' => 'success', 'commentId' => $commentId];
    }
}