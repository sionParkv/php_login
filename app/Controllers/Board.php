<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\LikeModel;

class Board extends Controller
{
    /**
     * 로그인 여부 체크
     * 로그인 안 되어 있으면 /login으로 리다이렉트
     * 모든 게시판 기능 접근 전에 호출
     */
    private function requireLogin()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->send();
        }
        return null;
    }

    /**
     * 게시판 목록 페이지
     * - 게시글 리스트 조회
     * - 작성자 이름 JOIN
     * - 좋아요 개수 집계
     * - 검색 기능
     * - 페이지네이션 처리
     */
    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        $q = trim((string) $this->request->getGet('q')); // 검색어

        $postModel = new PostModel();

        // 좋아요 개수 집계 서브쿼리
        $likeSub = "(SELECT post_id, COUNT(*) AS like_count FROM dev_likes GROUP BY post_id) likes";

        $builder = $postModel
            ->select("dev_posts.*, dev_users.name, IFNULL(likes.like_count,0) AS like_count")
            ->join('dev_users', 'dev_users.id = dev_posts.user_id') // 작성자 이름 JOIN
            ->join($likeSub, 'likes.post_id = dev_posts.id', 'left') // 좋아요 LEFT JOIN
            ->orderBy('dev_posts.id', 'DESC'); // 최신순

        // 검색 기능 (제목, 내용, 작성자)
        if ($q !== '') {
            $builder->groupStart()
                ->like('dev_posts.title', $q)
                ->orLike('dev_posts.content', $q)
                ->orLike('dev_users.name', $q)
                ->groupEnd();
        }

        $data = [
            'q' => $q,
            'posts' => $builder->paginate(10), // 페이지네이션
            'pager' => $postModel->pager,
            'totalCount' => $postModel->countAllResults(false),
        ];

        return view('board/index', $data);
    }

    /**
     * 게시글 상세보기
     * - 조회수 +1 증가
     * - 게시글 정보 조회
     * - 좋아요 개수 조회
     * - 내가 좋아요 눌렀는지 확인
     * - 댓글 목록 조회
     */
    public function view($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $postModel = new PostModel();
        $commentModel = new CommentModel();
        $likeModel = new LikeModel();

        // 조회수 1 증가
        $postModel->set('views', 'views+1', false)->where('id', $id)->update();

        // 게시글 + 작성자 이름 조회
        $post = $postModel
            ->select('dev_posts.*, dev_users.name')
            ->join('dev_users', 'dev_users.id = dev_posts.user_id')
            ->where('dev_posts.id', $id)
            ->first();

        if (!$post) return "게시글이 없습니다.";

        // 좋아요 총 개수
        $likeCount = $likeModel->where('post_id', $id)->countAllResults();

        // 현재 로그인 사용자가 좋아요 눌렀는지 확인
        $liked = (bool) $likeModel
            ->where('post_id', $id)
            ->where('user_id', session()->get('user_id'))
            ->first();

        // 댓글 목록 조회
        $comments = $commentModel
            ->select('dev_comments.*, dev_users.name')
            ->join('dev_users', 'dev_users.id = dev_comments.user_id')
            ->where('post_id', $id)
            ->orderBy('dev_comments.id', 'ASC')
            ->findAll();

        return view('board/view', [
            'post' => $post,
            'comments' => $comments,
            'likeCount' => $likeCount,
            'liked' => $liked
        ]);
    }

    /**
     * 글쓰기 화면
     */
    public function write()
    {
        if ($r = $this->requireLogin()) return $r;
        return view('board/write');
    }

    /**
     * 글쓰기 처리 (게시글 DB INSERT)
     */
    public function writePost()
    {
        if ($r = $this->requireLogin()) return $r;

        $title = trim((string) $this->request->getPost('title'));
        $content = trim((string) $this->request->getPost('content'));

        if ($title === '')
            return redirect()->back()->with('error', '제목을 입력하세요');

        $postModel = new PostModel();

        $postModel->insert([
            'user_id' => session()->get('user_id'), // 작성자 ID 저장
            'title' => $title,
            'content' => $content,
        ]);

        return redirect()->to('/board');
    }

    /**
     * 수정 화면
     * - 게시글 존재 확인
     * - 본인 글인지 권한 체크
     */
    public function edit($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $postModel = new PostModel();
        $post = $postModel->find($id);

        if (!$post) return "게시글이 없습니다.";
        if ((int)$post['user_id'] !== (int)session()->get('user_id'))
            return "권한 없음";

        return view('board/edit', ['post' => $post]);
    }

    /**
     * 수정 처리 (UPDATE)
     */
    public function editPost($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $postModel = new PostModel();
        $post = $postModel->find($id);

        if (!$post) return "게시글이 없습니다.";
        if ((int)$post['user_id'] !== (int)session()->get('user_id'))
            return "권한 없음";

        $title = trim((string) $this->request->getPost('title'));
        $content = trim((string) $this->request->getPost('content'));

        if ($title === '')
            return redirect()->back()->with('error', '제목을 입력하세요');

        $postModel->update($id, [
            'title' => $title,
            'content' => $content
        ]);

        return redirect()->to('/board/view/'.$id);
    }

    /**
     * 게시글 삭제
     * - 본인 글만 삭제 가능
     */
    public function delete($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $postModel = new PostModel();
        $post = $postModel->find($id);

        if (!$post) return "게시글이 없습니다.";
        if ((int)$post['user_id'] !== (int)session()->get('user_id'))
            return "권한 없음";

        $postModel->delete($id);

        return redirect()->to('/board');
    }

    /**
     * 댓글 등록
     */
    public function commentWrite()
    {
        if ($r = $this->requireLogin()) return $r;

        $postId = (int)$this->request->getPost('post_id');
        $content = trim((string)$this->request->getPost('content'));

        if ($content === '') return redirect()->back();

        $commentModel = new CommentModel();

        $commentModel->insert([
            'post_id' => $postId,
            'user_id' => session()->get('user_id'),
            'content' => $content
        ]);

        return redirect()->to('/board/view/'.$postId);
    }

    /**
     * 좋아요 토글
     * - 이미 눌렀으면 삭제
     * - 없으면 INSERT
     */
    public function toggleLike($postId)
    {
        if ($r = $this->requireLogin()) return $r;

        $likeModel = new LikeModel();
        $userId = (int)session()->get('user_id');

        $exists = $likeModel
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->first();

        if ($exists) {
            $likeModel->delete($exists['id']); // 좋아요 취소
        } else {
            $likeModel->insert([
                'post_id' => $postId,
                'user_id' => $userId
            ]);
        }

        return redirect()->back();
    }
}