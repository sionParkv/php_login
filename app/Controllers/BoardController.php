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

    public function commentWrite()
    {
        if ($r = $this->requireLogin()) return $r;

        $this->service->writeComment(
            (int)$this->request->getPost('post_id'),
            session()->get('user_id'),
            $this->request->getPost('content')
        );

        return redirect()->back();
    }

    public function toggleLike($postId)
    {
        if ($r = $this->requireLogin()) return $r;

        $this->service->toggleLike($postId, session()->get('user_id'));
        return redirect()->back();
    }
}