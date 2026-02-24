<?php
namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table = 'dev_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'password', 'name'];

    // ✅ 조회 헬퍼들
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function findByName(string $name): ?array
    {
        return $this->where('name', $name)->first();
    }

    /**
     * ✅ 회원가입 저장 (트랜잭션 포함)
     * - 이메일/이름 중복 체크
     * - 통과 시 insert
     * - 결과: ['ok'=>bool, 'message'=>string|null]
     */
    public function registerUser(string $email, string $name, string $hashedPassword): array
    {
        $db = $this->db; // CI4 Model은 $this->db 사용 가능

        $db->transStart();

        // 중복 체크 (트랜잭션 안에서)
        $existsEmail = $this->where('email', $email)->first();
        if ($existsEmail) {
            $db->transRollback();
            return ['ok' => false, 'message' => '이미 존재하는 이메일'];
        }

        $existsName = $this->where('name', $name)->first();
        if ($existsName) {
            $db->transRollback();
            return ['ok' => false, 'message' => '이미 존재하는 이름'];
        }

        $this->insert([
            'email' => $email,
            'name' => $name,
            'password' => $hashedPassword
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['ok' => false, 'message' => '회원가입 처리 실패'];
        }

        return ['ok' => true, 'message' => null];
    }

    public function updatePasswordByUserId(int $userId, string $hashedPassword): bool
    {
        return (bool)$this->update($userId, ['password' => $hashedPassword]);
    }
}