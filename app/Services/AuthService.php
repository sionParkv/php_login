<?php
namespace App\Services;

use App\Models\AuthModel;

class AuthService
{
    public function __construct(private AuthModel $users)
    {
    }

    public function register(string $email, string $name, string $password): array
    {
        if (!$email || !$name || !$password) {
            return ['status' => 'error', 'message' => '값 누락'];
        }

        // 비밀번호 정책은 서비스에서 중앙집중 관리 추천
        // (프론트에서 검증하더라도 서버 검증은 필수)
        if (!$this->validPassword($password)) {
            return ['status' => 'error', 'message' => '비밀번호 규칙 불일치'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $result = $this->users->registerUser($email, $name, $hashed);
        if (!$result['ok']) {
            return ['status' => 'error', 'message' => $result['message']];
        }

        return ['status' => 'success'];
    }

    public function login(string $email, string $password): array
    {
        if (!$email || !$password) {
            return ['ok' => false, 'message' => '값 누락'];
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            return ['ok' => false, 'message' => '존재하지 않는 이메일'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['ok' => false, 'message' => '비밀번호 불일치'];
        }

        return ['ok' => true, 'user' => $user];
    }

    public function findEmailByName(string $name): array
    {
        if (!$name) {
            return ['status' => 'error', 'message' => '값 누락'];
        }

        $user = $this->users->findByName($name);
        if (!$user) {
            return ['status' => 'error', 'message' => '일치하는 사용자가 없습니다'];
        }

        return ['status' => 'success', 'email' => $user['email']];
    }

    public function resetPassword(string $email, string $password): array
    {
        if (!$email || !$password) {
            return ['status' => 'error', 'message' => '값 누락'];
        }

        if (!$this->validPassword($password)) {
            return ['status' => 'error', 'message' => '비밀번호 규칙 불일치'];
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            return ['status' => 'error', 'message' => '존재하지 않는 이메일'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $ok = $this->users->updatePasswordByUserId((int)$user['id'], $hashed);

        return $ok ? ['status' => 'success'] : ['status' => 'error', 'message' => '비밀번호 변경 실패'];
    }

    private function validPassword(string $pw): bool
    {
        // 8자 이상 + 특수문자 포함
        return (bool)preg_match('/^(?=.*[!@#$%^&*])(?=.{8,})/', $pw);
    }
}