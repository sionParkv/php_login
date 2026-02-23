<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    // 회원가입 화면
    public function register()
    {
        return view('register');
    }

    // 회원가입 처리
    public function registerPost()
    {
        $model = new UserModel();

        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');
        $password = $this->request->getPost('password');

        if (!$email || !$name || !$password) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '값 누락'
            ]);
        }

        $exists_email = $model->where('email', $email)->first();
        $exists_name = $model->where('name', $name)->first();

        if ($exists_email) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '이미 존재하는 이메일'
            ]);
        }

        if ($exists_name) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '이미 존재하는 이름'
            ]);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $model->insert([
            'email' => $email,
            'name' => $name,
            'password' => $hashedPassword
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }

    public function login()
    {
        return view('login');
    }

    public function loginPost()
    {
        $model = new \App\Models\UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if (!$user) {
            return redirect()->to('/login')
                ->with('error', '존재하지 않는 이메일');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->to('/login')
                ->with('error', '비밀번호 불일치');
        }

        // ✅ 세션 저장
        session()->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'logged_in' => true
        ]);

        return redirect()->to('/board');
    }

    public function find()
    {
        return view('find');
    }
    // 로그아웃
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    /* 아이디 찾기 */
    public function findEmail()
    {
        $model = new \App\Models\UserModel();

        $name = $this->request->getPost('name');

        $user = $model->where('name', $name)->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '일치하는 사용자가 없습니다'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'email' => $user['email']
        ]);
    }

    /* 비밀번호 재설정 */
    public function resetPassword()
    {
        $model = new \App\Models\UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => '존재하지 않는 이메일'
            ]);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $model->update($user['id'], [
            'password' => $hashedPassword
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }

}
