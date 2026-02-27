<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AuthModel;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $service;

    public function __construct()
    {
        // 컨트롤러에서 서비스 생성 (CI4 DI를 안 쓰는 간단 버전)
        $this->service = new AuthService(new AuthModel());
    }

    // 회원가입 화면
    public function register()
    {
        return view('auth/register');
    }

    // 회원가입 처리
    public function register_post()
    {
        $res = $this->service->register(
            (string)$this->request->getPost('email'),
            (string)$this->request->getPost('name'),
            (string)$this->request->getPost('password')
        );

        return $this->response->setJSON($res);
    }

    // 로그인 화면
    public function login()
    {
        return view('auth/login');
    }

    // 로그인 처리
    public function login_post()
    {
        $result = $this->service->login(
            (string)$this->request->getPost('email'),
            (string)$this->request->getPost('password')
        );

        if (!$result['ok']) {
            return redirect()->to('auth/login')->with('error', $result['message']);
        }

        $user = $result['user'];

        session()->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'logged_in' => true
        ]);

        return redirect()->to('/board');
    }

    // 계정찾기 화면
    public function find()
    {
        return view('auth/find');
    }

    // 아이디(이메일) 찾기
    public function find_email()
    {
        $res = $this->service->findEmailByName((string)$this->request->getPost('name'));
        return $this->response->setJSON($res);
    }

    // 비밀번호 재설정
    public function reset_password()
    {
        $res = $this->service->resetPassword(
            (string)$this->request->getPost('email'),
            (string)$this->request->getPost('password')
        );

        return $this->response->setJSON($res);
    }

    // 로그아웃
    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }
}