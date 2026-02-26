<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\BoardModel;
use App\Services\BoardService;

class BoardController extends Controller
{
    private BoardService $service;

    public function __construct()
    {
        $this->service = new BoardService(new BoardModel());
    }

    private function requireLogin()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        return null;
    }

    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        $q = $this->request->getGet('q');

        $data = $this->service->getList($q);

        return view('board/index', array_merge($data, ['q' => $q]));
    }

    public function view($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $data = $this->service->getDetail($id, session()->get('user_id'));
        if (!$data) return "게시글이 없습니다.";

        return view('board/view', $data);
    }

    public function write()
    {
        if ($r = $this->requireLogin()) return $r;
        return view('board/write');
    }

    public function writePost()
    {
        if ($r = $this->requireLogin()) return $r;

        $this->service->create(
            session()->get('user_id'),
            $this->request->getPost('title'),
            $this->request->getPost('content')
        );

        return redirect()->to('/board');
    }

    public function edit($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $res = $this->service->getEditData((int)$id, (int)session()->get('user_id'));

        if (!$res['ok']) {
            return $res['message'];
        }

        return view('board/edit', ['post' => $res['post']]);
    }

    public function editPost($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $this->service->update(
            $id,
            session()->get('user_id'),
            $this->request->getPost('title'),
            $this->request->getPost('content')
        );

        return redirect()->to('/board/view/'.$id);
    }

    public function delete($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $this->service->delete($id, session()->get('user_id'));
        return redirect()->to('/board');
    }

    public function toggleLike()
    {
        if ($r = $this->requireLogin()) return $r;

        $postId = (int)$this->request->getPost('post_id');
        $userId = (int)session()->get('user_id');

        // ✅ 컨트롤러는 서비스 호출만
        $res = $this->service->toggleLike($postId, $userId);

        return $this->response->setJSON($res);
    }

    public function commentWrite()
    {
        if ($r = $this->requireLogin()) return $r;

        $postId = (int)$this->request->getPost('post_id');
        $userId = (int)session()->get('user_id');
        $userName = (string)session()->get('user_name');
        $content = (string)$this->request->getPost('content');

        // ✅ 컨트롤러는 서비스 호출만
        $res = $this->service->writeComment($postId, $userId, $userName, $content);

        return $this->response->setJSON($res);
    }

    public function commentDelete()
    {
        // AJAX 요청이므로 redirect 말고 JSON이 안전함
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => '로그인 필요']);
        }

        $commentId = (int)$this->request->getPost('comment_id');
        $userId = (int)session()->get('user_id');

        $res = $this->service->deleteComment($commentId, $userId);

        return $this->response->setJSON($res);
    }
}