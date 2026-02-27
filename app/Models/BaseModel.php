<?php
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

class BaseModel extends Model
{
    protected function t(string $table): BaseBuilder
    {
        return $this->db->table($table);
    }

    protected function applyWhere(BaseBuilder $b, array $where): BaseBuilder
    {
        foreach ($where as $k => $v) {
            $b->where($k, $v);
        }
        return $b;
    }

    /**
     * 데이터 다중 셀렉 (join 기반)
     */
    protected function selectAll(
        string $table,
        string $select = '*',
        array $where = [],
        ?array $orderBy = null,
        array $joins = []
    ): array {
        $b = $this->t($table)->select($select);

        foreach ($joins as $j) {
            // ['table' => 'dev_users', 'on' => 'dev_users.id = dev_comments.user_id', 'type' => 'left']
            $b->join($j['table'], $j['on'], $j['type'] ?? '');
        }

        if ($where) $this->applyWhere($b, $where);

        if ($orderBy) {
            // ['dev_comments.id', 'ASC']
            $b->orderBy($orderBy[0], $orderBy[1] ?? 'ASC');
        }

        return $b->get()->getResultArray();
    }

    /**
     * 데이터 1개 셀렉 (Join 기반)
     */
    protected function selectOne(
        string $table,
        string $select = '*',
        array $where = [],
        array $joins = []
    ): ?array {
        $b = $this->t($table)->select($select);

        foreach ($joins as $j) {
            $b->join($j['table'], $j['on'], $j['type'] ?? '');
        }

        if ($where) $this->applyWhere($b, $where);

        $row = $b->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * 데이터 1개 insert
     */
    protected function insertRow(string $table, array $data): bool
    {
        return (bool) $this->t($table)->insert($data);
    }

    /**
     * where 기반 update
     */
    protected function updateWhere(string $table, array $where, array $set): bool
    {
        $b = $this->t($table);
        $this->applyWhere($b, $where);
        return (bool) $b->update($set);
    }

    /**
     * where 기반 delete
     */
    protected function deleteWhere(string $table, array $where): bool
    {
        $b = $this->t($table);
        $this->applyWhere($b, $where);
        return (bool) $b->delete();
    }

    /**
     * where 기반 count
     */
    protected function countWhere(string $table, array $where = []): int
    {
        $b = $this->t($table);
        if ($where) $this->applyWhere($b, $where);
        return (int) $b->countAllResults();
    }

    /**
     * Exists row by where
     */
    protected function existsWhere(string $table, array $where): bool
    {
        return (bool) $this->selectOne($table, '1', $where);
    }

    /**
     * 카운트 증가 함수
     */
    protected function incrementWhere(string $table, array $where, string $field, int $step = 1): bool
    {
        $b = $this->t($table);
        $this->applyWhere($b, $where);

        // e.g. views = views + 1
        $b->set($field, "{$field}+{$step}", false);
        return (bool) $b->update();
    }
}